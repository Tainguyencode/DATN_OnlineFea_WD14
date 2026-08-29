<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class PreviewLessonImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');
        if (! ($course instanceof Course)) {
            $course = Course::find($course);
        }

        $section = $this->route('section');
        if (! ($section instanceof CourseSection)) {
            $section = CourseSection::find($section);
        }

        return $course instanceof Course
            && $section instanceof CourseSection
            && $course->isOwnedBy($this->user())
            && (int) $section->course_id === (int) $course->id;
    }

    /**
     * @throws AuthorizationException
     */
    protected function failedAuthorization(): void
    {
        throw new AuthorizationException(
            'Phiên đăng nhập hiện tại không sở hữu khóa học hoặc chương đã chọn. Vui lòng tải lại trang sau khi đăng nhập đúng tài khoản giảng viên.'
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'extensions:xlsx',
                'mimes:xlsx',
                'max:5120',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file Excel để preview.',
            'file.file' => 'File tải lên không hợp lệ.',
            'file.extensions' => 'Chỉ hỗ trợ file có phần mở rộng .xlsx.',
            'file.mimes' => 'Nội dung file phải là workbook XLSX hợp lệ.',
            'file.max' => 'Dung lượng file Excel tối đa là 5MB.',
        ];
    }
}
