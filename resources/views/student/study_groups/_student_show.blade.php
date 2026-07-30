@extends('layouts.app')

@section('title', 'Nhóm học tập: ' . $studyGroup->name)

@section('content')
    @include('student.study_groups._show_content')
@endsection
