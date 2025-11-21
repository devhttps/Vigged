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
        let allJobs = [];
        let displayedJobs = 3;
        let currentPage = 1;
        let currentFilters = {};

        // Load featured jobs from API
        async function loadFeaturedJobs() {
            try {
                // Primeiro, tentar buscar todas as vagas ativas (não apenas destacadas)
                const filters = {
                    limit: 20,
                    page: 1
                };
                
                const response = await buscarVagas(filters);
                console.log('Resposta da API:', response);
                
                if (response.success && response.data) {
                    // A API retorna data como array diretamente
                    allJobs = Array.isArray(response.data) ? response.data : [];
                    console.log('Vagas carregadas:', allJobs.length);
                    
                    // Filtrar vagas destacadas primeiro, se houver
                    const featuredJobs = allJobs.filter(job => job.destacada == 1);
                    if (featuredJobs.length > 0) {
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

        function loadFeaturedCompanies() {
            // Por enquanto, deixar vazio ou buscar empresas via API se necessário
            document.getElementById('featuredCompanies').innerHTML = '<p class="text-gray-500 text-center">Empresas em destaque serão exibidas aqui.</p>';
        }

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
