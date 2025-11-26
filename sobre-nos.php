<?php
// Configurar título da página
$title = 'Sobre Nós';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Incluir head
include 'includes/head.php';

// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-purple-600 to-purple-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Sobre a Vigged</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed">
                Conectamos talentos PCD às melhores oportunidades do mercado, 
                promovendo inclusão real e transformando o futuro do trabalho.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-purple-600 mb-6">Nossa Missão</h2>
                    <p class="text-lg text-gray-700 leading-relaxed mb-4">
                        A Vigged nasceu com o propósito de quebrar barreiras e criar pontes entre 
                        profissionais com deficiência e empresas comprometidas com a diversidade e inclusão.
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Acreditamos que todos merecem oportunidades iguais de crescimento profissional, 
                        e trabalhamos todos os dias para tornar o mercado de trabalho mais acessível, 
                        justo e inclusivo.
                    </p>
                </div>
                <div class="bg-purple-100 rounded-2xl p-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">Inclusão Real</h3>
                                <p class="text-gray-600">Promovemos oportunidades genuínas de crescimento profissional</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">Diversidade</h3>
                                <p class="text-gray-600">Valorizamos a pluralidade e as diferentes perspectivas</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">Acessibilidade</h3>
                                <p class="text-gray-600">Garantimos que todos tenham acesso às melhores oportunidades</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-purple-600 mb-12">Nossos Valores</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Empatia</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Colocamos-nos no lugar do outro e entendemos as necessidades únicas de cada pessoa.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Transparência</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Mantemos comunicação clara e honesta com candidatos e empresas parceiras.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Excelência</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buscamos constantemente melhorar nossos serviços e superar expectativas.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-purple-600 mb-6">Faça Parte Dessa Transformação</h2>
            <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                Seja você um profissional PCD em busca de oportunidades ou uma empresa 
                comprometida com a inclusão, a Vigged é o lugar certo para você.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="pre-cadastro.php" class="px-8 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-lg font-semibold">
                    Cadastre-se como Candidato
                </a>
                <a href="cadastro-empresa.php" class="px-8 py-4 bg-white text-purple-600 border-2 border-purple-600 rounded-lg hover:bg-purple-50 transition text-lg font-semibold">
                    Cadastre sua Empresa
                </a>
            </div>
        </div>
    </section>

<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
