<?php
// Configurar título da página
$title = 'Detalhes da Vaga';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Obter ID da vaga
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    header('Location: vagas.php');
    exit;
}

// Incluir head
include 'includes/head.php';

// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>

    <!-- Job Details Section -->
    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-6">
            <div id="job-details" class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form Section (only for PCD users) -->
    <?php if (isAuthenticated() && isPCD()): ?>
    <section class="bg-gray-50 py-16">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Candidatar-se para esta vaga</h2>
                <form id="application-form" class="space-y-6">
                    <input type="hidden" id="job-id" name="job_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem de Apresentação</label>
                        <textarea id="application-message" name="mensagem" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Conte-nos por que você é a pessoa ideal para esta vaga..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currículo (PDF)</label>
                        <input type="file" id="application-resume" name="curriculo" accept=".pdf" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <p class="text-sm text-gray-500 mt-1">Tamanho máximo: 5MB</p>
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                            Enviar Candidatura
                        </button>
                        <a href="vagas.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php
// Incluir footer padrão
include 'includes/footer.php';
?>

    <script src="assets/js/api.js"></script>
    <script>
        const jobId = <?php echo $job_id; ?>;
        let jobData = null;
        let hasApplied = false;

        // Aguardar carregamento completo da página
        document.addEventListener('DOMContentLoaded', function() {
            // Aguardar um pouco para garantir que api.js foi carregado
            setTimeout(loadJobDetails, 100);
        });

        // Carregar detalhes da vaga
        async function loadJobDetails() {
            try {
                console.log('Carregando detalhes da vaga ID:', jobId);
                
                // Verificar se a função existe, se não, usar fetch direto
                let response;
                if (typeof obterDetalhesVaga !== 'undefined') {
                    console.log('Usando função obterDetalhesVaga');
                    response = await obterDetalhesVaga(jobId);
                } else {
                    console.log('Função obterDetalhesVaga não encontrada, usando fetch direto');
                    const fetchResponse = await fetch(`api/detalhes_vaga.php?id=${jobId}`);
                    if (!fetchResponse.ok) {
                        throw new Error(`HTTP error! status: ${fetchResponse.status}`);
                    }
                    response = await fetchResponse.json();
                }
                
                console.log('Resposta da API:', response);
                
                if (response.success && response.data) {
                    jobData = response.data;
                    displayJobDetails(jobData);
                    
                    // Se usuário PCD está logado, verificar se já se candidatou
                    <?php if (isAuthenticated() && isPCD()): ?>
                    await checkApplication();
                    <?php endif; ?>
                } else {
                    const errorMsg = response.error || 'Vaga não encontrada';
                    showError('Vaga não encontrada', errorMsg);
                }
            } catch (error) {
                console.error('Erro ao carregar detalhes da vaga:', error);
                showError('Erro ao carregar vaga', 'Ocorreu um erro ao carregar os detalhes da vaga. Verifique o console para mais detalhes. Erro: ' + error.message);
            }
        }
        
        function showError(title, message) {
            document.getElementById('job-details').innerHTML = `
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">${title}</h2>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <a href="vagas.php" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-block">
                        Voltar para Vagas
                    </a>
                </div>
            `;
        }

        function displayJobDetails(job) {
            const container = document.getElementById('job-details');
            const companyName = job.empresa_nome || job.empresa_razao_social || 'Empresa';
            const location = job.localizacao || 'Não especificado';
            const type = job.tipo_contrato || 'Não especificado';
            const salary = job.faixa_salarial || 'A combinar';
            const views = job.visualizacoes || 0;
            const applications = job.total_candidaturas || 0;
            
            // Formatar data
            const createdDate = job.created_at ? new Date(job.created_at).toLocaleDateString('pt-BR') : '';
            
            container.innerHTML = `
                <div class="mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h1 class="text-4xl font-bold text-gray-800 mb-2">${job.titulo || 'Sem título'}</h1>
                            <p class="text-xl text-purple-600 mb-4">${companyName}</p>
                            <div class="flex items-center space-x-6 text-sm text-gray-600">
                                <span><i class="fas fa-map-marker-alt mr-2"></i>${location}</span>
                                <span><i class="fas fa-briefcase mr-2"></i>${type}</span>
                                <span><i class="fas fa-dollar-sign mr-2"></i>${salary}</span>
                                <span><i class="fas fa-eye mr-2"></i>${views} visualizações</span>
                            </div>
                        </div>
                        ${job.destacada == 1 ? '<span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-semibold">Destaque</span>' : ''}
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Publicada em</h3>
                            <p class="text-gray-800">${createdDate}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Candidaturas</h3>
                            <p class="text-gray-800">${applications} candidato(s)</p>
                        </div>
                    </div>
                </div>
                
                <div class="prose max-w-none mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Sobre a Vaga</h2>
                    <div class="text-gray-700 whitespace-pre-wrap">${job.descricao || 'Sem descrição disponível.'}</div>
                </div>
                
                ${job.requisitos ? `
                <div class="prose max-w-none mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Requisitos</h2>
                    <div class="text-gray-700 whitespace-pre-wrap">${job.requisitos}</div>
                </div>
                ` : ''}
                
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Sobre a Empresa</h2>
                    <div class="text-gray-700">
                        ${job.empresa_descricao || 'Informações da empresa não disponíveis.'}
                    </div>
                    ${job.empresa_setor ? `<p class="mt-2 text-sm text-gray-600"><strong>Setor:</strong> ${job.empresa_setor}</p>` : ''}
                    ${job.empresa_cidade && job.empresa_estado ? `<p class="text-sm text-gray-600"><strong>Localização:</strong> ${job.empresa_cidade}, ${job.empresa_estado}</p>` : ''}
                </div>
                
                <div class="mt-8 flex space-x-4">
                    <a href="vagas.php" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Voltar para Vagas
                    </a>
                </div>
            `;
            
            // Preencher campo hidden do formulário de candidatura
            <?php if (isAuthenticated() && isPCD()): ?>
            document.getElementById('job-id').value = job.id;
            <?php endif; ?>
        }

        <?php if (isAuthenticated() && isPCD()): ?>
        // Verificar se usuário já se candidatou
        async function checkApplication() {
            try {
                const response = await fetch(`api/verificar_candidatura.php?job_id=${jobId}`);
                const data = await response.json();
                
                if (data.success && data.ja_candidatou) {
                    hasApplied = true;
                    const candidatura = data.candidatura;
                    const form = document.getElementById('application-form');
                    
                    // Formatar data e hora
                    const dataCandidatura = new Date(candidatura.created_at);
                    const dataFormatada = dataCandidatura.toLocaleDateString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    const horaFormatada = dataCandidatura.toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Status com cores
                    const statusColors = {
                        'pendente': 'bg-yellow-100 text-yellow-800',
                        'em_analise': 'bg-blue-100 text-blue-800',
                        'aprovada': 'bg-green-100 text-green-800',
                        'rejeitada': 'bg-red-100 text-red-800',
                        'cancelada': 'bg-gray-100 text-gray-800'
                    };
                    const statusColor = statusColors[candidatura.status] || 'bg-gray-100 text-gray-800';
                    
                    // Avaliação por estrelas se houver
                    let starsHtml = '';
                    if (candidatura.avaliacao) {
                        starsHtml = '<div class="flex items-center mt-2"><span class="text-sm text-gray-600 mr-2">Avaliação:</span>';
                        for (let i = 1; i <= 5; i++) {
                            starsHtml += `<i class="fas fa-star ${i <= candidatura.avaliacao ? 'text-yellow-400' : 'text-gray-300'}"></i>`;
                        }
                        starsHtml += '</div>';
                    }
                    
                    form.innerHTML = `
                        <div class="bg-green-50 border-2 border-green-400 rounded-lg p-6 shadow-lg">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 text-3xl mr-4 mt-1"></i>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                                        <span class="bg-green-600 text-white px-4 py-2 rounded-lg mr-3">✓</span>
                                        Você já se candidatou para esta vaga!
                                    </h3>
                                    
                                    <div class="bg-white rounded-lg p-4 border border-green-200 mb-4">
                                        <p class="text-base font-semibold text-green-700 mb-2">
                                            <i class="fas fa-calendar-check mr-2"></i>
                                            Data e hora da candidatura:
                                        </p>
                                        <p class="text-lg font-bold text-green-800">
                                            ${dataFormatada} às ${horaFormatada}
                                        </p>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm">
                                        
                                        <div class="flex items-center">
                                            <span class="font-medium text-gray-700 mr-2">Status atual:</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColor}">
                                                ${candidatura.status_label}
                                            </span>
                                        </div>
                                        
                                        ${candidatura.mensagem ? 
                                            `<div class="mt-3">
                                                <span class="font-medium text-gray-700 block mb-1">Sua mensagem:</span>
                                                <p class="text-gray-700 bg-white p-3 rounded border border-gray-200">${candidatura.mensagem}</p>
                                            </div>` : ''
                                        }
                                        
                                        ${candidatura.feedback ? 
                                            `<div class="mt-3">
                                                <span class="font-medium text-gray-700 block mb-1">Feedback da empresa:</span>
                                                <p class="text-red-700 bg-red-50 p-3 rounded border border-red-200">${candidatura.feedback}</p>
                                            </div>` : ''
                                        }
                                        
                                        ${starsHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex space-x-4">
                            <a href="vagas.php" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar para Vagas
                            </a>
                            <a href="perfil-pcd.php" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold shadow-md">
                                <i class="fas fa-user mr-2"></i>Ver Minhas Candidaturas
                            </a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao verificar candidatura:', error);
            }
        }

        // Enviar candidatura
        document.getElementById('application-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Verificar novamente antes de enviar
            if (hasApplied) {
                alert('Você já se candidatou para esta vaga. Verifique as informações acima.');
                return;
            }
            
            // Verificar uma última vez antes de enviar
            try {
                const checkResponse = await fetch(`api/verificar_candidatura.php?job_id=${jobId}`);
                const checkData = await checkResponse.json();
                
                if (checkData.success && checkData.ja_candidatou) {
                    alert('Você já se candidatou para esta vaga. A página será atualizada.');
                    location.reload();
                    return;
                }
            } catch (error) {
                console.error('Erro ao verificar candidatura antes de enviar:', error);
            }
            
            const formData = new FormData();
            formData.append('job_id', document.getElementById('job-id').value);
            formData.append('mensagem', document.getElementById('application-message').value);
            
            const resumeFile = document.getElementById('application-resume').files[0];
            if (resumeFile) {
                formData.append('curriculo', resumeFile);
            }
            
            try {
                // Mostrar loading
                const submitButton = e.target.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
                
                const response = await fetch('processar_candidatura.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Resposta da API:', result);
                
                if (result.success) {
                    alert('Candidatura enviada com sucesso!');
                    // Recarregar página para mostrar mensagem de sucesso
                    window.location.href = 'detalhes-vaga.php?id=' + jobId;
                } else {
                    const errorMsg = result.error || result.errors?.join(' ') || 'Erro desconhecido';
                    alert('Erro ao enviar candidatura: ' + errorMsg);
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            } catch (error) {
                console.error('Erro ao enviar candidatura:', error);
                alert('Erro ao enviar candidatura. Verifique o console para mais detalhes.');
                const submitButton = e.target.querySelector('button[type="submit"]');
                submitButton.disabled = false;
                submitButton.textContent = 'Enviar Candidatura';
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

