<x-student-layout title="Trở thành Giảng viên" page-title="Đăng ký Giảng viên" breadcrumb="Gửi hồ sơ để admin duyệt">
    <div class="mx-auto max-w-3xl">
        <div class="ui-card p-6 sm:p-8">
            <p class="mb-6 text-sm text-[#6B7280]">Hoàn thành biểu mẫu dưới đây. Sau khi gửi, trạng thái hồ sơ sẽ là <strong class="text-[#0F172A]">Pending</strong> và admin sẽ xem xét.</p>

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-sm text-[#B91C1C]">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.become-instructor.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="ui-label">Họ tên</label>
                        <input type="text" value="{{ $user->name }}" disabled class="ui-input bg-[#F8FAFC]">
                    </div>
                    <div>
                        <label class="ui-label">Email / SĐT</label>
                        <input type="text" value="{{ $user->email ?? $user->phone }}" disabled class="ui-input bg-[#F8FAFC]">
                    </div>
                </div>

                <div>
                    <label class="ui-label">Ảnh đại diện</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-[#6B7280]">
                </div>

                <div>
                    <label class="ui-label">Chuyên môn *</label>
                    <input type="text" name="expertise" value="{{ old('expertise') }}" required class="ui-input">
                </div>

                <div>
                    <label class="ui-label">Kinh nghiệm *</label>
                    <textarea name="experience" rows="4" required class="ui-input">{{ old('experience') }}</textarea>
                </div>

                <div>
                    <label class="ui-label">Giới thiệu bản thân *</label>
                    <textarea name="introduction" rows="4" required class="ui-input">{{ old('introduction') }}</textarea>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="ui-label">CV (PDF/DOC) *</label>
                        <input type="file" name="cv" accept=".pdf,.doc,.docx" required class="w-full text-sm text-[#6B7280]">
                    </div>
                    <div>
                        <label class="ui-label">Bằng cấp</label>
                        <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-[#6B7280]">
                    </div>
                </div>

                <div>
                    <label class="ui-label">Video giới thiệu (URL)</label>
                    <input type="url" name="intro_video_url" value="{{ old('intro_video_url') }}" placeholder="https://youtube.com/..." class="ui-input">
                </div>

                <div class="rounded-xl border border-[#E5E7EB] p-4 space-y-4">
                    <h3 class="text-sm font-semibold text-[#0F172A]">Tài khoản ngân hàng</h3>
                    <div>
                        <label class="ui-label">Tên ngân hàng *</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" required class="ui-input">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="ui-label">Số tài khoản *</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" required class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Tên chủ tài khoản *</label>
                            <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" required class="ui-input">
                        </div>
                    </div>
                </div>

                <button type="submit" class="ui-btn-primary">Gửi đơn đăng ký</button>
            </form>
        </div>
    </div>
</x-student-layout>
