<x-instructor-layout title="Chi tiết phiên bản" page-title="Chi tiết phiên bản" :breadcrumb="$course->title">
    @include('content-versions.partials.show', ['isAdmin' => false])
</x-instructor-layout>
