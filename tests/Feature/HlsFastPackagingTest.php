<?php

namespace Tests\Feature;

use App\Services\HlsVideoService;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class HlsFastPackagingTest extends TestCase
{
    private string $work;

    protected function setUp(): void
    {
        parent::setUp();
        if (! is_file(base_path('bin/ffmpeg/ffmpeg.exe'))) {
            $this->markTestSkipped('Bundled FFmpeg is required for real media integration tests.');
        }
        $this->work = storage_path('app/hls-test-'.bin2hex(random_bytes(8)));
        File::ensureDirectoryExists($this->work);
        config(['video.ffmpeg.binary' => base_path('bin/ffmpeg/ffmpeg.exe'),
            'video.ffmpeg.probe_binary' => base_path('bin/ffmpeg/ffprobe.exe'),
            'video.ffmpeg.threads' => 2, 'video.hls.segment_seconds' => 2,
            'video.hls.fast_copy' => true]);
    }

    protected function tearDown(): void
    {
        if (isset($this->work) && str_starts_with($this->work, storage_path('app/hls-test-'))) {
            File::deleteDirectory($this->work);
        }
        parent::tearDown();
    }

    public function test_h264_aac_is_copied_and_output_decodes(): void
    {
        $this->convertAndDecode($this->fixture(), 'stream_copy');
    }

    public function test_h264_without_audio_is_supported(): void
    {
        $this->convertAndDecode($this->fixture(audio: false), 'stream_copy');
    }

    public function test_only_incompatible_audio_is_encoded(): void
    {
        $this->convertAndDecode($this->fixture(audioCodec: 'libmp3lame'), 'video_copy_audio_encode');
    }

    public function test_incompatible_video_uses_cpu(): void
    {
        $this->convertAndDecode($this->fixture(codec: 'mpeg4'), 'cpu_encode');
    }

    public function test_long_gop_falls_back_and_cleans_partial_output(): void
    {
        $this->convertAndDecode($this->fixture(gop: 250), 'cpu_encode');
    }

    public function test_fast_path_can_be_disabled(): void
    {
        config(['video.hls.fast_copy' => false]);
        $this->convertAndDecode($this->fixture(), 'cpu_encode');
    }

    public function test_compare_full_conversion_time_for_the_same_source(): void
    {
        $source = $this->fixture(duration: 30, size: '1280x720');
        $timings = [];
        foreach ([false, true] as $fast) {
            config(['video.hls.fast_copy' => $fast]);
            $started = microtime(true);
            $result = app(HlsVideoService::class)->transcode($source, $this->work.'/bench-'.(int) $fast);
            $timings[$result['mode']] = round(microtime(true) - $started, 3);
            $this->assertSame($fast ? 'stream_copy' : 'cpu_encode', $result['mode']);
        }
        fwrite(STDOUT, PHP_EOL.'HLS benchmark (30s 1280x720, includes probe; no S3): '.json_encode($timings).PHP_EOL);
    }

    private function fixture(string $codec = 'libx264', string $audioCodec = 'aac', bool $audio = true, int $gop = 25, int $duration = 8, string $size = '640x360'): string
    {
        $path = $this->work.'/source.mp4';
        $command = [base_path('bin/ffmpeg/ffmpeg.exe'), '-v', 'error', '-y',
            '-f', 'lavfi', '-i', 'testsrc2=size='.$size.':rate=25'];
        if ($audio) {
            array_push($command, '-f', 'lavfi', '-i', 'sine=frequency=440:sample_rate=44100');
        }
        array_push($command, '-t', (string) $duration, '-c:v', $codec, '-pix_fmt', 'yuv420p', '-g', (string) $gop);
        if ($codec === 'libx264') {
            array_push($command, '-preset', 'ultrafast', '-sc_threshold', '0');
        }
        if ($audio) {
            array_push($command, '-c:a', $audioCodec);
        }
        $command[] = $path;
        (new Process($command))->setTimeout(120)->mustRun();

        return $path;
    }

    private function convertAndDecode(string $source, string $expectedMode): void
    {
        $output = $this->work.'/output';
        $result = app(HlsVideoService::class)->transcode($source, $output);
        $this->assertSame($expectedMode, $result['mode']);
        $this->assertSame(8, $result['duration_seconds']);
        $this->assertGreaterThanOrEqual(3, $result['segment_count']);
        $this->assertCount($result['segment_count'], File::glob($output.'/segment_*.ts'));
        $decode = new Process([base_path('bin/ffmpeg/ffmpeg.exe'), '-v', 'error', '-xerror',
            '-i', $output.'/playlist.m3u8', '-f', 'null', '-']);
        $decode->setTimeout(120)->mustRun();
        $this->assertSame('', trim($decode->getErrorOutput()));
    }
}
