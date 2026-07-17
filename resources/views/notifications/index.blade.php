@if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'instructor')
    @php
        $layout = auth()->user()->role === 'admin' ? 'admin-layout' : 'instructor-layout';
    @endphp

    <x-dynamic-component :component="$layout" title="Thông báo" page-title="Thông báo" breadcrumb="Cập nhật thông tin và hoạt động mới nhất">
        <div class="mx-auto max-w-4xl space-y-6">
            @include('notifications._content')
        </div>
    </x-dynamic-component>
@else
    @include('notifications._student')
@endif
