<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Course $course */
        $course = $this->route('course');

        return $course->isOwnedBy($this->user());
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('lesson')) {
            $this->errorBag = 'updateLesson_'.$this->route('lesson')->id;
        } elseif ($this->route('section')) {
            $this->errorBag = 'storeLesson_'.$this->route('section')->id;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $lessonTypes = ['video', 'document', 'quiz', 'assignment'];
        $lessonStatuses = ['draft', 'published'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in($lessonTypes)],
            'video_file' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:204800', 'prohibited_unless:type,video'],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'max:10240'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_preview' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'status' => ['nullable', Rule::in($lessonStatuses)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên bài học.',
            'title.string' => 'Tên bài học phải là chuỗi ký tự.',
            'title.max' => 'Tên bài học không được vượt quá :max ký tự.',

            'type.required' => 'Vui lòng chọn loại bài học.',
            'type.in' => 'Loại bài học không hợp lệ. Chọn: Video, Tài liệu, Quiz hoặc Bài tập.',

            'video_file.file' => 'Video bài giảng phải là một file.',
            'video_file.mimes' => 'Video bài giảng chỉ cho phép định dạng MP4, MOV, AVI hoặc WEBM.',
            'video_file.max' => 'Dung lượng video bài giảng tối đa là 200MB.',
            'video_file.prohibited_unless' => 'Chỉ upload video khi loại bài học là Video.',

            'video_url.string' => 'Link video phải là chuỗi ký tự.',
            'video_url.max' => 'Link video không được vượt quá :max ký tự.',

            'content.string' => 'Nội dung bài học phải là chuỗi ký tự.',

            'document_file.file' => 'Tệp tài liệu phải là một file.',
            'document_file.max' => 'Dung lượng tệp tài liệu tối đa là 10MB.',

            'duration.integer' => 'Thời lượng phải là số nguyên.',
            'duration.min' => 'Thời lượng không được nhỏ hơn :min.',
            'duration.max' => 'Thời lượng không được vượt quá :max giây.',

            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên.',
            'sort_order.min' => 'Thứ tự sắp xếp không được nhỏ hơn :min.',
            'sort_order.max' => 'Thứ tự sắp xếp không được vượt quá :max.',

            'status.in' => 'Trạng thái bài học không hợp lệ.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Tên bài học',
            'type' => 'Loại bài học',
            'video_file' => 'Video bài giảng',
            'video_url' => 'Video URL',
            'content' => 'Nội dung',
            'document_file' => 'Tệp tài liệu',
            'duration' => 'Thời lượng',
            'is_preview' => 'Bài xem thử',
            'sort_order' => 'Thứ tự',
            'status' => 'Trạng thái',
        ];
    }
}
