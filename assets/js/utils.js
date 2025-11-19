/**
 * Funções Utilitárias
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Funções auxiliares reutilizáveis em todo o sistema
 */

/**
 * Função de logout
 */
function logout() {
    if (confirm('Deseja realmente sair?')) {
        // Limpar localStorage
        localStorage.removeItem('currentUser');
        localStorage.removeItem('preRegistrationData');
        localStorage.removeItem('registrationData');
        localStorage.removeItem('companyData');
        localStorage.removeItem('companyJobs');
        localStorage.removeItem('companyPlan');
        
        // Redirecionar para página inicial
        window.location.href = 'index.php';
    }
}

/**
 * Exibe notificação de sucesso
 * @param {string} message Mensagem a ser exibida
 */
function showSuccess(message) {
    // Implementação simples - pode ser melhorada com biblioteca de notificações
    alert(message);
    // TODO: Implementar com biblioteca de toast (ex: Sonner)
}

/**
 * Exibe notificação de erro
 * @param {string} message Mensagem de erro
 */
function showError(message) {
    alert('Erro: ' + message);
    // TODO: Implementar com biblioteca de toast
}

/**
 * Valida email
 * @param {string} email Email a ser validado
 * @returns {boolean} True se válido
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Valida CPF básico (formato)
 * @param {string} cpf CPF a ser validado
 * @returns {boolean} True se formato válido
 */
function validateCPFFormat(cpf) {
    const cleaned = cpf.replace(/\D/g, '');
    return cleaned.length === 11;
}

/**
 * Valida CNPJ básico (formato)
 * @param {string} cnpj CNPJ a ser validado
 * @returns {boolean} True se formato válido
 */
function validateCNPJFormat(cnpj) {
    const cleaned = cnpj.replace(/\D/g, '');
    return cleaned.length === 14;
}

/**
 * Formata número de telefone para exibição
 * @param {string} phone Número de telefone
 * @returns {string} Telefone formatado
 */
function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 11) {
        return cleaned.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }
    return phone;
}

/**
 * Carrega dados do pré-cadastro do localStorage
 * @returns {Object|null} Dados do pré-cadastro ou null
 */
function loadPreRegistrationData() {
    try {
        const data = localStorage.getItem('preRegistrationData');
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Erro ao carregar dados do pré-cadastro:', error);
        return null;
    }
}

/**
 * Limpa dados do pré-cadastro do localStorage
 */
function clearPreRegistrationData() {
    localStorage.removeItem('preRegistrationData');
}

/**
 * Salva dados no localStorage
 * @param {string} key Chave
 * @param {Object} data Dados a serem salvos
 */
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
        console.error('Erro ao salvar no localStorage:', error);
        showError('Erro ao salvar dados. Tente novamente.');
    }
}

/**
 * Carrega dados do localStorage
 * @param {string} key Chave
 * @returns {Object|null} Dados ou null
 */
function loadFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Erro ao carregar do localStorage:', error);
        return null;
    }
}

/**
 * Preenche campos de formulário com dados
 * @param {Object} data Dados a serem preenchidos
 * @param {Object} fieldMap Mapeamento de campos (chave: id do campo)
 */
function fillFormFields(data, fieldMap) {
    Object.keys(fieldMap).forEach(key => {
        const fieldId = fieldMap[key];
        const field = document.getElementById(fieldId);
        if (field && data[key]) {
            field.value = data[key];
        }
    });
}

/**
 * Obtém dados do formulário
 * @param {HTMLFormElement} form Elemento formulário
 * @returns {Object} Dados do formulário
 */
function getFormData(form) {
    const formData = new FormData(form);
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (formData.getAll(key).length > 1) {
            // Múltiplos valores (ex: checkboxes)
            data[key] = formData.getAll(key);
        } else {
            data[key] = value;
        }
    }
    return data;
}

/**
 * Exibe nome do arquivo selecionado
 * @param {HTMLInputElement} fileInput Input de arquivo
 * @param {string} displayElementId ID do elemento para exibir o nome
 */
function displayFileName(fileInput, displayElementId) {
    fileInput.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Nenhum arquivo selecionado';
        const displayElement = document.getElementById(displayElementId);
        if (displayElement) {
            displayElement.textContent = fileName;
        }
    });
}

/**
 * Debounce function para otimizar eventos
 * @param {Function} func Função a ser executada
 * @param {number} wait Tempo de espera em ms
 * @returns {Function} Função com debounce
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Verifica se usuário está autenticado (localStorage)
 * @returns {boolean} True se autenticado
 */
function isAuthenticated() {
    const user = loadFromLocalStorage('currentUser');
    return user !== null && user !== undefined;
}

/**
 * Obtém usuário atual do localStorage
 * @returns {Object|null} Dados do usuário ou null
 */
function getCurrentUser() {
    return loadFromLocalStorage('currentUser');
}

