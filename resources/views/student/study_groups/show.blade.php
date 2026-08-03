@if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'instructor')
    @php
        $layout = auth()->user()->role === 'admin' ? 'admin-layout' : 'instructor-layout';
    @endphp

    <x-dynamic-component :component="$layout" :title="'Nhóm học tập: ' . $studyGroup->name" :page-title="'Nhóm học tập: ' . $studyGroup->name" breadcrumb="Thảo luận trực tuyến với các thành viên khác">
        @include('student.study_groups._show_content')
    </x-dynamic-component>
@else
    @include('student.study_groups._student_show')
@endif
