<x-instructor-layout title="So sánh phiên bản" page-title="So sánh phiên bản" :breadcrumb="$course->title">
    @include('content-versions.partials.compare', ['isAdmin' => false])
</x-instructor-layout>
