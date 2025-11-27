/**
 * API Client - Vigged
 * Funções para comunicação com APIs do backend
 */

const API_BASE_URL = 'api';

/**
 * Buscar vagas com filtros
 * @param {Object} filters Filtros de busca
 * @returns {Promise} Promise com dados das vagas
 */
async function buscarVagas(filters = {}) {
    const params = new URLSearchParams();
    
    if (filters.q) params.append('q', filters.q);
    if (filters.localizacao) params.append('localizacao', filters.localizacao);
    if (filters.tipo_contrato) params.append('tipo_contrato', filters.tipo_contrato);
    if (filters.destacada !== undefined) params.append('destacada', filters.destacada ? 1 : 0);
    if (filters.empresa) params.append('empresa', filters.empresa);
    if (filters.page) params.append('page', filters.page);
    if (filters.limit) params.append('limit', filters.limit);
    
    try {
        const response = await fetch(`${API_BASE_URL}/buscar_vagas.php?${params}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao buscar vagas:', error);
        return { success: false, error: 'Erro ao buscar vagas' };
    }
}

/**
 * Obter dados da empresa logada
 * @returns {Promise} Promise com dados da empresa
 */
async function obterDadosEmpresa() {
    try {
        const response = await fetch(`${API_BASE_URL}/dados_empresa.php`);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP ao obter dados da empresa:', response.status, errorText);
            return { success: false, error: `Erro ${response.status}: ${errorText.substring(0, 100)}` };
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Resposta não é JSON:', text.substring(0, 200));
            return { success: false, error: 'Resposta inválida do servidor' };
        }
        
        const data = await response.json();
        
        if (!data || typeof data !== 'object') {
            console.error('Dados inválidos recebidos:', data);
            return { success: false, error: 'Dados inválidos recebidos' };
        }
        
        return data;
    } catch (error) {
        console.error('Erro ao obter dados da empresa:', error);
        return { success: false, error: 'Erro ao carregar dados: ' + error.message };
    }
}

/**
 * Obter vagas da empresa logada
 * @param {string} status Status das vagas (ativa, pausada, encerrada, todas)
 * @returns {Promise} Promise com vagas da empresa
 */
async function obterVagasEmpresa(status = 'ativa') {
    try {
        const url = `${API_BASE_URL}/vagas_empresa.php?status=${status}`;
        console.log('Buscando vagas da empresa:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP ao obter vagas:', response.status, errorText);
            return { success: false, error: `Erro ${response.status}: ${errorText.substring(0, 100)}` };
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Resposta não é JSON:', text.substring(0, 200));
            return { success: false, error: 'Resposta inválida do servidor' };
        }
        
        const data = await response.json();
        
        if (!data || typeof data !== 'object') {
            console.error('Dados inválidos recebidos:', data);
            return { success: false, error: 'Dados inválidos recebidos' };
        }
        
        if (!data.success) {
            console.error('Erro na resposta da API:', data);
        } else {
            console.log('Vagas carregadas com sucesso:', data.data?.length || 0, 'vagas');
        }
        
        return data;
    } catch (error) {
        console.error('Erro ao obter vagas da empresa:', error);
        return { success: false, error: `Erro ao carregar vagas: ${error.message}` };
    }
}

/**
 * Publicar/Atualizar vaga
 * @param {Object} vagaData Dados da vaga
 * @returns {Promise} Promise com resultado
 */
async function salvarVaga(vagaData) {
    try {
        const formData = new FormData();
        formData.append('titulo', vagaData.titulo);
        formData.append('descricao', vagaData.descricao);
        formData.append('requisitos', vagaData.requisitos || '');
        formData.append('localizacao', vagaData.localizacao);
        formData.append('tipo_contrato', vagaData.tipo_contrato);
        formData.append('faixa_salarial', vagaData.faixa_salarial || '');
        formData.append('destacada', vagaData.destacada ? 'sim' : 'nao');
        
        if (vagaData.job_id) {
            formData.append('action', 'update');
            formData.append('job_id', vagaData.job_id);
        } else {
            formData.append('action', 'create');
        }
        
        const response = await fetch('processar_vaga.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao salvar vaga:', error);
        return { success: false, errors: ['Erro ao salvar vaga'] };
    }
}

/**
 * Obter dados do candidato PCD logado
 * @returns {Promise} Promise com dados do candidato
 */
async function obterDadosPCD() {
    try {
        const response = await fetch(`${API_BASE_URL}/dados_pcd.php`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter dados do PCD:', error);
        return { success: false, error: 'Erro ao carregar dados' };
    }
}

/**
 * Candidatar-se a uma vaga
 * @param {number} jobId ID da vaga
 * @param {string} mensagem Mensagem opcional
 * @param {File} curriculo Arquivo de currículo (opcional)
 * @returns {Promise} Promise com resultado
 */
async function candidatarVaga(jobId, mensagem = '', curriculo = null) {
    try {
        const formData = new FormData();
        formData.append('job_id', jobId);
        formData.append('mensagem', mensagem);
        if (curriculo) {
            formData.append('curriculo', curriculo);
        }
        
        const response = await fetch('processar_candidatura.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return { success: true };
        }
        
        // Se houver erro, será redirecionado com mensagem na sessão
        return { success: true };
    } catch (error) {
        console.error('Erro ao candidatar-se:', error);
        return { success: false, error: 'Erro ao processar candidatura' };
    }
}

/**
 * Admin - Listar usuários PCD
 * @param {Object} filters Filtros
 * @returns {Promise} Promise com lista de usuários
 */
async function adminListarUsuarios(filters = {}) {
    const params = new URLSearchParams();
    if (filters.status) params.append('status', filters.status);
    if (filters.page) params.append('page', filters.page);
    if (filters.limit) params.append('limit', filters.limit);
    
    try {
        const response = await fetch(`${API_BASE_URL}/admin_usuarios.php?action=list&${params}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao listar usuários:', error);
        return { success: false, error: 'Erro ao carregar usuários' };
    }
}

/**
 * Admin - Atualizar status de usuário
 * @param {number} userId ID do usuário
 * @param {string} status Novo status
 * @returns {Promise} Promise com resultado
 */
async function adminAtualizarStatusUsuario(userId, status) {
    try {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('status', status);
        
        const response = await fetch(`${API_BASE_URL}/admin_usuarios.php?action=update_status`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao atualizar status:', error);
        return { success: false, error: 'Erro ao atualizar status' };
    }
}

/**
 * Admin - Listar empresas
 * @param {Object} filters Filtros
 * @returns {Promise} Promise com lista de empresas
 */
async function adminListarEmpresas(filters = {}) {
    const params = new URLSearchParams();
    if (filters.status) params.append('status', filters.status);
    if (filters.page) params.append('page', filters.page);
    if (filters.limit) params.append('limit', filters.limit);
    
    try {
        const response = await fetch(`${API_BASE_URL}/admin_empresas.php?action=list&${params}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao listar empresas:', error);
        return { success: false, error: 'Erro ao carregar empresas' };
    }
}

/**
 * Admin - Atualizar status de empresa
 * @param {number} companyId ID da empresa
 * @param {string} status Novo status
 * @returns {Promise} Promise com resultado
 */
async function adminAtualizarStatusEmpresa(companyId, status) {
    try {
        const formData = new FormData();
        formData.append('company_id', companyId);
        formData.append('status', status);
        
        const response = await fetch(`${API_BASE_URL}/admin_empresas.php?action=update_status`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao atualizar status:', error);
        return { success: false, error: 'Erro ao atualizar status' };
    }
}

/**
 * Obter detalhes de uma vaga específica
 * @param {number} jobId ID da vaga
 * @returns {Promise} Promise com detalhes da vaga
 */
async function obterDetalhesVaga(jobId) {
    try {
        const response = await fetch(`${API_BASE_URL}/detalhes_vaga.php?id=${jobId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter detalhes da vaga:', error);
        return { success: false, error: 'Erro ao carregar vaga' };
    }
}

/**
 * Gerenciar vaga (pausar, ativar, encerrar, deletar)
 * @param {number} jobId ID da vaga
 * @param {string} action Ação (pausar, ativar, encerrar, deletar)
 * @returns {Promise} Promise com resultado
 */
async function gerenciarVaga(jobId, action) {
    try {
        const formData = new FormData();
        formData.append('job_id', jobId);
        formData.append('action', action);
        
        const response = await fetch(`${API_BASE_URL}/gerenciar_vaga.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao gerenciar vaga:', error);
        return { success: false, error: 'Erro ao processar ação' };
    }
}

/**
 * Obter candidaturas de uma vaga (empresa)
 * @param {number} jobId ID da vaga
 * @param {string} status Status das candidaturas (opcional)
 * @returns {Promise} Promise com candidaturas
 */
async function obterCandidaturasVaga(jobId, status = 'todas') {
    try {
        const params = new URLSearchParams();
        params.append('job_id', jobId);
        if (status !== 'todas') params.append('status', status);
        
        const response = await fetch(`${API_BASE_URL}/candidaturas_vaga.php?${params}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter candidaturas:', error);
        return { success: false, error: 'Erro ao carregar candidaturas' };
    }
}

/**
 * Gerenciar candidatura (aprovar, rejeitar, cancelar)
 * @param {number} applicationId ID da candidatura
 * @param {string} action Ação (aprovar, rejeitar, cancelar, em_analise)
 * @param {string} mensagem Mensagem opcional (para rejeição)
 * @returns {Promise} Promise com resultado
 */
async function gerenciarCandidatura(applicationId, action, mensagem = '') {
    try {
        const formData = new FormData();
        formData.append('application_id', applicationId);
        formData.append('action', action);
        if (mensagem) formData.append('mensagem', mensagem);
        
        const response = await fetch(`${API_BASE_URL}/gerenciar_candidatura.php`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao gerenciar candidatura:', error);
        return { success: false, error: 'Erro ao processar ação' };
    }
}

/**
 * Obter estatísticas
 * @param {string} type Tipo (general, company, admin)
 * @returns {Promise} Promise com estatísticas
 */
async function obterEstatisticas(type = 'general') {
    try {
        const response = await fetch(`${API_BASE_URL}/estatisticas.php?type=${type}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter estatísticas:', error);
        return { success: false, error: 'Erro ao carregar estatísticas' };
    }
}

/**
 * Obter configurações do sistema
 * @returns {Promise} Promise com configurações
 */
async function obterConfiguracoes() {
    try {
        const response = await fetch(`${API_BASE_URL}/admin_configuracoes.php?action=get`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter configurações:', error);
        return { success: false, error: 'Erro ao carregar configurações' };
    }
}

/**
 * Salvar configurações do sistema
 * @param {Object} settings Objeto com configurações
 * @returns {Promise} Promise com resultado
 */
async function salvarConfiguracoes(settings) {
    try {
        const response = await fetch(`${API_BASE_URL}/admin_configuracoes.php?action=save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(settings)
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao salvar configurações:', error);
        return { success: false, error: 'Erro ao salvar configurações' };
    }
}

/**
 * Obter informações do sistema
 * @returns {Promise} Promise com informações do sistema
 */
async function obterInfoSistema() {
    try {
        const response = await fetch(`${API_BASE_URL}/admin_configuracoes.php?action=system_info`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao obter informações do sistema:', error);
        return { success: false, error: 'Erro ao carregar informações' };
    }
}

/**
 * Limpar cache e logs
 * @returns {Promise} Promise com resultado
 */
async function limparCache() {
    try {
        const response = await fetch(`${API_BASE_URL}/admin_configuracoes.php?action=clear_cache`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao limpar cache:', error);
        return { success: false, error: 'Erro ao limpar cache' };
    }
}

/**
 * Atualizar perfil PCD
 * @param {Object} perfilData Dados do perfil
 * @returns {Promise} Promise com resultado
 */
async function atualizarPerfilPCD(perfilData) {
    try {
        const formData = new FormData();
        Object.keys(perfilData).forEach(key => {
            if (perfilData[key] !== null && perfilData[key] !== undefined) {
                if (key === 'recursos') {
                    perfilData[key].forEach(r => formData.append('recursos[]', r));
                } else {
                    formData.append(key, perfilData[key]);
                }
            }
        });
        
        const response = await fetch('processar_perfil_pcd.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return { success: true };
        }
        
        return { success: true };
    } catch (error) {
        console.error('Erro ao atualizar perfil:', error);
        return { success: false, error: 'Erro ao atualizar perfil' };
    }
}

/**
 * Atualizar perfil Empresa
 * @param {Object} perfilData Dados do perfil
 * @returns {Promise} Promise com resultado
 */
async function atualizarPerfilEmpresa(perfilData) {
    try {
        const formData = new FormData();
        Object.keys(perfilData).forEach(key => {
            if (perfilData[key] !== null && perfilData[key] !== undefined) {
                if (key === 'recursos_acessibilidade') {
                    perfilData[key].forEach(r => formData.append('recursos_acessibilidade[]', r));
                } else {
                    formData.append(key, perfilData[key]);
                }
            }
        });
        
        const response = await fetch('processar_perfil_empresa.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return { success: true };
        }
        
        return { success: true };
    } catch (error) {
        console.error('Erro ao atualizar perfil:', error);
        return { success: false, error: 'Erro ao atualizar perfil' };
    }
}

/**
 * Solicitar recuperação de senha
 * @param {string} email Email do usuário
 * @returns {Promise} Promise com resultado
 */
async function solicitarRecuperacaoSenha(email) {
    try {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('action', 'request');
        
        const response = await fetch('processar_recuperar_senha.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return { success: true };
        }
        
        return { success: true };
    } catch (error) {
        console.error('Erro ao solicitar recuperação:', error);
        return { success: false, error: 'Erro ao processar solicitação' };
    }
}

/**
 * Resetar senha com token
 * @param {string} token Token de recuperação
 * @param {string} newPassword Nova senha
 * @param {string} confirmPassword Confirmação da senha
 * @returns {Promise} Promise com resultado
 */
async function resetarSenha(token, newPassword, confirmPassword) {
    try {
        const formData = new FormData();
        formData.append('token', token);
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);
        formData.append('action', 'reset');
        
        const response = await fetch('processar_recuperar_senha.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return { success: true };
        }
        
        return { success: true };
    } catch (error) {
        console.error('Erro ao resetar senha:', error);
        return { success: false, error: 'Erro ao resetar senha' };
    }
}

/**
 * Buscar endereço por CEP
 * @param {string} cep CEP (apenas números ou com formatação)
 * @returns {Promise} Promise com dados do endereço
 */
async function buscarCep(cep) {
    // Remover formatação do CEP (apenas números)
    const cepLimpo = cep.replace(/\D/g, '');
    
    if (cepLimpo.length !== 8) {
        return { success: false, error: 'CEP deve conter 8 dígitos' };
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/buscar_cep.php?cep=${cepLimpo}`);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP:', response.status, errorText);
            return { success: false, error: `Erro ao buscar CEP: ${response.status}` };
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        return { success: false, error: 'Erro ao buscar CEP. Tente novamente.' };
    }
}

/**
 * Solicitar plano
 * @param {string} plano Nome do plano (essencial, profissional, enterprise)
 * @param {number} valor Valor do plano
 * @param {string} observacoes Observações opcionais
 * @returns {Promise} Promise com resultado da solicitação
 */
async function solicitarPlano(plano, valor, observacoes = '') {
    try {
        const response = await fetch(`${API_BASE_URL}/solicitar_plano.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                plano: plano.toLowerCase(),
                valor: valor,
                observacoes: observacoes
            })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP:', response.status, errorText);
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                errorData = { error: errorText || `Erro ${response.status}` };
            }
            return { success: false, error: errorData.error || 'Erro ao solicitar plano' };
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao solicitar plano:', error);
        return { success: false, error: 'Erro ao solicitar plano: ' + error.message };
    }
}

/**
 * Listar planos pendentes (apenas admin)
 * @returns {Promise} Promise com lista de planos pendentes
 */
async function listarPlanosPendentes() {
    try {
        const response = await fetch(`${API_BASE_URL}/planos_pendentes.php`);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP:', response.status, errorText);
            return { success: false, error: `Erro ao listar planos: ${response.status}` };
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao listar planos pendentes:', error);
        return { success: false, error: 'Erro ao listar planos pendentes' };
    }
}

/**
 * Aprovar ou rejeitar plano (apenas admin)
 * @param {number} requestId ID da solicitação
 * @param {string} acao 'aprovar' ou 'rejeitar'
 * @param {string} motivoRejeicao Motivo da rejeição (obrigatório se rejeitar)
 * @returns {Promise} Promise com resultado da ação
 */
async function gerenciarPlano(requestId, acao, motivoRejeicao = '') {
    try {
        const response = await fetch(`${API_BASE_URL}/gerenciar_plano.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                request_id: requestId,
                acao: acao,
                motivo_rejeicao: motivoRejeicao
            })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erro HTTP:', response.status, errorText);
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                errorData = { error: errorText || `Erro ${response.status}` };
            }
            return { success: false, error: errorData.error || 'Erro ao processar solicitação' };
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Erro ao gerenciar plano:', error);
        return { success: false, error: 'Erro ao processar solicitação: ' + error.message };
    }
}

/**
 * Excluir conta do usuário (PCD ou Empresa)
 * @returns {Promise} Promise com resultado da exclusão
 */
async function excluirConta() {
    try {
        const response = await fetch(`${API_BASE_URL}/excluir_conta.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Incluir cookies de sessão
        });
        
        const responseText = await response.text();
        console.log('Resposta da API (excluir conta):', response.status, responseText);
        
        if (!response.ok) {
            console.error('Erro HTTP ao excluir conta:', response.status, responseText);
            let errorData;
            try {
                errorData = JSON.parse(responseText);
            } catch (e) {
                errorData = { error: responseText || `Erro ${response.status}` };
            }
            return { 
                success: false, 
                error: errorData.error || 'Erro ao excluir conta',
                debug: errorData.debug || null
            };
        }
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Erro ao parsear JSON:', e, responseText);
            return { success: false, error: 'Resposta inválida do servidor' };
        }
        
        return data;
    } catch (error) {
        console.error('Erro ao excluir conta:', error);
        return { success: false, error: 'Erro ao processar solicitação: ' + error.message };
    }
}

// Exportar funções para uso global
window.ViggedAPI = {
    buscarVagas,
    obterDadosEmpresa,
    obterVagasEmpresa,
    salvarVaga,
    obterDadosPCD,
    candidatarVaga,
    adminListarUsuarios,
    adminAtualizarStatusUsuario,
    adminListarEmpresas,
    adminAtualizarStatusEmpresa,
    obterDetalhesVaga,
    gerenciarVaga,
    obterCandidaturasVaga,
    buscarCep,
    gerenciarCandidatura,
    obterEstatisticas,
    
    // Planos
    solicitarPlano,
    listarPlanosPendentes,
    gerenciarPlano,
    
    // Configurações
    obterConfiguracoes,
    salvarConfiguracoes,
    obterInfoSistema,
    limparCache,
    atualizarPerfilPCD,
    atualizarPerfilEmpresa,
    solicitarRecuperacaoSenha,
    resetarSenha,
    excluirConta
};

