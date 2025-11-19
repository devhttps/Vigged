/**
 * Máscaras de Formulário
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Funções reutilizáveis para aplicar máscaras em campos de formulário
 */

/**
 * Aplica máscara de CPF (000.000.000-00)
 * @param {HTMLInputElement} input Elemento input
 */
function applyCPFMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        }
    });
}

/**
 * Aplica máscara de CNPJ (00.000.000/0000-00)
 * @param {HTMLInputElement} input Elemento input
 */
function applyCNPJMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        }
    });
}

/**
 * Aplica máscara de CEP (00000-000)
 * @param {HTMLInputElement} input Elemento input
 */
function applyCEPMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 8) {
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        }
    });
}

/**
 * Aplica máscara de telefone celular (00) 00000-0000
 * @param {HTMLInputElement} input Elemento input
 */
function applyPhoneMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (value.length > 0) {
            value = value.replace(/^(\d*)/, '($1');
        }
        
        e.target.value = value;
    });
}

/**
 * Aplica máscara de telefone fixo (00) 0000-0000
 * @param {HTMLInputElement} input Elemento input
 */
function applyLandlineMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        }
    });
}

/**
 * Aplica máscara de data (DD/MM/AAAA)
 * @param {HTMLInputElement} input Elemento input
 */
function applyDateMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 8) {
            value = value.replace(/(\d{2})(\d)/, '$1/$2');
            value = value.replace(/(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');
            e.target.value = value;
        }
    });
}

/**
 * Inicializa máscaras automaticamente baseado em IDs comuns
 * Chame esta função quando a página carregar
 */
function initMasks() {
    // CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) applyCPFMask(cpfInput);
    
    // CNPJ
    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) applyCNPJMask(cnpjInput);
    
    // CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) applyCEPMask(cepInput);
    
    // Telefones (celular)
    const phoneInputs = document.querySelectorAll('#telefone, #celular, #telefone_responsavel');
    phoneInputs.forEach(input => applyPhoneMask(input));
    
    // Telefones (fixo)
    const landlineInputs = document.querySelectorAll('#telefone_empresa');
    landlineInputs.forEach(input => applyLandlineMask(input));
    
    // Data
    const dateInputs = document.querySelectorAll('#data_nascimento, #data_fundacao');
    dateInputs.forEach(input => applyDateMask(input));
}

// Auto-inicializa quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMasks);
} else {
    initMasks();
}

