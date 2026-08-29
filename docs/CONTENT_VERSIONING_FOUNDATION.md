# Immutable Content Versioning Foundation (Phase 5C1)

Phase 5C1 adds historical snapshots without changing current application reads or writes.

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

`ContentUpdate` and the Phase 5A state machine remain authoritative for production approval. Existing pages continue to read the live identity rows. Phase 5C2 can construct candidates and activate pointers transactionally.

Quiz remains independent: its established `QuizVersion`/`QuestionVersion` architecture is not migrated or generalized.

`courses.slug` remains stable identity routing metadata. Its value is snapshotted for audit only; no version activation is enabled in this phase.

Legacy `chapters` has no version table. `course_sections` is canonical; `legacy_chapter_id` is only captured on a lesson snapshot as compatibility metadata.

## Media and history

`LessonVersion` preserves document and video/HLS references. Phase 5C1 never moves or deletes media. Future activation should use version-specific immutable media paths because SQL transactions cannot roll back filesystem/S3 operations.

Lesson progress remains tied to stable `lesson_id`: ordinary content edits must not erase completion. `submissions.assignment_version_id` is nullable for future historical assignment-contract binding; legacy submissions remain valid with `null`.

## Backfill

Draft legacy courses intentionally remain legacy authoring data in 5C1. Published/approved legacy courses can be snapshotted explicitly:

```powershell
php artisan content-versions:backfill --dry-run
php artisan content-versions:backfill
```

The command is idempotent: it creates missing V1 snapshots and never creates V2 merely because it is rerun. It must be operated deliberately per environment; this phase does not run it automatically against development data.
