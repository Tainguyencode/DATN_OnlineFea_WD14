<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Notifications\CertificateIssuedNotification;
use App\Services\CertificatePdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CertificateController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        $certificates = Certificate::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('certificate_code')
            ->whereNotNull('issued_at')
            ->whereHas('course')
            ->with('course:id,title,slug,thumbnail')
            ->latest('issued_at')
            ->paginate(12)
            ->withQueryString();

        if ($request->boolean('send_email')) {
            $certificate = Certificate::query()
                ->where('user_id', $request->user()->id)
                ->whereKey($request->integer('certificate_id'))
                ->with('course')
                ->first();

            if (! $certificate) {
                return $this->emailResponse($request, false, 'Không tìm thấy chứng chỉ hợp lệ.');
            }

            try {
                app(CertificatePdfService::class)->ensureStored($certificate);
                $request->user()->notify(new CertificateIssuedNotification($certificate->course, $certificate->fresh()));

                return $this->emailResponse($request, true, 'Chứng chỉ đã được gửi tới email của bạn.');
            } catch (Throwable $exception) {
                Log::warning('Resend student certificate failed.', [
                    'certificate_id' => $certificate->id,
                    'message' => $exception->getMessage(),
                ]);

                return $this->emailResponse($request, false, 'Không thể gửi chứng chỉ. Vui lòng thử lại sau.');
            }
        }

        return view('student.dashboard.certificates.index', compact('certificates'));
    }

    public function pdf(Request $request, Certificate $certificate): BinaryFileResponse|\Illuminate\Http\Response
    {
        abort_unless((int) $certificate->user_id === (int) $request->user()->id, 403);
        abort_unless($certificate->certificate_code && $certificate->issued_at && $certificate->course()->exists(), 404);

        $certificate->load(['course', 'user']);
        $service = app(CertificatePdfService::class);
        $certificate = $service->ensureStored($certificate);
        $fileName = 'certificate-'.$certificate->certificate_code.'.pdf';

        if ($absolutePath = $service->absolutePath($certificate)) {
            return response()->file($absolutePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($request->boolean('download') ? 'attachment' : 'inline').'; filename="'.$fileName.'"',
            ]);
        }

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        return $request->boolean('download') ? $pdf->download($fileName) : $pdf->stream($fileName);
    }

    private function emailResponse(Request $request, bool $success, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(compact('success', 'message'), $success ? 200 : 422);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
