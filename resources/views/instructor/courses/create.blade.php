<x-instructor-layout title="Tß║ío kh├│a hß╗ìc" page-title="Tß║ío kh├│a hß╗ìc" breadcrumb="L╞░u bß║ún nh├íp ─æß║ºu ti├¬n tr╞░ß╗¢c khi x├óy dß╗▒ng nß╗Öi dung">

<div class="mx-auto max-w-5xl">
    @include('instructor.courses._form', [
        'course' => null,
        'categories' => $categories,
        'action' => route('instructor.courses.store'),
        'method' => 'POST',
        'submitLabel' => 'L╞░u nh├íp',
    ])
</div>

</x-instructor-layout>
