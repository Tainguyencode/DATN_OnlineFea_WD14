<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$result = DB::transaction(function (): array {
    $users = User::query()
        ->where(fn ($query) => $query
            ->where('email', 'like', '%@faculty.example.test')
            ->orWhere('email', 'like', '%@learners.example.test'))
        ->get();
    $changed = 0;

    foreach ($users as $user) {
        $oldName = $user->name;
        $newName = trim((string) preg_replace(
            '/^(?:Em|Bác|Cụ|Chú|Ông|Bà|Cô|Chị|Anh)\.\s+/u',
            '',
            $oldName
        ));

        if ($newName === '' || $newName === $oldName) {
            continue;
        }

        $base = Str::of($newName)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->limit(24, '')
            ->toString() ?: 'fea_member';

        do {
            $username = $base.'_'.strtolower(Str::random(5));
        } while (User::query()->whereKeyNot($user->id)->where('username', $username)->exists());

        $domain = $user->role === 'instructor'
            ? 'faculty.example.test'
            : 'learners.example.test';

        $user->name = $newName;
        $user->username = $username;
        $user->email = $username.'@'.$domain;
        $user->bio = $user->bio ? str_replace($oldName, $newName, $user->bio) : null;
        $user->save();

        if ($user->role === 'instructor') {
            $profile = $user->instructorProfile;
            if ($profile) {
                $profile->bio = $profile->bio ? str_replace($oldName, $newName, $profile->bio) : null;
                $profile->save();
            }

            $application = $user->instructorApplication;
            if ($application) {
                $application->introduction = $application->introduction
                    ? str_replace($oldName, $newName, $application->introduction)
                    : null;
                $application->save();
            }
        }

        $changed++;
    }

    $remainingTitles = User::query()
        ->where(fn ($query) => $query
            ->where('email', 'like', '%@faculty.example.test')
            ->orWhere('email', 'like', '%@learners.example.test'))
        ->whereRaw("name REGEXP '^(Em|Bác|Cụ|Chú|Ông|Bà|Cô|Chị|Anh)\\\\.'")
        ->count();

    if ($remainingTitles !== 0) {
        throw new RuntimeException("Name normalization failed for {$remainingTitles} accounts.");
    }

    return ['normalized' => $changed, 'remaining_titles' => $remainingTitles];
});

dump($result);
