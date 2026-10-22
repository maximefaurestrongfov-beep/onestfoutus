<?php
/**
 * On est foutus ? — vérification d'un paiement Stripe pour le certificat de politesse.
 * À déposer sur l'hébergement OVH : api.strongfamily.fr/onestfoutus/verify.php
 * Appel : verify.php?session_id=cs_live_...
 * Réponse JSON : {"paid":true,"numero":"N° 123456","date":"22/10/2026","name":"..."} ou {"paid":false,"error":"..."}
 */

$config = require __DIR__ . '/config.php';

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $config['allowed_origins'], true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function reply(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$sid = $_GET['session_id'] ?? '';
if (!preg_match('/^cs_(live|test)_[A-Za-z0-9]{10,200}$/', $sid)) {
    reply(400, ['paid' => false, 'error' => 'session_invalide']);
}

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . rawurlencode($sid));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $config['stripe_key']],
    CURLOPT_TIMEOUT        => 10,
]);
$body = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($body === false) {
    reply(502, ['paid' => false, 'error' => 'stripe_injoignable']);
}
if ($status === 404) {
    reply(404, ['paid' => false, 'error' => 'session_inconnue']);
}
if ($status !== 200) {
    reply(502, ['paid' => false, 'error' => 'erreur_stripe']);
}

$s = json_decode($body, true);

// Avec la conversion de devises de Stripe (Adaptive Pricing), le montant d'origine en euros est dans currency_conversion
$currency = $s['currency'] ?? '';
$amount   = (int)($s['amount_total'] ?? 0);
if (!empty($s['currency_conversion']['source_currency'])) {
    $currency = $s['currency_conversion']['source_currency'];
    $amount   = (int)($s['currency_conversion']['amount_total'] ?? 0);
}

$ok = ($s['status'] ?? '') === 'complete'
   && ($s['payment_status'] ?? '') === 'paid'
   && $currency === 'eur'
   && $amount >= (int)$config['min_amount'];

if ($ok && !empty($config['payment_link'])) {
    $ok = ($s['payment_link'] ?? '') === $config['payment_link'];
}
if (!$ok) {
    reply(402, ['paid' => false, 'error' => 'non_paye']);
}

// Nom saisi dans le champ personnalisé du lien de paiement, s'il existe
$name = '';
foreach (($s['custom_fields'] ?? []) as $f) {
    if (($f['type'] ?? '') === 'text' && !empty($f['text']['value'])) {
        $name = mb_substr(trim($f['text']['value']), 0, 40);
        break;
    }
}

$date = (new DateTime('@' . (int)$s['created']))
    ->setTimezone(new DateTimeZone('Europe/Paris'))
    ->format('d/m/Y');
$numero = 'N° ' . (100000 + (crc32($sid) % 900000));

reply(200, ['paid' => true, 'numero' => $numero, 'date' => $date, 'name' => $name]);
