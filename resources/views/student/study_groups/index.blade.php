@if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'instructor')
    @php
        $layout = auth()->user()->role === 'admin' ? 'admin-layout' : 'instructor-layout';
    @endphp

    <x-dynamic-component :component="$layout" title="Nhóm học tập" page-title="Nhóm học tập" breadcrumb="Cùng nhau học tập và chia sẻ kiến thức">
        @include('student.study_groups._content')
    </x-dynamic-component>
@else
    @include('student.study_groups._student_index')
@endif
