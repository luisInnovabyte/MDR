<?php
/**
 * Script de diagnóstico — acceder via:
 * http://192.168.31.19/MDR/HTML/chat/api/test.php
 */
header('Content-Type: application/json; charset=utf-8');

$apiKey = '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';

$result = [
    'php_version'    => PHP_VERSION,
    'curl_enabled'   => function_exists('curl_init'),
    'openssl'        => extension_loaded('openssl'),
    'temp_dir'       => sys_get_temp_dir(),
    'temp_writable'  => is_writable(sys_get_temp_dir()),
    'api_test'       => null,
    'api_error'      => null,
];

// Test llamada NO-streaming a la API
if ($result['curl_enabled']) {
    $payload = json_encode([
        'model'      => 'claude-3-5-haiku-20241022',
        'max_tokens' => 50,
        'messages'   => [['role' => 'user', 'content' => 'Di solo "OK"']],
    ]);

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'anthropic-version: 2023-06-01',
            'x-api-key: ' . $apiKey,
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false, // por si hay problema SSL en local
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $result['http_code']  = $httpCode;
    $result['curl_error'] = $curlError ?: null;

    if ($response) {
        $decoded = json_decode($response, true);
        $result['api_test']  = $decoded['content'][0]['text'] ?? null;
        $result['api_error'] = $decoded['error'] ?? null;
        $result['raw']       = strlen($response) > 500 ? substr($response, 0, 500) . '...' : $response;
    } else {
        $result['api_error'] = 'No response from curl';
    }
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
