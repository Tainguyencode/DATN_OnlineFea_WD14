@props([
    'title' => 'FEA Learning',
    'pageTitle' => null,
    'breadcrumb' => null,
])

@include('student.dashboard.layouts.app', [
    'title' => $title,
    'pageTitle' => $pageTitle,
    'breadcrumb' => $breadcrumb,
    'slot' => $slot,
])
