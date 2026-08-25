@props([
    'learningPath' => null,
])

@php
    $learningPathId = $learningPath?->id ?? null;
@endphp

<div
    x-data="learningPathAiChat({
        learningPathId: {{ $learningPathId ? (int)$learningPathId : 'null' }},
        chatUrl: '{{ route('learning-paths.ai.chat') }}',
        conversationUrl: '{{ route('learning-paths.ai.conversation') }}',
        resetUrl: '{{ route('learning-paths.ai.reset') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    x-effect="document.body.style.overflow = isOpen ? 'hidden' : ''"
    x-cloak
    class="relative font-sans"
    style="z-index: 9999;"
>
    {{-- Floating Action Button --}}
    <button
        type="button"
        @click="toggleChat()"
        class="fixed bottom-6 right-6 z-40 flex items-center gap-2.5 rounded-full bg-gradient-to-r from-[#0056D2] to-[#0046B8] px-4 py-3.5 text-white shadow-xl shadow-blue-500/25 transition-all duration-300 hover:scale-105 hover:shadow-blue-500/40 focus:outline-none"
        style="z-index: 9990;"
        aria-label="Mở AI Tư vấn Lộ trình"
    >
        <span class="relative flex h-3 w-3">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-3 w-3 rounded-full bg-emerald-400"></span>
        </span>
        <span class="text-xs font-black tracking-wide sm:text-sm">AI Tư Vấn Lộ Trình</span>
    </button>

    {{-- Centered Modal Wrapper with Soft Backdrop --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 md:p-6"
        style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem;"
    >
        {{-- Soft Backdrop Overlay (Click outside to close) --}}
        <div
            @click="isOpen = false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
            style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
        ></div>

        {{-- Main Centered Panel (Exact Match to Image 2) --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            @click.stop
            class="relative z-10 flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            style="position: relative; z-index: 10; display: flex; flex-direction: column; width: 100%; max-width: 840px; height: 86vh; max-height: 820px; border-radius: 1.5rem; overflow: hidden; background-color: #ffffff; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);"
        >
            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800" style="flex-shrink: 0;">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" style="font-size: 1rem; font-weight: 700;">AI Tư Vấn Lộ Trình</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 0.75rem; color: #64748b;">Cho tôi biết mục tiêu và trình độ hiện tại, tôi sẽ giúp bạn xây dựng một lộ trình học phù hợp.</p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Reset / New Chat Button --}}
                    <button
                        type="button"
                        @click="resetChat()"
                        :disabled="isLoading"
                        class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-[#0056D2] hover:bg-blue-50/50 hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Đoạn chat mới</span>
                    </button>
                    {{-- Close Button --}}
                    <button
                        type="button"
                        @click="isOpen = false"
                        class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Current Learning Path Banner if active --}}
            <template x-if="currentLearningPath">
                <div class="shrink-0 border-b border-sky-100 bg-sky-50/80 px-6 py-2 text-xs text-sky-900 dark:border-sky-900/30 dark:bg-sky-950/40 dark:text-sky-300">
                    <span>Đang xem Lộ trình: </span>
                    <span class="font-bold" x-text="currentLearningPath.title"></span>
                </div>
            </template>

            {{-- Chat Messages Area (Scrollable with min-h-0 and overscroll-contain) --}}
            <div
                id="lp-chat-messages-container"
                class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-4"
                style="flex: 1 1 0%; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;"
            >
                {{-- Welcome bubble --}}
                <div class="flex justify-start">
                    <div class="w-full rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-xs leading-relaxed text-slate-700 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-200" style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 1rem;">
                        <p class="font-bold text-slate-900 dark:text-white">👋 Xin chào! Tôi là AI Tư vấn Lộ trình học tập FEA.</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">
                            Cho tôi biết mục tiêu và trình độ hiện tại, tôi sẽ giúp bạn xây dựng một lộ trình học phù hợp.
                        </p>
                    </div>
                </div>

                {{-- Quick Survey Panel for Quick Setup (Exact match to Image 2) --}}
                <div x-show="showSurvey && messages.length <= 1" class="rounded-2xl border border-blue-100/80 bg-blue-50/40 p-5 dark:border-blue-900/40 dark:bg-blue-950/20" style="background-color: #eff6ff80; border: 1px solid #dbeafe; border-radius: 1rem;">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-[#0056D2] dark:text-blue-300">Khảo sát thông tin người học</h4>
                        <button type="button" @click="showSurvey = false" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">Ẩn form</button>
                    </div>
                    <div class="mt-3.5 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Ngành / Vị trí mục tiêu (*)</label>
                            <input type="text" x-model="survey.field" placeholder="Ví dụ: Backend Developer, Fullstack Web, Data Analyst..." class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Trình độ hiện tại</label>
                                <select x-model="survey.level" class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-800 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    <option value="Beginner">Hoàn toàn mới (Beginner)</option>
                                    <option value="Knows Basics">Đã biết cơ bản</option>
                                    <option value="Intermediate">Trung cấp</option>
                                    <option value="Advanced">Nâng cao</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Mục tiêu chính</label>
                                <select x-model="survey.goal" class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-800 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    <option value="Tìm việc làm">Tìm việc làm</option>
                                    <option value="Chuyển ngành">Chuyển ngành</option>
                                    <option value="Nâng cao chuyên môn">Nâng cao chuyên môn</option>
                                    <option value="Làm dự án thực tế">Làm dự án thực tế</option>
                                    <option value="Thi chứng chỉ">Thi chứng chỉ</option>
                                    <option value="Học vì sở thích">Học vì sở thích</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Thời gian học dự kiến</label>
                            <input type="text" x-model="survey.duration" placeholder="Ví dụ: 1 giờ/ngày, 6 tháng..." class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-[#0056D2] focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                        <button
                            type="button"
                            @click="submitSurvey()"
                            :disabled="isLoading"
                            class="mt-2 flex h-11 w-full items-center justify-center rounded-xl bg-[#0056D2] text-xs font-bold text-white shadow-sm transition hover:bg-[#0046B8] disabled:opacity-50"
                            style="background-color: #0056D2; height: 44px; border-radius: 0.75rem;"
                        >
                            Xây dựng Lộ trình ngay
                        </button>
                    </div>
                </div>

                {{-- Message Loop --}}
                <template x-for="(msg, msgIdx) in messages" :key="msg.id || msg.tempId">
                    <div class="space-y-3">
                        {{-- User Bubble --}}
                        <template x-if="msg.role === 'user'">
                            <div class="flex justify-end">
                                <div class="max-w-[85%] rounded-2xl rounded-tr-xs bg-[#0056D2] px-4 py-2.5 text-xs leading-relaxed text-white shadow-xs" style="background-color: #0056D2; color: #ffffff;" x-text="msg.content"></div>
                            </div>
                        </template>

                        {{-- Assistant Bubble --}}
                        <template x-if="msg.role === 'assistant'">
                            <div class="flex flex-col items-start gap-3">
                                {{-- Text Message --}}
                                <div class="w-full rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-xs leading-relaxed text-slate-800 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-200">
                                    <div class="whitespace-pre-line" x-text="msg.content"></div>

                                    {{-- Topic switch confirmation prompt --}}
                                    <template x-if="msg.metadata && msg.metadata.type === 'topic_switch_confirmation'">
                                        <div class="mt-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                                            <template x-if="msg.metadata.status === 'pending' || !msg.metadata.status">
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        @click="confirmTopicSwitch(msg, msg.metadata.new_topic)"
                                                        :disabled="isLoading"
                                                        class="rounded-xl bg-[#0056D2] px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-[#0046B8] disabled:opacity-50"
                                                    >
                                                        ✅ Tạo lộ trình mới
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="dismissTopicSwitch(msg)"
                                                        :disabled="isLoading"
                                                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                                    >
                                                        Giữ lộ trình cũ
                                                    </button>
                                                </div>
                                            </template>

                                            <template x-if="msg.metadata.status === 'resolved'">
                                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓ Đã đồng ý tạo lộ trình mới.</span>
                                            </template>
                                            <template x-if="msg.metadata.status === 'dismissed'">
                                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">✓ Đã giữ nguyên lộ trình cũ.</span>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                {{-- Render Structured Roadmap (PHẦN A TOÀN BỘ TRƯỚC -> PHẦN B SAU CÙNG) --}}
                                <template x-if="msg.metadata && msg.metadata.type === 'learning_roadmap'">
                                    <div class="w-full space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                                        {{-- ================= PHẦN A: ROADMAP ĐƯỢC AI ĐỀ XUẤT ================= --}}
                                        <div class="border-b border-slate-100 pb-3.5 dark:border-slate-800">
                                            <div class="flex items-center gap-2.5">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-bold text-sm">🗺️</span>
                                                <div>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">PHẦN A — LỘ TRÌNH DO AI ĐỀ XUẤT</span>
                                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white" x-text="msg.metadata.roadmap.title"></h4>
                                                </div>
                                            </div>
                                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 text-xs text-slate-600 dark:text-slate-400">
                                                <div><span class="font-bold text-slate-700 dark:text-slate-300">🎯 Mục tiêu:</span> <span x-text="msg.metadata.roadmap.goal || 'Phát triển chuyên môn'"></span></div>
                                                <div><span class="font-bold text-slate-700 dark:text-slate-300">⏳ Thời gian dự kiến:</span> <span x-text="msg.metadata.roadmap.estimated_duration || '4 - 6 tháng'"></span></div>
                                                <div><span class="font-bold text-slate-700 dark:text-slate-300">📊 Trình độ:</span> <span x-text="msg.metadata.roadmap.current_level || 'Người mới bắt đầu'"></span></div>
                                                <div><span class="font-bold text-slate-700 dark:text-slate-300">💼 Vị trí mục tiêu:</span> <span x-text="msg.metadata.roadmap.target_role || 'Chuyên viên'"></span></div>
                                            </div>
                                            <template x-if="msg.metadata.roadmap.overview">
                                                <div class="mt-2.5 rounded-xl bg-slate-50 p-2.5 text-xs text-slate-600 dark:bg-slate-800/60 dark:text-slate-300" x-text="msg.metadata.roadmap.overview"></div>
                                            </template>
                                        </div>

                                        {{-- Danh sách chi tiết TẤT CẢ các Giai đoạn (Stages) --}}
                                        <div class="space-y-4">
                                            <template x-for="(stg, stgIdx) in (msg.metadata.roadmap.stages || [])" :key="stgIdx">
                                                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                                                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2.5 dark:border-slate-700/60">
                                                        <div>
                                                            <span class="inline-block rounded-md bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-[#0056D2] dark:bg-blue-950 dark:text-blue-300" x-text="'GIAI ĐOẠN ' + (stg.stage || (stgIdx + 1))"></span>
                                                            <h5 class="mt-1 text-xs font-bold text-slate-900 dark:text-white" x-text="stg.title"></h5>
                                                        </div>
                                                        <span class="text-xs font-semibold text-slate-500 shrink-0" x-text="stg.duration || ''"></span>
                                                    </div>

                                                    {{-- Mục tiêu giai đoạn --}}
                                                    <template x-if="stg.objective">
                                                        <div class="mt-2.5 text-xs text-slate-700 dark:text-slate-300">
                                                            <span class="font-bold text-slate-900 dark:text-white">🎯 Mục tiêu:</span>
                                                            <span x-text="stg.objective"></span>
                                                        </div>
                                                    </template>

                                                    {{-- Nội dung chi tiết cần học --}}
                                                    <template x-if="stg.topics_to_learn && stg.topics_to_learn.length > 0">
                                                        <div class="mt-2.5">
                                                            <span class="text-[11px] font-bold text-slate-900 dark:text-white">📚 Nội dung cần học:</span>
                                                            <ul class="mt-1 space-y-1 pl-3 text-xs text-slate-600 dark:text-slate-300 list-disc list-inside">
                                                                <template x-for="(topic, tIdx) in stg.topics_to_learn" :key="tIdx">
                                                                    <li x-text="topic"></li>
                                                                </template>
                                                            </ul>
                                                        </div>
                                                    </template>

                                                    {{-- Kỹ năng cần đạt --}}
                                                    <template x-if="stg.skills && stg.skills.length > 0">
                                                        <div class="mt-2.5">
                                                            <span class="text-[11px] font-bold text-slate-900 dark:text-white">🛠️ Kỹ năng cần đạt:</span>
                                                            <div class="mt-1 flex flex-wrap gap-1.5">
                                                                <template x-for="sk in stg.skills" :key="sk">
                                                                    <span class="rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-medium text-slate-700 shadow-2xs dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200" x-text="sk"></span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Thực hành & Dự án --}}
                                                    <template x-if="stg.practice">
                                                        <div class="mt-2.5 text-xs text-slate-700 dark:text-slate-300">
                                                            <span class="font-bold text-slate-900 dark:text-white">💻 Thực hành:</span>
                                                            <span x-text="stg.practice"></span>
                                                        </div>
                                                    </template>

                                                    {{-- Điều kiện chuyển tiếp --}}
                                                    <template x-if="stg.transition_criteria">
                                                        <div class="mt-2 text-xs text-slate-600 dark:text-slate-400 italic">
                                                            <span class="font-bold text-slate-700 dark:text-slate-300 not-italic">🏁 Điều kiện hoàn thành:</span>
                                                            <span x-text="stg.transition_criteria"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Lời khuyên cuối roadmap --}}
                                        <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-3 text-xs text-blue-900 dark:border-blue-900/40 dark:bg-blue-950/30 dark:text-blue-200">
                                            📌 <span class="font-bold">Lời khuyên:</span> Bạn nên hoàn thành tuần tự từng giai đoạn trên để nắm vững kiến thức một cách bài bản nhất.
                                        </div>

                                        {{-- ================= PHẦN B: CÁC KHÓA HỌC PHÙ HỢP TRÊN FEA (RIÊNG BIỆT Ở CUỐI) ================= --}}
                                        <div class="mt-5 border-t border-slate-200 pt-4 dark:border-slate-800">
                                            <div class="flex items-center gap-2">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-100 text-[#0056D2] font-bold text-sm">🎓</span>
                                                <div>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#0056D2] dark:text-sky-400">PHẦN B — CÁC KHÓA HỌC PHÙ HỢP TRÊN FEA</span>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">Đối chiếu với hệ thống khóa học hiện có trên FEA:</p>
                                                </div>
                                            </div>

                                            {{-- Case 1: FEA CÓ khóa học phù hợp theo từng giai đoạn --}}
                                            <template x-if="msg.metadata.matched_courses && msg.metadata.matched_courses.has_any_matched_courses">
                                                <div class="mt-3 space-y-3">
                                                    <template x-for="stgMatch in (msg.metadata.matched_courses.stages || [])" :key="stgMatch.stage">
                                                        <div>
                                                            <template x-if="hasCourseInStageMatches(stgMatch.matches)">
                                                                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-800 dark:bg-slate-800/40">
                                                                    <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300" x-text="'Khóa học cho Giai đoạn ' + stgMatch.stage + ' (' + stgMatch.title + '):'"></div>
                                                                    <div class="mt-2 space-y-2">
                                                                        <template x-for="c in getCoursesOnly(stgMatch.matches)" :key="c.course_id">
                                                                            <a :href="c.url" target="_blank" class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-2.5 transition hover:border-[#0056D2] hover:bg-blue-50/30 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-blue-500">
                                                                                <div class="flex items-center gap-2.5 min-w-0">
                                                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[#0056D2] font-black text-sm">
                                                                                        📖
                                                                                    </div>
                                                                                    <div class="min-w-0 flex-1">
                                                                                        <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="c.title"></div>
                                                                                        <div class="text-[10px] text-slate-500">GV: <span x-text="c.instructor_name"></span> • <span x-text="c.lessons_count + ' bài học'"></span></div>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="shrink-0 text-xs font-bold text-[#0056D2]" x-text="c.formatted_price"></span>
                                                                            </a>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- Case 2: FEA KHÔNG CÓ khóa học phù hợp (e.g. Kinh doanh khi chỉ có HTML) --}}
                                            <template x-if="!msg.metadata.matched_courses || !msg.metadata.matched_courses.has_any_matched_courses">
                                                <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50/60 p-4 text-xs leading-relaxed text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                                                    <p class="font-bold">⚠️ Hiện FEA chưa có khóa học phù hợp trực tiếp với lộ trình này.</p>
                                                    <p class="mt-1 text-amber-800 dark:text-amber-300">
                                                        Bạn hoàn toàn có thể theo cấu trúc và các chuyên đề chi tiết trong lộ trình AI đề xuất ở Phần A để tự học và nghiên cứu.
                                                    </p>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Banner Liên kết Lộ trình bài bản trên FEA nếu có cùng ngành --}}
                                        <template x-if="msg.metadata.related_learning_path">
                                            <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50/80 p-3 text-xs dark:border-emerald-900/40 dark:bg-emerald-950/30">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div>
                                                        <span class="font-bold text-emerald-900 dark:text-emerald-200">✨ FEA có sẵn chương trình Lộ trình bài bản:</span>
                                                        <p class="font-bold text-slate-900 dark:text-white" x-text="msg.metadata.related_learning_path.title"></p>
                                                    </div>
                                                    <a :href="msg.metadata.related_learning_path.url" target="_blank" class="shrink-0 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700">
                                                        Xem chi tiết ↗
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Loading UX Indicator --}}
                <div x-show="isLoading" class="flex justify-start">
                    <div class="flex items-center gap-2.5 rounded-2xl rounded-tl-xs border border-slate-200/80 bg-slate-50/90 px-4 py-2.5 text-xs text-slate-700 shadow-xs dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-300">
                        <svg class="h-4 w-4 animate-spin text-[#0056D2]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-medium">Đang nghĩ...</span>
                    </div>
                </div>
            </div>

            {{-- Shortcuts Bar (Chips exactly like Image 2) --}}
            <div class="shrink-0 px-6 py-2.5 border-t border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center gap-2" style="flex-shrink: 0;">
                <button type="button" @click="sendMessage('Tôi chưa biết nên học gì')" :disabled="isLoading" class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-2xs transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Tôi chưa biết nên học gì
                </button>
                <button type="button" @click="sendMessage('Tôi muốn chuyển ngành')" :disabled="isLoading" class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-2xs transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Tôi muốn chuyển ngành
                </button>
                <button type="button" @click="sendMessage('Tôi muốn học để làm việc')" :disabled="isLoading" class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-2xs transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Tôi muốn học để làm việc
                </button>
                <button type="button" @click="sendMessage('Tôi muốn trở thành Backend Developer')" :disabled="isLoading" class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-2xs transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Backend Developer
                </button>
                <button type="button" @click="sendMessage('Tôi muốn trở thành Data Analyst')" :disabled="isLoading" class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-2xs transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Data Analyst
                </button>
            </div>

            {{-- Input Box (Exact match to Image 2) --}}
            <div class="shrink-0 px-6 pb-5 pt-1 bg-white dark:bg-slate-900" style="flex-shrink: 0;">
                <form @submit.prevent="submitChat()" class="relative flex items-center rounded-2xl border border-slate-200 bg-slate-50/80 p-1.5 focus-within:border-[#0056D2] focus-within:bg-white transition dark:border-slate-700 dark:bg-slate-800 dark:focus-within:border-blue-500" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 0.375rem;">
                    <input
                        type="text"
                        x-model="inputMessage"
                        :disabled="isLoading"
                        placeholder="Mô tả mục tiêu học tập của bạn..."
                        class="w-full bg-transparent px-3.5 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none dark:text-white"
                        style="outline: none; background: transparent;"
                    >
                    <button
                        type="submit"
                        :disabled="isLoading || !inputMessage.trim()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0056D2] text-white shadow-xs transition hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-40"
                        style="background-color: #0056D2; width: 36px; height: 36px; border-radius: 0.75rem;"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('learningPathAiChat', (config) => ({
        isOpen: false,
        isLoading: false,
        inputMessage: '',
        showSurvey: true,
        currentLearningPath: null,
        survey: {
            field: '',
            level: 'Beginner',
            goal: 'Tìm việc làm',
            duration: ''
        },
        messages: [],

        init() {
            this.loadConversation();
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async loadConversation() {
            try {
                const url = new URL(config.conversationUrl, window.location.origin);
                if (config.learningPathId) {
                    url.searchParams.set('learning_path_id', config.learningPathId);
                }

                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.messages = data.messages || [];
                    this.currentLearningPath = data.current_learning_path || null;
                    if (this.messages.length > 0) {
                        this.showSurvey = false;
                    }
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (err) {
                console.error('Failed to load conversation', err);
            }
        },

        async submitSurvey() {
            if (!this.survey.field.trim()) {
                alert('Vui lòng nhập ngành hoặc vị trí bạn muốn học.');
                return;
            }
            const prompt = `Tôi muốn học ngành ${this.survey.field}, mục tiêu là ${this.survey.goal}, trình độ hiện tại là ${this.survey.level}${this.survey.duration ? ', thời gian học dự kiến là ' + this.survey.duration : ''}. Hãy xây dựng lộ trình học tập phù hợp cho tôi.`;
            
            const onboardingPayload = {
                field: this.survey.field,
                goal: this.survey.goal,
                level: this.survey.level,
                duration: this.survey.duration
            };

            this.showSurvey = false;
            await this.executeChat(prompt, onboardingPayload);
        },

        sendMessage(text) {
            this.inputMessage = text;
            this.submitChat();
        },

        async submitChat() {
            const text = this.inputMessage.trim();
            if (!text || this.isLoading) return;
            this.inputMessage = '';
            await this.executeChat(text);
        },

        async confirmTopicSwitch(targetMsg, newTopic) {
            if (this.isLoading) return;
            if (targetMsg && targetMsg.metadata) {
                targetMsg.metadata.status = 'resolved';
            }
            await this.executeChat(`Đồng ý, hãy tạo lộ trình học mới cho ${newTopic}.`);
        },

        async dismissTopicSwitch(targetMsg) {
            if (this.isLoading) return;
            if (targetMsg && targetMsg.metadata) {
                targetMsg.metadata.status = 'dismissed';
            }
            await this.executeChat('Tiếp tục lộ trình hiện tại');
        },

        async executeChat(promptText, onboardingData = null) {
            if (this.isLoading) return;

            // Append user message immediately
            const tempUserMsg = {
                tempId: Date.now(),
                role: 'user',
                content: promptText
            };
            this.messages.push(tempUserMsg);
            this.isLoading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch(config.chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        message: promptText,
                        onboarding: onboardingData || {},
                        learning_path_id: config.learningPathId
                    })
                });

                const data = await res.json();

                if (data.success) {
                    this.messages.push({
                        id: Date.now() + 1,
                        role: 'assistant',
                        content: data.message,
                        metadata: {
                            type: data.response_type,
                            roadmap: data.roadmap,
                            matched_courses: data.matched_courses,
                            related_learning_path: data.related_learning_path,
                            new_topic: data.new_topic,
                            status: data.is_topic_switch ? 'pending' : undefined
                        }
                    });
                } else {
                    this.messages.push({
                        id: Date.now() + 1,
                        role: 'assistant',
                        content: data.message || 'Có lỗi xảy ra, vui lòng thử lại sau.'
                    });
                }
            } catch (err) {
                this.messages.push({
                    id: Date.now() + 1,
                    role: 'assistant',
                    content: 'Không thể kết nối đến máy chủ AI. Vui lòng kiểm tra mạng và thử lại.'
                });
            } finally {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async resetChat() {
            if (this.isLoading) return;
            if (!confirm('Bạn có chắc chắn muốn bắt đầu cuộc trò chuyện lộ trình mới?')) return;
            try {
                const res = await fetch(config.resetUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        learning_path_id: config.learningPathId
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.messages = [];
                    this.showSurvey = true;
                }
            } catch (err) {
                console.error('Reset failed', err);
            }
        },

        hasCourseInStageMatches(matches) {
            if (!Array.isArray(matches)) return false;
            return matches.some(m => m && m.has_course && m.course_id);
        },

        getCoursesOnly(matches) {
            if (!Array.isArray(matches)) return [];
            return matches.filter(m => m && m.has_course && m.course_id);
        },

        getMatchedCoursesForStage(matchedStages, stageNumber) {
            if (!matchedStages) return [];
            const stages = Array.isArray(matchedStages) ? matchedStages : (matchedStages.stages || []);
            const stg = stages.find(s => s.stage === stageNumber);
            return stg ? (stg.matches || []) : [];
        },

        scrollToBottom() {
            const container = document.getElementById('lp-chat-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }));
});
</script>
