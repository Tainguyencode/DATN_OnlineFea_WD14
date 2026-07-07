@extends('layouts.app')

@section('title', 'Đăng ký - Website học online FEA')

@section('content')
<div class="bg-white dark:bg-slate-950">
    <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-5 py-10 sm:py-14">
        <div class="mx-auto w-full max-w-2xl">
            <x-auth.card>
                <x-auth.header
                    title="Đăng ký tài khoản"
                    subtitle="Chọn loại tài khoản phù hợp với mục tiêu của bạn."
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('register.role', 'student') }}"
                       class="group flex flex-col rounded-xl border border-slate-200 bg-slate-50 p-5 transition duration-200 hover:border-[#0056D2] hover:bg-blue-50/60 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-blue-400 dark:hover:bg-slate-800">
                        <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#0056D2]/10 text-[#0056D2] dark:bg-blue-500/10 dark:text-blue-300">
                            <svg class="h-6 w-6 text-current stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">Học viên</span>
                        <span class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Đăng ký để tham gia khóa học, theo dõi tiến độ và nhận chứng chỉ.</span>
                        <span class="mt-4 text-sm font-semibold text-[#0056D2] dark:text-blue-300">Đăng ký học viên →</span>
                    </a>

                    <a href="{{ route('register.role', 'instructor') }}"
                       class="group flex flex-col rounded-xl border border-slate-200 bg-slate-50 p-5 transition duration-200 hover:border-[#0056D2] hover:bg-blue-50/60 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-blue-400 dark:hover:bg-slate-800">
                        <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300">
                            <svg class="h-6 w-6 text-current stroke-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                        </span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">Giảng viên</span>
                        <span class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Tạo và quản lý khóa học, chia sẻ kiến thức với cộng đồng học viên.</span>
                        <span class="mt-4 text-sm font-semibold text-[#0056D2] dark:text-blue-300">Đăng ký giảng viên →</span>
                    </a>
                </div>

                <x-auth.footer-link
                    text="Đã có tài khoản?"
                    link-text="Đăng nhập"
                    :href="route('login')"
                />
            </x-auth.card>
        </div>
    </div>
</div>
@endsection
