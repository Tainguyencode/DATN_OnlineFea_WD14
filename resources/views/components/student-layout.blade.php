@props([
    'title' => 'FEA Learning',
    'pageTitle' => null,
    'breadcrumb' => null,
    'backUrl' => null,
    'showBack' => true,
])

@include('student.dashboard.layouts.app', [
    'title' => $title,
    'pageTitle' => $pageTitle,
    'breadcrumb' => $breadcrumb,
    'backUrl' => $backUrl,
    'showBack' => $showBack,
    'slot' => $slot,
])

