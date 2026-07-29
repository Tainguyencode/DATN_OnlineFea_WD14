<?php

namespace Tests;

use App\Http\Middleware\SingleSessionMiddleware;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(SingleSessionMiddleware::class);
        $this->ensureViteManifestExists();
    }

    protected function ensureViteManifestExists(): void
    {
        $manifestPath = public_path('build/manifest.json');

        if (file_exists($manifestPath)) {
            return;
        }

        $entries = [
            'resources/css/app.css' => [
                'file' => 'assets/app.css',
                'src' => 'resources/css/app.css',
                'isEntry' => true,
            ],
            'resources/css/welcome.css' => [
                'file' => 'assets/welcome.css',
                'src' => 'resources/css/welcome.css',
                'isEntry' => true,
            ],
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js',
                'isEntry' => true,
            ],
            'resources/js/learning-player.js' => [
                'file' => 'assets/learning-player.js',
                'src' => 'resources/js/learning-player.js',
                'isEntry' => true,
            ],
        ];

        if (! is_dir(dirname($manifestPath))) {
            mkdir(dirname($manifestPath), 0777, true);
        }

        file_put_contents($manifestPath, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
