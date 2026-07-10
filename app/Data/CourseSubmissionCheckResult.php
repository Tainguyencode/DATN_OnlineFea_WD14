<?php

namespace App\Data;

class CourseSubmissionCheckResult
{
    /**
     * @param  array<int, array{key: string, label: string, passed: bool, message: string|null}>  $items
     */
    public function __construct(
        private readonly array $items,
    ) {}

    public function passes(): bool
    {
        return $this->failures() === [];
    }

    /**
     * @return array<int, array{key: string, label: string, passed: bool, message: string|null}>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, array{key: string, label: string, passed: bool, message: string|null}>
     */
    public function failures(): array
    {
        return array_values(array_filter(
            $this->items,
            fn (array $item) => ! $item['passed'],
        ));
    }

    /**
     * @return array<int, string>
     */
    public function errorMessages(): array
    {
        return array_values(array_filter(array_map(
            fn (array $item) => $item['message'],
            $this->failures(),
        )));
    }

    public function summaryMessage(): string
    {
        $messages = $this->errorMessages();

        if ($messages === []) {
            return 'Khóa học đã đủ điều kiện gửi duyệt.';
        }

        return 'Chưa thể gửi duyệt: '.implode('; ', $messages).'.';
    }
}
