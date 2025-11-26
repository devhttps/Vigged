<?php
// Configurar título da página
$title = 'Inclusão e Oportunidades Reais';

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold text-purple-600 mb-6 leading-tight">
                        Inclusão e Oportunidades Reais
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Conectamos talentos PCD às melhores oportunidades do mercado. Aqui, você encontra empresas comprometidas com a diversidade e acessibilidade. Cadastre-se e transforme seu futuro profissional com segurança e facilidade.
                    </p>
                </div>
                <div class="flex justify-center">
                    <img src="garoto.png" alt="Profissional PCD" class="max-w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-gradient-to-br from-purple-600 to-purple-800 py-16 px-6"">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Recursos que Facilitam Sua Jornada Profissional</h2>
                <p class="text-purple-100 max-w-3xl mx-auto">
                    Nossas funcionalidades foram pensadas para tornar sua busca por oportunidades mais acessível, prática e inclusiva. Conecte-se com o mercado de forma simples e segura.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white text-gray-800 rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-purple-600 mb-4">Busca de Vagas Acessível</h3>
                    <p class="text-gray-600">
                        Encontre oportunidades que respeitam suas necessidades. Nossa plataforma garante uma navegação adaptada para que você encontre a vaga ideal de forma fácil e eficiente.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white text-gray-800 rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-purple-600 mb-4">Programa de Inclusão Ativa</h3>
                    <p class="text-gray-600">
                        Tenha acesso a empresas comprometidas com a diversidade e inclusão. Baseado em um sistema que valoriza seu potencial.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white text-gray-800 rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-handshake text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-purple-600 mb-4">Conexões Sem Barreiras</h3>
                    <p class="text-gray-600">
                        Cadastre-se e tenha acesso a processos seletivos de forma 100% online, com acesso e garantia em todas as etapas.
                    </p>
                </div>
            </div>
        </div>
    </section>

<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
