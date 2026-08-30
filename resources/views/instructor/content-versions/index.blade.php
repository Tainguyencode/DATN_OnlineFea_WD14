<x-instructor-layout title="Lịch sử phiên bản" page-title="Lịch sử phiên bản" :breadcrumb="$course->title">
    @include('content-versions.partials.index', ['isAdmin' => false])
</x-instructor-layout>
