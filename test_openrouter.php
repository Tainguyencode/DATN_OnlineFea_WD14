<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$key = env('OPENROUTER_API_KEY');
$models = [
    'google/gemini-3.5-flash',
    'google/gemini-3.1-flash-image',
];

foreach ($models as $model) {
    $response = Illuminate\Support\Facades\Http::withHeaders(['Authorization' => "Bearer $key"])
        ->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 200
        ]);
        
    echo "Model: $model -> Status: " . $response->status() . "\n";
    if ($response->failed()) {
        echo $response->body() . "\n\n";
    } else {
        echo "SUCCESS!\n\n";
        break; // Stop on first success
    }
}
