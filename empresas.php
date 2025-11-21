<?php
// Configurar título da página
$title = 'Empresas';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Estilos customizados para esta página
$additionalStyles = [
    '<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .popular-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .plan-card {
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.2);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            background: #7c3aed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
    </style>'
];

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>
    
    <!-- Hero Section -->
    <section class="bg-white py-16 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-bold text-purple-600 mb-6">Admita mais PCDs</h1>
            <p class="text-xl text-gray-700 mb-8 max-w-3xl mx-auto">
                Oferecemos planos personalizados para empresas divulgarem suas vagas inclusivas e encontrarem talentos PCD.
            </p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="cadastro-empresa.php" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition text-lg">
                    Publicar Vagas
                </a>
                <a href="cadastro-empresa.php" class="bg-white text-purple-600 border-2 border-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition text-lg">
                    Conhecer Planos
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Vigged Section -->
    <section class="bg-gray-50 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-purple-600 text-center mb-12">Por que escolher a Vigged?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Banco de Talentos PCD</h3>
                    <p class="text-gray-600">
                        Acesse os candidatos qualificados e escritos para trabalhar com sua empresa.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Relatórios Detalhados</h3>
                    <p class="text-gray-600">
                        Acompanhe o desempenho das suas vagas e otimize suas ações recrutadas.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Consultoria Especializada</h3>
                    <p class="text-gray-600">
                        Suporte mais com um consultoria de trabalho muito inclusiva e acessível.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans Section -->
    <section class="bg-gradient-to-br from-purple-600 to-purple-800 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-white text-center mb-12">Nossos Planos</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Plan 1 - Essencial -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center">Plano Essencial - Inclusão Consciente</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$199</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">3 vagas ativas por mês</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação vagas até 30 dias</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas destacadas em até 5 dias incluídas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Acesso a dados e relatórios sobre candidaturas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte técnico por email</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>

                <!-- Plan 2 - Profissional (Popular) -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl relative">
                    <div class="popular-badge">MAIS POPULAR</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center mt-4">Plano Profissional - Diversidade em Foco</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$399</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">10 vagas ativas por mês</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação vagas até 60 dias</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas em destaque na página principal</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Envio automático para vagas banco de talentos</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Relatórios mensais de desempenho das vagas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte por email e WhatsApp</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>

                <!-- Plan 3 - Enterprise -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center">Plano Enterprise - Inclusão Total</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$799</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas ilimitadas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação ilimitada (vagas ativas por até 90 dias)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Destaque premium com logomarca da empresa</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Acesso a consultoria de inclusão e Diversidade</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Relatórios avançados e segmentados</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte prioritário (WhatsApp e telefone direto)</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- Footer -->
    <footer class="bg-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">Vigged</h3>
                    <p class="text-purple-200 mb-4">
                        Conectando talentos PCD às melhores oportunidades do mercado.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-purple-200 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="hover:text-purple-200 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="hover:text-purple-200 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                        </a>
                        <a href="#" class="hover:text-purple-200 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Para Candidatos</h4>
                    <ul class="space-y-2 text-purple-200">
                        <li><a href="vagas.php" class="hover:text-white transition">Buscar Vagas</a></li>
                        <li><a href="pre-cadastro.php" class="hover:text-white transition">Criar Conta</a></li>
                        <li><a href="perfil-pcd.php" class="hover:text-white transition">Meu Perfil</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Para Empresas</h4>
                    <ul class="space-y-2 text-purple-200">
                        <li><a href="empresas.php" class="hover:text-white transition">Planos</a></li>
                        <li><a href="cadastro-empresa.php" class="hover:text-white transition">Cadastrar Empresa</a></li>
                        <li><a href="perfil-empresa.php" class="hover:text-white transition">Painel Empresa</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2 text-purple-200">
                        <li><a href="suporte.php" class="hover:text-white transition">Contato</a></li>
                        <li><a href="sobre-nos.php" class="hover:text-white transition">Sobre Nós</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Termos de Uso</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-purple-500 pt-8 text-center text-purple-200">
                <p>&copy; 2025 Vigged. Todos os direitos reservados.</p>
            </div>
        </div>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
