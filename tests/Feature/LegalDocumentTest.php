<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalDocumentTest extends TestCase
{
    public function test_student_registration_uses_the_registration_terms_pdf(): void
    {
        $this->get(route('register.role', 'student'))
            ->assertOk()
            ->assertSee('data-student-terms-trigger', false)
            ->assertSee('data-student-terms-modal', false)
            ->assertSee('data-student-terms-pdf', false)
            ->assertSee('data-student-terms-open-pdf', false)
            ->assertSee(route('legal.registration-terms'), false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);

        $this->get(route('register.role', 'instructor'))
            ->assertOk()
            ->assertDontSee('data-student-terms-modal', false)
            ->assertDontSee('data-student-terms-pdf', false);
    }

    public function test_registration_terms_pdf_is_available_to_guests(): void
    {
        $response = $this->get(route('legal.registration-terms'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringStartsWith(
            'inline;',
            (string) $response->headers->get('Content-Disposition')
        );
        $this->assertSame(
            realpath(storage_path('app/legal/Dieu_khoan_dang_ky_Online_FEA.pdf')),
            $response->baseResponse->getFile()->getRealPath()
        );
    }
}
