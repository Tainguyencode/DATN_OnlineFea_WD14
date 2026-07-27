<?php

return [
    'default_status' => env('COURSE_REVIEW_DEFAULT_STATUS', 'approved'),
    'reset_status_on_update' => env('COURSE_REVIEW_REMODERATE_ON_UPDATE', false),
    'minimum_progress_percent' => (float) env('COURSE_REVIEW_MIN_PROGRESS', 0.01),
    'per_page' => (int) env('COURSE_REVIEW_PER_PAGE', 8),
];
