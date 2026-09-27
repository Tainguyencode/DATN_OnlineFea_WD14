# Immutable Content Versioning (Phases 5C1–5C2)

Phase 5C1 adds historical snapshots. Phase 5C2 makes the published pointer authoritative after a ContentUpdate approval while retaining current identity rows as a compatibility projection.

## Identity and version

Existing rows are stable identities:

- `courses` → `course_versions`
- `course_sections` → `course_section_versions`
- `lessons` → `lesson_versions`
- `assignments` → `assignment_versions`

For example:

```text
Lesson #10
├── LessonVersion #31 V1 superseded
├── LessonVersion #42 V2 published
└── LessonVersion #57 V3 draft
```

Version numbers are scoped to one identity. A course V2 does not imply every section or lesson is V2.

## Compatibility rules

`ContentUpdate` and the Phase 5A state machine remain authoritative for production approval. On the canonical draft → pending transition for a published identity, Phase 5C2 materializes exactly one immutable candidate tied to `content_update_id`. Approval atomically supersedes V1, publishes the candidate, swaps `published_version_id`, clears `draft_version_id`, and projects the published fields back to the identity row. Rejection retains a rejected candidate for audit and keeps V1/live data unchanged.

Existing pages still read the compatibility identity rows, but those rows must mirror the published pointer after activation. The central `ContentVersionService` owns all projection mappings; controllers and review services must not duplicate them. Old pending records created before this transition are repaired from their already-immutable pending payload at approval.

Quiz remains independent: its established `QuizVersion`/`QuestionVersion` architecture is not migrated or generalized.

`courses.slug` remains stable identity routing metadata. Its value is snapshotted for audit, but activation deliberately does not rewrite it; approved metadata edits do not invalidate existing public URLs.

Legacy `chapters` has no version table. `course_sections` is canonical; `legacy_chapter_id` is only captured on a lesson snapshot as compatibility metadata.

## Media and history

`LessonVersion` preserves document and video/HLS references. Activation only projects the approved V2 references; it does not move or delete V1 files. Pending/rejected media therefore cannot disturb the currently published lesson, and superseded versions retain historical references. A later retention phase may clean media only with an explicit audit-safe policy.

Lesson progress remains tied to stable `lesson_id`: ordinary content edits do not erase completion. New assignment attempts bind `submissions.assignment_version_id` to the assignment's current published pointer; that binding is never rewritten. Completion policy reads the bound assignment version where present and uses the live assignment only for legacy null bindings.

## Backfill

Draft legacy courses intentionally remain legacy authoring data in 5C1. Published/approved legacy courses can be snapshotted explicitly:

```powershell
php artisan content-versions:backfill --dry-run
php artisan content-versions:backfill
```

The command is idempotent: it creates missing V1 snapshots and never creates V2 merely because it is rerun. It must be operated deliberately per environment; this phase does not run it automatically against development data.
