<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


$inputData = file_get_contents("php://input");
$decodedInput = json_decode($inputData, true);
$userPrompt = $decodedInput['prompt'] ?? 'hi';


$runpodUrl = "https://d5pgaeqaz076r2-8000.proxy.runpod.net/chat";


$ch = curl_init($runpodUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 6);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response !== false && $httpCode === 200) {

    echo $response;
    exit;
}

$groqUrl = "https://api.groq.com/openai/v1/chat/completions";
$groqKey = getenv('GROQ_API_KEY');

$groqData = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        [
            "role" => "system",
            "content" => "Anda adalah 'Pocket AI Advisor', pakar penasihat kewangan peribadi. Berikan nasihat yang ringkas, tegas, kelakar, dan gunakan Bahasa Melayu santai."
        ],
        [
            "role" => "user",
            "content" => $userPrompt
        ]
    ]
];

$chBackup = curl_init($groqUrl);
curl_setopt($chBackup, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chBackup, CURLOPT_POST, true);
curl_setopt($chBackup, CURLOPT_POSTFIELDS, json_encode($groqData));
curl_setopt($chBackup, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $groqKey
));
curl_setopt($chBackup, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chBackup, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($chBackup, CURLOPT_TIMEOUT, 5); 

$backupResponse = curl_exec($chBackup);
curl_close($chBackup);

$decodedBackup = json_decode($backupResponse, true);
$aiReply = $decodedBackup['choices'][0]['message']['content'] ?? "Aduh, AI kami sedang berehat sekejap. Cuba lagi mesej awak.";

echo json_encode(["response" => $aiReply]);
?>