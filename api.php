<?php
header('Content-Type: application/json');
$config = include 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['topic'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data.']);
    exit;
}

$topic = $input['topic'];
$scenes = intval($input['scenes']);
$style = $input['style'];
$intensity = $input['intensity'];
$tone = $input['tone'];
$continuity = $input['continuity'] ? "Maintain visual continuity throughout scenes." : "Scenes can be distinct but related.";
$subtitles = $input['subtitles'] ? "Include a subtitles section." : "Do not include subtitles.";

$total_duration = $scenes * 8;

$system_prompt = "You are a professional video prompt engineer. 
Generate a script for an 8-second per scene video. Total duration: $total_duration seconds.
Topic: $topic. Visual Style: $style. Intensity: $intensity. Audio Tone: $tone.
$continuity

Strict Output Format:
=== INDONESIAN VERSION ===
VIDEO PROMPT 1 (8 seconds – HOOK):
[Detailed visual description, 9:16 aspect ratio, cinematic 3D, no text/narration]

VIDEO PROMPT N (8 seconds – CONCLUSION):
...

AUDIO NARRATION (~$total_duration seconds):
...

" . ($input['subtitles'] ? "SUBTITLES:\nScene 1: [text]\n..." : "") . "

=== ENGLISH VERSION ===
VIDEO PROMPT 1 (8 seconds – HOOK):
...

AUDIO NARRATION (~$total_duration seconds):
...

ELEVENLABS SETTINGS:
Language: English
Tone: $tone
Speed: Medium
No music
No effects";

$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $config['api_key'],
    'Content-Type: application/json',
    'HTTP-Referer: http://localhost', 
    'X-Title: PromptGen'
]);

$payload = [
    'model' => $config['model'],
    'messages' => [
        ['role' => 'system', 'content' => 'You only output the requested formatted text. No conversational fillers.'],
        ['role' => 'user', 'content' => $system_prompt]
    ]
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['success' => false, 'error' => 'CURL Error: ' . $err]);
} else {
    $res_data = json_decode($response, true);
    if (isset($res_data['choices'][0]['message']['content'])) {
        echo json_encode(['success' => true, 'content' => $res_data['choices'][0]['message']['content']]);
    } else {
        $msg = $res_data['error']['message'] ?? 'AI Error';
        echo json_encode(['success' => false, 'error' => $msg]);
    }
}
