<div data-course-grid class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach($courseItems as $course)
        <div data-course-card data-course-id="{{ $course->id }}" class="min-w-0">
            <x-course-card :course="$course" :favorited="(bool) ($course->is_favorited ?? false)" />
        </div>
    @endforeach
</div>
