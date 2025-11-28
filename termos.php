<?php
$company = 'Vigged';
$title = 'Termos de Uso e Termos de Serviço - ' . $company;
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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 text-gray-800">
    <main class="max-w-5xl mx-auto p-6 mt-10 bg-white shadow-lg rounded-xl">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-purple-600">Termos de Uso e Termos de Serviço</h1>
            <p class="text-sm text-gray-600 mt-1">Última atualização: <?= date('d/m/Y') ?></p>
        </header>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">1. Introdução</h2>
            <p class="leading-relaxed">Bem-vindo à <strong><?= htmlspecialchars($company) ?></strong>, uma plataforma dedicada à conexão entre pessoas com deficiência (PCD) e oportunidades de emprego. Ao acessar e utilizar nossos serviços, você concorda com os termos estabelecidos neste documento, que visa garantir uma experiência segura, transparente e inclusiva para todos os usuários.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">2. Aceitação dos Termos</h2>
            <p class="leading-relaxed">Ao criar uma conta ou navegar pela plataforma, você confirma estar de acordo com estes Termos de Uso e Termos de Serviço. Caso não concorde com qualquer parte das condições apresentadas, pedimos que não utilize nossos serviços.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">3. Objetivo da Plataforma</h2>
            <p class="leading-relaxed">A <?= htmlspecialchars($company) ?> tem como principal propósito facilitar o acesso de pessoas com deficiência ao mercado de trabalho, conectando candidatos a empresas que oferecem vagas inclusivas, alinhadas às necessidades e capacidades de cada indivíduo.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">4. Cadastro e Responsabilidades do Usuário</h2>
            <ul class="list-disc pl-6 leading-relaxed">
                <li>Fornecer informações verdadeiras e atualizadas no cadastro;</li>
                <li>Manter a confidencialidade da senha e notificar imediatamente em caso de uso não autorizado;</li>
                <li>Utilizar a plataforma de forma respeitosa e de acordo com a legislação aplicável;</li>
                <li>Não publicar conteúdos discriminatórios, ofensivos ou que violem direitos de terceiros.</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">5. Responsabilidades da Plataforma</h2>
            <p class="leading-relaxed">A <?= htmlspecialchars($company) ?> atua como intermediadora, oferecendo ferramentas para divulgação de vagas e comunicação entre candidatos e empregadores. Não nos responsabilizamos por decisões de contratação nem por conteúdos publicados por terceiros, exceto quando houver violação comprovada destes termos.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">6. Acessibilidade e Inclusão</h2>
            <p class="leading-relaxed">Priorizamos a acessibilidade em nossos produtos e serviços. Caso encontre barreiras de uso, entre em contato pelo canal de suporte para que possamos corrigir e atender às suas necessidades.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">7. Privacidade e Proteção de Dados</h2>
            <p class="leading-relaxed">O tratamento de dados pessoais realizados pela <?= htmlspecialchars($company) ?> é regido pela nossa Política de Privacidade, que deve ser lida em conjunto com estes Termos. Ao aceitar estes Termos, você também concorda com as práticas descritas na Política de Privacidade.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">8. Conteúdo de Terceiros e Vagas</h2>
            <p class="leading-relaxed">Empresas e empregadores são responsáveis pelo conteúdo das vagas publicadas, incluindo requisitos, benefícios e informações sobre acessibilidade. Recomendamos que candidatos verifiquem as condições diretamente com o anunciante antes de aceitar propostas.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">9. Propriedade Intelectual</h2>
            <p class="leading-relaxed">Os conteúdos, marcas e materiais protegidos por direitos autorais disponibilizados na plataforma pertencem à <?= htmlspecialchars($company) ?> ou a seus licenciadores. É proibida a reprodução não autorizada desses materiais.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">10. Modificações dos Termos</h2>
            <p class="leading-relaxed">Podemos alterar estes Termos periodicamente. Quando mudanças importantes ocorrerem, nós notificaremos os usuários cadastrados por e-mail ou por aviso na plataforma. O uso contínuo após a publicação das alterações constitui aceite das novas condições.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">11. Rescisão</h2>
            <p class="leading-relaxed">Podemos suspender ou encerrar contas que violem estes Termos ou a legislação aplicável. Usuários também podem encerrar suas contas a qualquer momento seguindo o procedimento na área de configurações.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">12. Limitação de Responsabilidade</h2>
            <p class="leading-relaxed">Na máxima extensão permitida pela lei, a <?= htmlspecialchars($company) ?> não será responsável por danos indiretos, lucros cessantes ou perda de oportunidades decorrentes do uso da plataforma.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">13. Canal de Contato</h2>
            <p class="leading-relaxed">Para dúvidas, denúncias ou solicitações relacionadas a estes Termos, entre em contato através do e-mail: <a href="mailto:suporte@vigged.com" class="text-purple-600 underline">suporte@vigged.com</a>.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">14. Disposições Finais</h2>
            <p class="leading-relaxed">Estes Termos são regidos pelas leis do Brasil. Eventuais controvérsias serão dirimidas no foro da comarca do domicílio do usuário quando permitido por lei.</p>
        </section>

        <footer class="mt-6 border-t pt-4 text-sm text-gray-600">
            <p>© <?= date('Y') ?> <?= htmlspecialchars($company) ?> — Plataforma de emprego inclusiva para PCD.</p>
        </footer>
    </main>
</body>
</html>
