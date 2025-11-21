<?php
/**
 * API para Buscar Endereço por CEP
 * Utiliza a API ViaCEP (gratuita e pública)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Validar CEP
$cep = isset($_GET['cep']) ? preg_replace('/[^0-9]/', '', $_GET['cep']) : '';

if (empty($cep) || strlen($cep) !== 8) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'CEP inválido. Digite um CEP com 8 dígitos.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Consultar ViaCEP
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    // Usar cURL se disponível, senão usar file_get_contents
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Erro ao consultar CEP');
        }
    } else {
        // Fallback para file_get_contents
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'GET'
            ]
        ]);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Erro ao consultar CEP');
        }
    }
    
    $data = json_decode($response, true);
    
    // Verificar se CEP foi encontrado
    if (isset($data['erro'])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'CEP não encontrado'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Formatar resposta
    echo json_encode([
        'success' => true,
        'data' => [
            'cep' => $data['cep'] ?? '',
            'logradouro' => $data['logradouro'] ?? '',
            'complemento' => $data['complemento'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'cidade' => $data['localidade'] ?? '',
            'estado' => $data['uf'] ?? '',
            'ibge' => $data['ibge'] ?? ''
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Erro ao buscar CEP: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao consultar CEP. Tente novamente.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

