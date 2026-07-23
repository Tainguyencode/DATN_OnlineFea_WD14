@php($selectedRating = (int) old('rating', $review?->rating ?? 0))
<fieldset>
    <legend class="text-sm font-bold text-slate-800 dark:text-slate-100">Mức đánh giá <span class="text-rose-600">*</span></legend>
    <div class="mt-2 flex flex-wrap gap-2">
        @for($star = 1; $star <= 5; $star++)
            <label class="cursor-pointer">
                <input type="radio" name="rating" value="{{ $star }}" class="peer sr-only" @checked($selectedRating === $star) required>
                <span class="flex h-11 items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 text-sm font-bold text-slate-600 transition-colors hover:border-amber-300 peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:peer-checked:bg-amber-500/10 dark:peer-checked:text-amber-300">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                    {{ $star }}
                </span>
            </label>
        @endfor
    </div>
    @error('rating')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
</fieldset>

<div>
    <label for="review-comment-{{ $review?->id ?? 'new' }}" class="text-sm font-bold text-slate-800 dark:text-slate-100">Nội dung nhận xét <span class="text-rose-600">*</span></label>
    <textarea id="review-comment-{{ $review?->id ?? 'new' }}" name="comment" rows="5" minlength="10" maxlength="2000" required class="mt-2 block w-full resize-y rounded-xl border-slate-300 bg-white text-sm leading-6 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white" placeholder="Điều gì hữu ích nhất? Khóa học có đáp ứng mong đợi của bạn không?">{{ old('comment', $review?->comment) }}</textarea>
    <div class="mt-1 flex justify-between gap-3 text-xs text-slate-500 dark:text-slate-400"><span>10–2.000 ký tự, không nhập HTML.</span></div>
    @error('comment')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
</div>

<button type="submit" :disabled="submitting" class="inline-flex h-11 cursor-pointer items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-extrabold text-white transition-colors hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
    <span x-show="!submitting">{{ $submitLabel }}</span>
    <span x-cloak x-show="submitting">Đang gửi...</span>
</button>
