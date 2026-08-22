<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegalDocumentController extends Controller
{
    private const REGISTRATION_TERMS_FILE = 'Dieu_khoan_dang_ky_Online_FEA.pdf';

    public function registrationTerms(): BinaryFileResponse
    {
        $path = storage_path('app/legal/'.self::REGISTRATION_TERMS_FILE);

        abort_unless(is_file($path) && is_readable($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.self::REGISTRATION_TERMS_FILE.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
