<x-student-layout title="Yêu thích" page-title="Khóa học yêu thích">

@if($items->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center text-slate-500">Chưa có khóa học yêu thích.</div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
            <x-course-card :course="$item->course" />
        @endforeach
    </div>
@endif

</x-student-layout>
