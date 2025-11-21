<?php
/**
 * Head comum para todas as páginas
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * @param string $title Título da página (opcional, padrão: "Vigged")
 * @param array $additionalStyles Estilos adicionais (opcional)
 */
$pageTitle = isset($title) ? $title . ' - Vigged' : 'Vigged - Inclusão e Oportunidades Reais';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (isset($additionalStyles) && is_array($additionalStyles)): ?>
        <?php foreach ($additionalStyles as $style): ?>
            <?php echo $style; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">

