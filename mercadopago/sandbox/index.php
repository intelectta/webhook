<?php
/**
 * Mercado Pago - Webhook Sandbox
 * Intelectta
 */

date_default_timezone_set('America/Sao_Paulo');

// Garante resposta rápida
header("Content-Type: application/json");

// Lê o corpo da requisição
$rawBody = file_get_contents("php://input");

// Cria pasta de logs se não existir
$logDir = __DIR__ . "/logs";
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Arquivo de log por dia
$logFile = $logDir . "/webhook_" . date("Y-m-d") . ".log";

// Log bruto (sempre)
file_put_contents(
    $logFile,
    "=============================\n" .
    date("Y-m-d H:i:s") . "\n" .
    "RAW BODY:\n" .
    $rawBody . "\n\n",
    FILE_APPEND
);

// Decodifica JSON
$data = json_decode($rawBody, true);

// Validação mínima (necessária para testes)
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "JSON inválido"]);
    exit;
}

// Extrai dados principais
$eventType  = $data["type"]        ?? "unknown";
$action     = $data["action"]      ?? "unknown";
$paymentId  = $data["data"]["id"]  ?? null;
$liveMode   = $data["live_mode"]   ?? null;

// Log estruturado
file_put_contents(
    $logFile,
    "EVENTO:\n" .
    "type: {$eventType}\n" .
    "action: {$action}\n" .
    "payment_id: {$paymentId}\n" .
    "live_mode: " . var_export($liveMode, true) . "\n\n",
    FILE_APPEND
);

// Aqui você pode futuramente:
// - consultar o pagamento na API
// - salvar no banco
// - atualizar status
// - disparar e-mail

// Resposta OBRIGATÓRIA ao Mercado Pago
http_response_code(200);
echo json_encode([
    "status" => "ok",
    "received" => true
]);

