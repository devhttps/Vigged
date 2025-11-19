# Includes - Componentes Reutilizáveis

Esta pasta contém componentes PHP reutilizáveis que devem ser incluídos nas páginas do projeto.

## Arquivos Disponíveis

### head.php
Cabeçalho HTML comum com meta tags, Tailwind CSS e Google Fonts.

**Uso:**
```php
<?php
$title = "Nome da Página"; // Opcional
$additionalStyles = []; // Opcional - array de strings HTML com estilos adicionais
include 'includes/head.php';
?>
```

### nav.php
Navegação comum do site. Suporta dois modos: público e autenticado.

**Uso:**
```php
<?php
// Navegação pública (padrão)
include 'includes/nav.php';

// OU navegação autenticada (com botão Sair)
$navType = 'authenticated';
include 'includes/nav.php';
?>
```

### footer.php
Rodapé comum do site. Suporta dois estilos: roxo (padrão) e escuro.

**Uso:**
```php
<?php
// Footer roxo (padrão)
include 'includes/footer.php';

// OU footer escuro
$footerStyle = 'dark';
include 'includes/footer.php';
?>
```

## Exemplo Completo de Página

```php
<?php
// Definir título da página
$title = "Minha Página";

// Incluir head
include 'includes/head.php';

// Incluir navegação (pública ou autenticada)
$navType = 'public'; // ou 'authenticated'
include 'includes/nav.php';
?>

<!-- Conteúdo da página aqui -->

<?php
// Incluir footer
include 'includes/footer.php';
?>
```

## Migração de Páginas Existentes

Para migrar uma página existente:

1. Remover o código duplicado do `<head>`
2. Substituir por `include 'includes/head.php'`
3. Remover o código duplicado da `<nav>`
4. Substituir por `include 'includes/nav.php'`
5. Remover o código duplicado do `<footer>`
6. Substituir por `include 'includes/footer.php'`
7. Adicionar scripts JavaScript consolidados antes do `</body>`

## Scripts JavaScript Consolidados

Após incluir o footer, adicione os scripts consolidados:

```php
<script src="assets/js/masks.js"></script>
<script src="assets/js/utils.js"></script>
```

Os scripts são auto-inicializados quando o DOM estiver pronto.

