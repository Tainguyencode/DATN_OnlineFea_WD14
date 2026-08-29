@extends('layouts.app')

@section('title', 'Thông báo - FEA Learning')

@section('content')
<div class="ui-container py-8 sm:py-12">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('student.dashboard') }}'; }" class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-400 cursor-pointer transition py-1">
                ← Quay lại
            </button>
        </div>
        @include('notifications._content')
    </div>
</div>
@endsection
