<?php
/**
 * Funções Utilitárias
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Funções reutilizáveis para validação e sanitização
 */

/**
 * Sanitiza dados de entrada
 * @param string $data Dados a serem sanitizados
 * @return string Dados sanitizados
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida email
 * @param string $email Email a ser validado
 * @return bool True se válido, false caso contrário
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida CPF básico (formato)
 * @param string $cpf CPF a ser validado
 * @return bool True se formato válido, false caso contrário
 */
function validateCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return strlen($cpf) === 11;
}

/**
 * Valida CNPJ básico (formato)
 * @param string $cnpj CNPJ a ser validado
 * @return bool True se formato válido, false caso contrário
 */
function validateCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    return strlen($cnpj) === 14;
}

/**
 * Formata CPF (000.000.000-00)
 * @param string $cpf CPF sem formatação
 * @return string CPF formatado
 */
function formatCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

/**
 * Formata CNPJ (00.000.000/0000-00)
 * @param string $cnpj CNPJ sem formatação
 * @return string CNPJ formatado
 */
function formatCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) === 14) {
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }
    return $cnpj;
}

/**
 * Formata telefone ((00) 00000-0000)
 * @param string $phone Telefone sem formatação
 * @return string Telefone formatado
 */
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 11) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
    } elseif (strlen($phone) === 10) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
    }
    return $phone;
}

