<?php
$company = 'Vigged';
$title = 'Política de Privacidade - ' . $company;

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
<body class="bg-gray-100 text-gray-900">
    <main class="max-w-5xl mx-auto p-6 mt-10 bg-white shadow-lg rounded-xl">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-purple-600">Política de Privacidade</h1>
            <p class="text-sm text-gray-600 mt-1">Última atualização: <?= date('d/m/Y') ?></p>
        </header>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">1. Introdução</h2>
            <p class="leading-relaxed">
                A <strong><?= htmlspecialchars($company) ?></strong> valoriza a sua privacidade e está
                comprometida com a proteção dos dados pessoais de pessoas com deficiência (PCDs),
                empresas e demais usuários que utilizam nossa plataforma de emprego inclusiva.
                Esta Política explica como coletamos, usamos e protegemos suas informações.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">2. Informações que Coletamos</h2>
            <p class="leading-relaxed mb-2">Coletamos dados para oferecer uma experiência segura e eficiente. Entre eles:</p>
            <ul class="list-disc pl-6 leading-relaxed">
                <li>Nome, e-mail, telefone e senha;</li>
                <li>Informações de perfil profissional e currículo;</li>
                <li>Dados relacionados a deficiências, quando informados voluntariamente;</li>
                <li>Histórico de candidaturas e interações dentro da plataforma;</li>
                <li>Dados técnicos como IP, navegador e registros de acesso.</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">3. Finalidade do Uso dos Dados</h2>
            <p class="leading-relaxed">Utilizamos seus dados para:</p>
            <ul class="list-disc pl-6 leading-relaxed">
                <li>Criar e gerenciar sua conta;</li>
                <li>Conectar candidatos PCD a vagas compatíveis;</li>
                <li>Permitir que empresas publiquem oportunidades de trabalho;</li>
                <li>Melhorar a experiência do usuário e personalizar recomendações;</li>
                <li>Garantir segurança, prevenir fraudes e cumprir obrigações legais.</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">4. Compartilhamento de Informações</h2>
            <p class="leading-relaxed">
                Seus dados podem ser compartilhados somente quando necessário, como:
            </p>
            <ul class="list-disc pl-6 leading-relaxed">
                <li>Com empresas anunciantes de vagas às quais você se candidata;</li>
                <li>Com prestadores de serviço que auxiliam na operação da plataforma;</li>
                <li>Com autoridades públicas, quando exigido por lei;</li>
                <li>Com parceiros de acessibilidade ou inclusão (quando autorizado).</li>
            </ul>
            <p class="leading-relaxed mt-2">
                Nunca vendemos seus dados pessoais.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">5. Direitos do Usuário (LGPD)</h2>
            <p class="leading-relaxed">De acordo com a Lei Geral de Proteção de Dados (LGPD), você possui direitos como:</p>
            <ul class="list-disc pl-6 leading-relaxed">
                <li>Acessar seus dados;</li>
                <li>Corrigir informações incorretas;</li>
                <li>Solicitar exclusão de dados;</li>
                <li>Revogar consentimentos;</li>
                <li>Solicitar portabilidade;</li>
                <li>Ser informado sobre compartilhamento.</li>
            </ul>
            <p class="leading-relaxed mt-2">
                Para exercer seus direitos, entre em contato pelo e-mail abaixo.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">6. Segurança dos Dados</h2>
            <p class="leading-relaxed">
                Implementamos medidas de segurança técnicas e administrativas para proteger seus dados
                contra acessos não autorizados, perda ou alteração. Apesar disso, nenhum sistema é
                100% seguro, mas trabalhamos continuamente para reforçar nossa proteção.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">7. Cookies e Tecnologias de Rastreamento</h2>
            <p class="leading-relaxed">
                Utilizamos cookies para melhorar a navegação, personalizar conteúdo e analisar o uso
                da plataforma. Você pode ajustar suas preferências de cookies no navegador.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">8. Retenção dos Dados</h2>
            <p class="leading-relaxed">
                Mantemos seus dados apenas pelo tempo necessário para cumprir com as finalidades desta
                Política, obrigações legais ou até que você solicite sua exclusão.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">9. Alterações na Política de Privacidade</h2>
            <p class="leading-relaxed">
                Esta Política pode ser atualizada periodicamente. Em caso de alterações significativas,
                você será avisado dentro da própria plataforma ou por e-mail.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-3">10. Contato</h2>
            <p class="leading-relaxed">
                Para solicitar informações, exercer seus direitos ou tirar dúvidas, entre em contato:
            </p>
            <p class="mt-2">
                <a href="mailto:privacidade@vigged.com" class="text-purple-600 underline">
                    privacidade@vigged.com
                </a>
            </p>
        </section>

        <footer class="mt-6 border-t pt-4 text-sm text-gray-600">
            <p>© <?= date('Y') ?> <?= htmlspecialchars($company) ?> — Plataforma de emprego inclusiva para PCD.</p>
        </footer>
    </main>
</body>
</html>