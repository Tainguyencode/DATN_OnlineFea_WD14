@extends('layouts.quiz')

@section('title', $quizContext['title'] . ' - Quiz')

@section('content')
    <main class="min-h-screen">
        <x-learning.quiz-player :quiz-context="$quizContext" :lesson="$lesson" :standalone="true" />
    </main>
@endsection
