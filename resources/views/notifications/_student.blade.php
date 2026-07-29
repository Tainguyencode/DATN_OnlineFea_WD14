@extends('layouts.app')

@section('title', 'Thông báo - FEA Learning')

@section('content')
<div class="ui-container py-8 sm:py-12">
    <div class="mx-auto max-w-4xl space-y-6">
        @include('notifications._content')
    </div>
</div>
@endsection
