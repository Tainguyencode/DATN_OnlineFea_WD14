<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorTeachingField;
use App\Services\ActivityLogService;
use App\Services\InstructorRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstructorTeachingFieldReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', InstructorTeachingField::STATUS_PENDING);
        $fields = InstructorTeachingField::query()
            ->with(['profile.user:id,name,email,username', 'category.parent', 'replacedField.category', 'certificates.requirement'])
            ->when(in_array($status, [InstructorTeachingField::STATUS_DRAFT, InstructorTeachingField::STATUS_PENDING, InstructorTeachingField::STATUS_APPROVED, InstructorTeachingField::STATUS_REJECTED, InstructorTeachingField::STATUS_SUPERSEDED], true), fn ($query) => $query->where('approval_status', $status))
            ->latest('submitted_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $requirements = app(InstructorRequirementService::class);
        $fields->getCollection()->each(fn (InstructorTeachingField $field) => $field->setAttribute('requirement_data', $requirements->getTeachingFieldRequirementData($field)));

        return view('admin.instructors.teaching-fields.index', compact('fields', 'status'));
    }

    public function approve(Request $request, InstructorTeachingField $teachingField): RedirectResponse
    {
        DB::transaction(function () use ($request, $teachingField): void {
            $field = InstructorTeachingField::query()->lockForUpdate()->findOrFail($teachingField->id);
            abort_unless($field->approval_status === InstructorTeachingField::STATUS_PENDING, 422, 'Chỉ ngành đang chờ duyệt mới có thể phê duyệt.');

            $replacedField = $field->replace_of_teaching_field_id
                ? InstructorTeachingField::query()
                    ->where('instructor_profile_id', $field->instructor_profile_id)
                    ->lockForUpdate()
                    ->find($field->replace_of_teaching_field_id)
                : null;

            abort_if(
                $field->replace_of_teaching_field_id && (! $replacedField || ! $replacedField->isApproved()),
                422,
                'Ngành được thay thế không còn ở trạng thái đã duyệt.'
            );

            $field->update([
                'approval_status' => InstructorTeachingField::STATUS_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'rejection_reason' => null,
            ]);

            $field->certificates()
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => $request->user()->id,
                ]);

            if ($replacedField) {
                // Preserve A for history and existing owned courses, while preventing
                // it from granting permission for newly-created courses.
                $wasPrimary = (bool) $replacedField->is_primary;
                $replacedField->update([
                    'approval_status' => InstructorTeachingField::STATUS_SUPERSEDED,
                    'is_primary' => false,
                ]);

                $field->update(['is_primary' => $wasPrimary]);

                if ($wasPrimary) {
                    $field->profile()->update(['category_id' => $field->category_id]);
                }
            }
        });

        ActivityLogService::log($request->user()->id, 'approve_instructor_teaching_field', InstructorTeachingField::class, $teachingField->id);

        return back()->with('success', 'Đã duyệt ngành giảng dạy. Quyền tạo khóa học theo ngành này đã được cấp.');
    }

    public function reject(Request $request, InstructorTeachingField $teachingField): RedirectResponse
    {
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'min:10', 'max:2000']]);

        DB::transaction(function () use ($request, $teachingField, $validated): void {
            $field = InstructorTeachingField::query()->lockForUpdate()->findOrFail($teachingField->id);
            abort_unless($field->approval_status === InstructorTeachingField::STATUS_PENDING, 422, 'Chỉ ngành đang chờ duyệt mới có thể từ chối.');
            $field->update([
                'approval_status' => InstructorTeachingField::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'rejection_reason' => $validated['rejection_reason'],
            ]);
        });

        ActivityLogService::log($request->user()->id, 'reject_instructor_teaching_field', InstructorTeachingField::class, $teachingField->id, ['reason' => $validated['rejection_reason']]);

        return back()->with('success', 'Đã từ chối yêu cầu ngành giảng dạy.');
    }
}
