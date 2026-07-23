<x-student-layout title="Yêu thích" page-title="Khóa học yêu thích" breadcrumb="Danh sách khóa học bạn đã lưu để xem lại sau.">
    @if($items->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có khóa học yêu thích" description="Lưu những khóa bạn quan tâm để dễ dàng quay lại." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
    @else
        <div x-data="{
                totalCount: {{ (int) $items->total() }},
                visibleCount: {{ (int) $items->count() }},
                async removeCourse(courseId, destroyUrl, $el) {
                    if (!confirm('Bỏ khóa học này khỏi danh sách yêu thích?')) return;

                    try {
                        const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || @js(csrf_token());
                        const res = await fetch(destroyUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                _token: token,
                                _method: 'DELETE'
                            })
                        });

                        if (!res.ok) throw new Error('Request failed');
                        const data = await res.json();

                        const card = $el.closest('[data-wishlist-card]');
                        if (card) {
                            card.style.transition = 'all 0.3s ease-out';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                card.remove();
                                this.visibleCount--;
                                this.totalCount = typeof data.count !== 'undefined' ? data.count : Math.max(0, this.totalCount - 1);
                            }, 300);
                        } else {
                            this.visibleCount--;
                            this.totalCount = typeof data.count !== 'undefined' ? data.count : Math.max(0, this.totalCount - 1);
                        }

                        window.dispatchEvent(new CustomEvent('favorite-updated', {
                            detail: {
                                courseId: courseId,
                                favorited: false,
                                count: data.count,
                                message: data.message
                            }
                        }));

                        if (window.AppToast && data.message) {
                            window.AppToast.show({
                                message: data.message,
                                type: 'info'
                            });
                        }
                    } catch (err) {
                        if (window.AppToast) {
                            window.AppToast.show({
                                message: 'Không thể bỏ lưu khóa học. Vui lòng thử lại.',
                                type: 'error'
                            });
                        }
                    }
                }
            }">
            <template x-if="totalCount > 0 && visibleCount > 0">
                <div>
                    <p class="mb-5 text-sm font-semibold text-slate-500">
                        <span x-text="totalCount">{{ $items->total() }}</span> khóa học đã lưu
                    </p>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
                        @foreach($items as $item)
                            @continue(! $item->course)
                            @php
                                $owned = in_array($item->course->id, $enrolledCourseIds, true);
                                $inCart = in_array($item->course->id, $cartCourseIds, true);
                                $primaryUrl = $owned
                                    ? ($item->course->learningEntryUrl() ?? route('courses.show', $item->course->slug))
                                    : ($inCart ? route('student.cart') : route('courses.show', $item->course->slug));
                                $primaryLabel = $owned ? 'Vào học' : ($inCart ? 'Đã có trong giỏ' : 'Chi tiết');
                            @endphp
                            <div data-wishlist-card class="flex flex-col">
                                <x-student.dashboard.course-card :course="$item->course">
                                    <x-slot:actions>
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ $primaryUrl }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0046B8] hover:shadow-md active:translate-y-0 active:scale-95">{{ $primaryLabel }}</a>
                                            <button type="button"
                                                    x-on:click="removeCourse({{ (int) $item->course->id }}, @js(route('courses.favorite.destroy', $item->course)), $el)"
                                                    class="min-h-10 w-full cursor-pointer rounded-xl border border-rose-200 bg-white px-3 text-sm font-bold text-rose-600 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-50 hover:shadow active:translate-y-0 active:scale-95 disabled:opacity-60 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                                Bỏ lưu
                                            </button>
                                        </div>
                                    </x-slot:actions>
                                </x-student.dashboard.course-card>
                            </div>
                        @endforeach
                    </div>
                    <x-student.dashboard.pagination :paginator="$items" />
                </div>
            </template>
            <template x-if="totalCount === 0 || visibleCount === 0">
                <x-student.dashboard.empty-state title="Chưa có khóa học yêu thích" description="Lưu những khóa bạn quan tâm để dễ dàng quay lại." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
            </template>
        </div>
    @endif
</x-student-layout>
