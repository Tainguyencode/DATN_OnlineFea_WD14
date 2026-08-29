<x-student-layout title="Khóa học yêu thích" page-title="Khóa học yêu thích">
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Khóa học yêu thích</h2>
        <p class="mt-1 max-w-2xl text-sm text-slate-500">Lưu những khóa học bạn quan tâm để xem lại hoặc đăng ký sau.</p>
    </div>
    <p class="text-sm font-semibold text-slate-500">{{ $items->total() }} khóa học yêu thích</p>
</div>

@if($items->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-950">Bạn chưa có khóa học yêu thích</h2>
        <p class="mt-2 text-sm text-slate-500">Khám phá và lưu những khóa học bạn quan tâm để xem lại sau.</p>
        <a href="{{ route('courses.index') }}" class="mt-5 inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white transition hover:bg-indigo-700">
            Khám phá khóa học
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($items as $item)
            <x-course-card
                :course="$item->course"
                :favorited="true"
                :show-actions="true"
                :cart-course-ids="$cartCourseIds"
                :enrolled-course-ids="$enrolledCourseIds"
            />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
</x-student-layout>
