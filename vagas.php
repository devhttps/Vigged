<?php
// Configurar título da página
$title = 'Vagas';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Incluir head
include 'includes/head.php';

// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>

    <!-- Hero Section -->
    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h1 class="text-4xl md:text-5xl font-bold text-purple-600 mb-4">Encontre seu próximo emprego</h1>
            <p class="text-gray-600 text-lg mb-8">Acesse aqui milhares de vagas para PCDs</p>
            
            <!-- Search Bar -->
            <div class="flex items-center max-w-2xl mx-auto bg-white border-2 border-gray-300 rounded-full px-6 py-3 shadow-lg">
                <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="searchInput" placeholder="Buscar por título, cidades, palavras chaves..." class="flex-1 outline-none text-gray-700">
                <button onclick="searchJobs()" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition ml-4">Buscar</button>
            </div>
        </div>
    </section>

    <!-- Featured Companies -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-10 text-gray-800">Empresas em Destaque</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" id="featuredCompanies">
                <!-- Companies will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Featured Jobs -->
    <section class="py-16 bg-purple-600">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-10 text-white">Vagas em Destaque</h2>
            <div class="space-y-4" id="featuredJobs">
                <!-- Jobs will be loaded here -->
            </div>
            <div class="text-center mt-8">
                <button onclick="loadMoreJobs()" class="bg-white text-purple-600 px-8 py-3 rounded-full hover:bg-gray-100 transition font-semibold">Ver mais vagas</button>
            </div>
        </div>
    </section>

    <!-- Modal de Perfil da Empresa -->
    <div id="companyProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Perfil da Empresa</h3>
                    <button onclick="closeCompanyProfileModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="companyProfileContent">
                    <!-- Conteúdo será carregado aqui -->
                </div>
            </div>
        </div>
    </div>

    <!-- Accessibility Features -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Acessibilidade para Todos</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Vagas Inclusivas</h3>
                    <p class="text-gray-600">Todas as vagas são verificadas para garantir ambientes de trabalho acessíveis e inclusivos.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Suporte Especializado</h3>
                    <p class="text-gray-600">Oferecemos suporte personalizado durante todo o processo de candidatura.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Empresas Certificadas</h3>
                    <p class="text-gray-600">Trabalhamos apenas com empresas comprometidas com a diversidade e inclusão.</p>
                </div>
            </div>
        </div>
    </section>

