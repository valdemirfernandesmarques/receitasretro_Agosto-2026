<?php
function upload_para_cloudinary($arquivo_tmp) {
    // Busca as chaves com segurança das variáveis de ambiente ou utiliza os valores padrão
    $cloud_name = getenv('CLOUDINARY_CLOUD_NAME') ?: 'nfaazxlq'; 
    $api_key    = getenv('CLOUDINARY_API_KEY') ?: '651547368421455'; 
    $api_secret = getenv('CLOUDINARY_API_SECRET'); 

    if (empty($api_secret)) {
        error_log("Erro Cloudinary: API Secret não configurado.");
        return false;
    }

    $timestamp = time();
    $signature = sha1("timestamp=" . $timestamp . $api_secret);

    $data = [
        'file'      => new CURLFile($arquivo_tmp),
        'api_key'   => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $cloud_name . "/image/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return false;
    }

    $result = json_decode($response, true);

    if (isset($result['secure_url'])) {
        return $result['secure_url']; // Retorna a URL HTTPS permanente da nuvem
    }

    return false;
}
?>