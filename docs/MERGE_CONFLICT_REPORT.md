# MERGE_CONFLICT_REPORT — Merge TuanTu_Dev into main

**Date:** 2026-07-11  
**Branch:** `main` ← `TuanTu_Dev`  
**Status:** Conflicts resolved in working tree (ready for `git add`, not committed)

---

## Summary

39 files were in unmerged state. All conflict markers have been removed. Resolution strategy: **combine valid logic from both branches** — never wholesale "ours" or "theirs".

---

## Files resolved

| File | Conflict type | main kept | TuanTu_Dev kept | Merge decision |
|------|---------------|-----------|-----------------|----------------|
| `.gitignore` | content | IDE ignores | Laravel/vendor rules | Union of both |
| `app/Models/User.php` | content | Social, roles | Email codes, lessonProgress | Full merge |
| `app/Models/Cart.php` | content | `courses()` pivot | `items()` hasMany | Both relationships |
| `app/Models/Course.php` | content | Submission validator, labels | CourseStatus enum, learningEntryUrl | Enum + both feature sets |
| `app/Models/CourseReview.php` | content | action/items (old) | status/checklist_json | **TuanTu schema** (matches migration) |
| `app/Models/*` (Enrollment, Lesson, etc.) | content/AA | Progress fields | Enhanced columns | Merged fillable/casts |
| `app/Services/AuthService.php` | content | Registration flow | Email verification, events | TuanTu + Registered event |
| `app/Services/LearningProgressService.php` | AA | Video progress | Quiz/assignment completion | 6-arg recordLessonProgress |
| `app/Http/Controllers/Web/AuthController.php` | content | Student hub | Code verification, quick login | No duplicate Socialite |
| `app/Http/Controllers/Web/CourseController.php` | content | Filters, favorites | LearningPlayerService | Merged show + lesson player |
| `app/Http/Controllers/Web/Student/CartController.php` | content | Pending order checkout | Add validation | HEAD checkout + TuanTu validation |
| `app/Http/Controllers/Web/Student/QuizController.php` | AA | Basic quiz | QuizService, submitAjax | TuanTu service layer |
| `app/Http/Controllers/Web/Instructor/*` | content/AA | _form partial, AI moderation | CourseReviewService submit | HEAD UI + TuanTu workflow |
| `app/Http/Controllers/Web/Admin/*` | content | Manage stats | Course review admin | Both dashboards/routes |
| `app/Http/Requests/*` | content | Avatar upload | Email/phone validation | Union rules |
| `routes/web.php` | content | Cart/checkout/payment | Social, verification code, admin review | All routes, no duplicate names |
| `config/services.php` | content | GitHub OAuth | Google/Facebook | All providers |
| `vite.config.js` | content | app.css entry | learning-player.js | Both entries |
| `resources/css/app.css` | content | Public UI styles | Learning player CSS | Import union |
| `database/seeders/*` | content | Sample courses | PermissionSeeder, roles | Ordered call chain |
| `resources/views/auth/*` | content/AA | Payment in orders | 6-digit code, cart hub | **Merged verify-email** |
| `resources/views/components/auth/social-buttons.blade.php` | content | Google/GitHub | Conditional Google/Facebook | TuanTu isConfigured pattern |
| `resources/views/courses/lesson.blade.php` | content | Inline video JS | Learning layout/components | **TuanTu player** |
| `resources/views/courses/show.blade.php` | content | Rich curriculum UI | learningEntryUrl, enroll CTA | Both UIs merged |
| `resources/views/instructor/courses/*` | content | `_form` partial | Inline forms | HEAD _form + TuanTu statuses |
| `resources/views/student/courses/index.blade.php` | content | Card layout | learningEntryUrl | Merged CTAs |

---

## Key architectural decisions

1. **Course status:** `pending_review`, `approved`, `rejected`, `published` via `CourseStatus` enum (TuanTu migrations).
2. **CourseReview:** Single table with `status`, `checklist_json`, `submission_number` — no `CourseReviewItem`.
3. **Cart checkout:** Create pending `Order` + `OrderItem`; enroll only after payment (main).
4. **Learning:** `LearningPlayerService`, `LearningProgressService`, dedicated `layouts.learning`.
5. **Auth:** Email verification **code** (TuanTu) + SocialAuthController; social buttons only when configured.

---

## Risks / follow-up

- Run `php artisan migrate:status` on target DB before seeding.
- Verify `.env` has OAuth keys if social login is required.
- Accidental merge artifacts: `et --hard HEAD`, `laravel_test` — review before commit.
- `ProfileController::studentShow` and admin `ManageController` methods must match routes (verified in merged `web.php`).

---

## Validation commands (run after `git add`)

```bash
git diff --check
git grep -n "<<<<<<<"
composer dump-autoload
php artisan optimize:clear
php artisan route:list
php artisan migrate:status
php artisan test --stop-on-failure
npm run build
```

---

## Proposed commit

```bash
git add .
git commit -m "Merge TuanTu_Dev into main and resolve conflicts"
```

Do **not** push unless explicitly requested.