<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
    <script src="assets/js/api.js"></script>
    <script>
        // Definir BASE_URL se não estiver definido
        const BASE_URL = '<?php echo BASE_URL; ?>';
        
        let allJobs = [];
        let displayedJobs = 3;
        let currentPage = 1;
        let currentFilters = {};

        // Verificar se há filtro de empresa na URL
        const urlParams = new URLSearchParams(window.location.search);
        const empresaId = urlParams.get('empresa');
        
        // Load featured jobs from API
        async function loadFeaturedJobs() {
            try {
                // Se houver filtro de empresa, buscar vagas dessa empresa
                let filters = {
                    limit: 20,
                    page: 1
                };
                
                // Se tiver empresaId na URL, adicionar filtro
                if (empresaId) {
                    filters.empresa = empresaId;
                    // Atualizar título da seção
                    const sectionTitle = document.querySelector('#featuredJobs').parentElement.querySelector('h2');
                    if (sectionTitle) {
                        sectionTitle.textContent = 'Vagas da Empresa';
                    }
                }
                
                const response = await buscarVagas(filters);
                console.log('Resposta da API:', response);
                
                if (response.success && response.data) {
                    // A API retorna data como array diretamente
                    allJobs = Array.isArray(response.data) ? response.data : [];
                    console.log('Vagas carregadas:', allJobs.length);
                    
                    // Filtrar vagas destacadas primeiro, se houver (e não estiver filtrando por empresa)
                    const featuredJobs = allJobs.filter(job => job.destacada == 1);
                    if (featuredJobs.length > 0 && !empresaId) {
                        displayJobs(featuredJobs.slice(0, displayedJobs));
                    } else {
                        displayJobs(allJobs.slice(0, displayedJobs));
                    }
                } else {
                    console.error('Erro na resposta:', response);
                    document.getElementById('featuredJobs').innerHTML = '<div class="text-center text-white text-lg">Nenhuma vaga disponível no momento.</div>';
                }
            } catch (error) {
                console.error('Erro ao carregar vagas:', error);
                document.getElementById('featuredJobs').innerHTML = '<div class="text-center text-white text-lg">Erro ao carregar vagas. Tente novamente mais tarde.</div>';
            }
        }

        function displayJobs(jobs) {
            const container = document.getElementById('featuredJobs');
            
            if (jobs.length === 0) {
                container.innerHTML = '<div class="text-center text-white text-lg">Nenhuma vaga encontrada.</div>';
                return;
            }
            
            container.innerHTML = jobs.map(job => {
                const companyName = job.empresa_nome || job.nome_fantasia || 'Empresa';
                const location = job.localizacao || 'Não especificado';
                const type = job.tipo_contrato || 'Não especificado';
                const salary = job.faixa_salarial || 'A combinar';
                
                return `
                <div class="bg-white rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
                    <div class="flex-1">
                            <h3 class="font-bold text-xl mb-2 text-gray-800">${job.titulo || 'Sem título'}</h3>
                            <p class="text-gray-600 mb-2">${companyName}</p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span><i class="fas fa-map-marker-alt mr-1"></i>${location}</span>
                            <span>•</span>
                                <span><i class="fas fa-briefcase mr-1"></i>${type}</span>
                            <span>•</span>
                                <span><i class="fas fa-dollar-sign mr-1"></i>${salary}</span>
                        </div>
                    </div>
                    <button onclick="viewJob(${job.id})" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition font-semibold">Ver vaga</button>
                </div>
                `;
            }).join('');
        }

        async function loadMoreJobs() {
            displayedJobs += 3;
            
            if (displayedJobs >= allJobs.length) {
                // Carregar mais vagas da API
                try {
                    const filters = {
                        ...currentFilters,
                        limit: 10,
                        page: currentPage + 1
                    };
                    
                    const response = await buscarVagas(filters);
                    if (response.success && response.data) {
                        const newJobs = Array.isArray(response.data) ? response.data : [];
                        allJobs = [...allJobs, ...newJobs];
                        currentPage++;
                        displayJobs(allJobs.slice(0, displayedJobs));
                    } else {
                        document.querySelector('button[onclick="loadMoreJobs()"]').style.display = 'none';
                    }
                } catch (error) {
                    console.error('Erro ao carregar mais vagas:', error);
                }
            } else {
                displayJobs(allJobs.slice(0, displayedJobs));
            }
            
            if (displayedJobs >= allJobs.length) {
                document.querySelector('button[onclick="loadMoreJobs()"]').style.display = 'none';
            }
        }

        async function searchJobs() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            
            if (!searchTerm) {
            loadFeaturedJobs();
                return;
            }
            
            try {
                currentFilters = { q: searchTerm, limit: 20, page: 1 };
                const response = await buscarVagas(currentFilters);
                
                if (response.success && response.data) {
                    allJobs = Array.isArray(response.data) ? response.data : [];
                    displayedJobs = Math.min(3, allJobs.length);
                    displayJobs(allJobs.slice(0, displayedJobs));
                    
                    if (allJobs.length <= 3) {
                        document.querySelector('button[onclick="loadMoreJobs()"]').style.display = 'none';
                    } else {
                        document.querySelector('button[onclick="loadMoreJobs()"]').style.display = 'block';
                    }
                } else {
                    document.getElementById('featuredJobs').innerHTML = '<div class="text-center text-white text-lg">Nenhuma vaga encontrada com os termos pesquisados.</div>';
                }
            } catch (error) {
                console.error('Erro ao buscar vagas:', error);
                document.getElementById('featuredJobs').innerHTML = '<div class="text-center text-white text-lg">Erro ao buscar vagas. Tente novamente.</div>';
            }
        }

        function viewJob(jobId) {
            // Redirecionar para página de detalhes da vaga ou candidatura
            window.location.href = `detalhes-vaga.php?id=${jobId}`;
        }

        async function loadFeaturedCompanies() {
            const container = document.getElementById('featuredCompanies');
            container.innerHTML = '<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div><p class="mt-4 text-gray-500">Carregando empresas...</p></div>';
            
            try {
                const response = await fetch('api/empresas_destaque.php?limit=6');
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    displayCompanies(result.data);
            } else {
                    container.innerHTML = '<p class="text-gray-500 text-center col-span-full py-8">Nenhuma empresa em destaque no momento.</p>';
                }
            } catch (error) {
                console.error('Erro ao carregar empresas em destaque:', error);
                container.innerHTML = '<p class="text-red-500 text-center col-span-full py-8">Erro ao carregar empresas. Tente novamente mais tarde.</p>';
            }
        }
        
        function displayCompanies(empresas) {
            const container = document.getElementById('featuredCompanies');
            
            // Placeholder SVG inline para evitar erro 404
            const placeholderSvg = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZTdlOWViIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkVtcHJlc2E8L3RleHQ+PC9zdmc+';
            
            container.innerHTML = empresas.map(empresa => {
                const logoUrl = empresa.logo || placeholderSvg;
                const nomeEmpresa = empresa.nome || 'Empresa';
                const localizacao = empresa.localizacao || 'Não especificado';
                
                return `
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer" onclick="viewCompanyJobs(${empresa.id})">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4 overflow-hidden border-2 border-purple-100 relative">
                                ${empresa.logo ? 
                                    `<img src="${logoUrl}" alt="${nomeEmpresa}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                     <div class="w-full h-full items-center justify-center hidden" style="display: none;">
                                        <i class="fas fa-building text-purple-600 text-3xl"></i>
                                     </div>` :
                                    `<i class="fas fa-building text-purple-600 text-3xl"></i>`
                                }
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">${nomeEmpresa}</h3>
                            <p class="text-sm text-gray-600 mb-2">${empresa.setor}</p>
                            ${localizacao !== 'Não especificado' ? 
                                `<p class="text-xs text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1"></i>${localizacao}
                                </p>` : ''
                            }
                            <div class="flex items-center justify-center space-x-4 text-sm mt-2">
                                <div class="flex items-center text-purple-600">
                                    <i class="fas fa-briefcase mr-1"></i>
                                    <span class="font-semibold">${empresa.vagas_ativas}</span>
                                    <span class="text-gray-500 ml-1">vaga${empresa.vagas_ativas !== 1 ? 's' : ''}</span>
                                </div>
                                ${empresa.total_candidaturas > 0 ? 
                                    `<div class="flex items-center text-gray-600">
                                        <i class="fas fa-users mr-1"></i>
                                        <span>${empresa.total_candidaturas}</span>
                                    </div>` : ''
                                }
                            </div>
                            <button class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                                Ver Vagas
                            </button>
                            </div>
                        </div>
                `;
            }).join('');
        }
        
        async function viewCompanyJobs(companyId) {
            // Abrir modal com perfil da empresa
            await openCompanyProfileModal(companyId);
        }
        
        async function openCompanyProfileModal(companyId) {
            const modal = document.getElementById('companyProfileModal');
            const modalContent = document.getElementById('companyProfileContent');
            
            // Mostrar loading
            modal.classList.remove('hidden');
            modalContent.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <p class="ml-4 text-gray-600">Carregando informações da empresa...</p>
                </div>
            `;
            
            try {
                const response = await fetch(`api/perfil_empresa_publico.php?id=${companyId}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    displayCompanyProfile(result.data);
                } else {
                    modalContent.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                            <p class="text-red-600 font-medium">${result.error || 'Erro ao carregar dados da empresa'}</p>
                            <button onclick="closeCompanyProfileModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Fechar
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao carregar perfil da empresa:', error);
                modalContent.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <p class="text-red-600 font-medium">Erro ao carregar dados da empresa</p>
                        <button onclick="closeCompanyProfileModal()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Fechar
                        </button>
                    </div>
                `;
            }
        }
        
        function displayCompanyProfile(empresa) {
            const modalContent = document.getElementById('companyProfileContent');
            const placeholderSvg = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgZmlsbD0iI2U3ZTllYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5Y2EzYWYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2dvPC90ZXh0Pjwvc3ZnPg==';
            const logoUrl = empresa.logo || placeholderSvg;
            
            let vagasHtml = '';
            if (empresa.vagas_recentes && empresa.vagas_recentes.length > 0) {
                vagasHtml = empresa.vagas_recentes.map(vaga => {
                    const date = new Date(vaga.created_at);
                    const dateStr = date.toLocaleDateString('pt-BR');
                    return `
                        <div class="border-l-4 border-purple-500 pl-4 py-2 hover:bg-gray-50 transition">
                            <h4 class="font-semibold text-gray-900">${vaga.titulo || 'Sem título'}</h4>
                            <div class="flex flex-wrap gap-2 mt-1 text-sm text-gray-600">
                                ${vaga.localizacao ? `<span><i class="fas fa-map-marker-alt mr-1"></i>${vaga.localizacao}</span>` : ''}
                                ${vaga.tipo_contrato ? `<span><i class="fas fa-briefcase mr-1"></i>${vaga.tipo_contrato}</span>` : ''}
                                ${vaga.faixa_salarial ? `<span><i class="fas fa-dollar-sign mr-1"></i>${vaga.faixa_salarial}</span>` : ''}
                                <span><i class="fas fa-calendar mr-1"></i>${dateStr}</span>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                vagasHtml = '<p class="text-gray-500 text-sm">Nenhuma vaga ativa no momento.</p>';
            }
            
            modalContent.innerHTML = `
                <div class="space-y-6">
                    <!-- Header da Empresa -->
                    <div class="flex items-start space-x-6">
                        <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border-2 border-purple-100 flex-shrink-0">
                            ${empresa.logo ? 
                                `<img src="${logoUrl}" alt="${empresa.nome}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                 <div class="w-full h-full items-center justify-center hidden" style="display: none;">
                                    <i class="fas fa-building text-purple-600 text-4xl"></i>
                                 </div>` :
                                `<i class="fas fa-building text-purple-600 text-4xl"></i>`
                            }
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">${empresa.nome}</h2>
                            <p class="text-purple-600 font-medium mb-2">${empresa.setor}</p>
                            ${empresa.endereco_completo ? 
                                `<p class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-map-marker-alt mr-2"></i>${empresa.endereco_completo}
                                </p>` : ''
                            }
                            ${empresa.website ? 
                                `<a href="${empresa.website}" target="_blank" class="text-sm text-purple-600 hover:text-purple-700">
                                    <i class="fas fa-globe mr-1"></i>${empresa.website}
                                </a>` : ''
                            }
                        </div>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">${empresa.vagas_ativas}</p>
                            <p class="text-sm text-gray-600">Vagas Ativas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">${empresa.total_vagas}</p>
                            <p class="text-sm text-gray-600">Total de Vagas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">${empresa.total_candidaturas}</p>
                            <p class="text-sm text-gray-600">Candidaturas</p>
                        </div>
                    </div>
                    
                    <!-- Sobre a Empresa -->
                    ${empresa.descricao ? 
                        `<div class="pt-4 border-t">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Sobre a Empresa</h3>
                            <p class="text-gray-700 text-sm leading-relaxed">${empresa.descricao}</p>
                        </div>` : ''
                    }
                    
                    <!-- Política de Inclusão -->
                    ${empresa.politica_inclusao ? 
                        `<div class="pt-4 border-t">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">
                                <i class="fas fa-heart text-purple-600 mr-2"></i>Política de Inclusão
                            </h3>
                            <p class="text-gray-700 text-sm leading-relaxed">${empresa.politica_inclusao}</p>
                        </div>` : ''
                    }
                    
                    ${empresa.ja_contrata_pcd ? 
                        `<div class="pt-4 border-t">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-green-900">Empresa que já contrata PCD</p>
                                        <p class="text-sm text-green-700">Esta empresa já possui experiência em contratação de pessoas com deficiência.</p>
                                    </div>
                                </div>
                            </div>
                        </div>` : ''
                    }
                    
                    <!-- Vagas Recentes -->
                    <div class="pt-4 border-t">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Vagas Ativas</h3>
                            <button onclick="window.location.href='vagas.php?empresa=${empresa.id}'" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                Ver todas <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            ${vagasHtml}
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="pt-4 border-t flex space-x-3">
                        <button onclick="window.location.href='vagas.php?empresa=${empresa.id}'" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-briefcase mr-2"></i>Ver Todas as Vagas
                        </button>
                        <button onclick="closeCompanyProfileModal()" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Fechar
                        </button>
                    </div>
                </div>
            `;
        }
        
        function closeCompanyProfileModal() {
            document.getElementById('companyProfileModal').classList.add('hidden');
        }
        
        // Se houver empresaId na URL, mostrar botão para limpar filtro
        if (empresaId) {
            document.addEventListener('DOMContentLoaded', function() {
                const searchBar = document.querySelector('.max-w-2xl.mx-auto');
                if (searchBar) {
                    const clearFilterBtn = document.createElement('button');
                    clearFilterBtn.className = 'mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-300 transition text-sm';
                    clearFilterBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Limpar filtro de empresa';
                    clearFilterBtn.onclick = function() {
                        window.location.href = 'vagas.php';
                    };
                    searchBar.appendChild(clearFilterBtn);
                }
            });
        }

        // Fechar modal de perfil da empresa ao clicar fora
        document.getElementById('companyProfileModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCompanyProfileModal();
            }
        });
        
        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('companyProfileModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeCompanyProfileModal();
                }
            }
        });

        // Permitir busca ao pressionar Enter
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchJobs();
            }
        });

        // Initialize page
        loadFeaturedCompanies();
        loadFeaturedJobs();
    </script>
</body>
</html>
