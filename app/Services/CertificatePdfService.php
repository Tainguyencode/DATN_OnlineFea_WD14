<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CertificatePdfService
{
    public const DISK = 'local';

    public function store(Certificate $certificate): string
    {
        $certificate->loadMissing(['course', 'user']);

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        $path = sprintf(
            'certificates/%d/%s.pdf',
            $certificate->user_id,
            $certificate->certificate_code
        );

        Storage::disk(self::DISK)->put($path, $pdf->output());

        $certificate->update(['file_path' => $path]);

        return $path;
    }

    public function ensureStored(Certificate $certificate): Certificate
    {
        if ($certificate->file_path && Storage::disk(self::DISK)->exists($certificate->file_path)) {
            return $certificate;
        }

        try {
            $this->store($certificate);
            $certificate->refresh();
        } catch (Throwable $exception) {
            Log::error('Failed to store certificate PDF.', [
                'certificate_id' => $certificate->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        return $certificate;
    }

    public function absolutePath(Certificate $certificate): ?string
    {
        if (! $certificate->file_path || ! Storage::disk(self::DISK)->exists($certificate->file_path)) {
            return null;
        }

        return Storage::disk(self::DISK)->path($certificate->file_path);
    }
}
