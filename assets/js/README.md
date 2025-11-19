# JavaScript - Scripts Consolidados

Esta pasta contém scripts JavaScript reutilizáveis consolidados de múltiplas páginas.

## Arquivos Disponíveis

### masks.js
Máscaras de formulário para campos brasileiros.

**Máscaras disponíveis:**
- CPF: `000.000.000-00`
- CNPJ: `00.000.000/0000-00`
- CEP: `00000-000`
- Telefone Celular: `(00) 00000-0000`
- Telefone Fixo: `(00) 0000-0000`
- Data: `DD/MM/AAAA`

**Uso:**
```html
<!-- O script inicializa automaticamente máscaras baseado em IDs comuns -->
<script src="assets/js/masks.js"></script>

<!-- OU aplicar manualmente -->
<script>
    const cpfInput = document.getElementById('cpf');
    applyCPFMask(cpfInput);
</script>
```

**IDs suportados automaticamente:**
- `cpf` - CPF
- `cnpj` - CNPJ
- `cep` - CEP
- `telefone`, `celular`, `telefone_responsavel` - Telefone celular
- `telefone_empresa` - Telefone fixo
- `data_nascimento`, `data_fundacao` - Data

### utils.js
Funções utilitárias reutilizáveis.

**Funções disponíveis:**
- `logout()` - Faz logout do usuário
- `showSuccess(message)` - Exibe notificação de sucesso
- `showError(message)` - Exibe notificação de erro
- `validateEmail(email)` - Valida formato de email
- `validateCPFFormat(cpf)` - Valida formato de CPF
- `validateCNPJFormat(cnpj)` - Valida formato de CNPJ
- `formatPhone(phone)` - Formata número de telefone
- `loadPreRegistrationData()` - Carrega dados do pré-cadastro
- `clearPreRegistrationData()` - Limpa dados do pré-cadastro
- `saveToLocalStorage(key, data)` - Salva dados no localStorage
- `loadFromLocalStorage(key)` - Carrega dados do localStorage
- `fillFormFields(data, fieldMap)` - Preenche campos de formulário
- `getFormData(form)` - Obtém dados do formulário
- `displayFileName(fileInput, displayElementId)` - Exibe nome do arquivo
- `debounce(func, wait)` - Função debounce
- `isAuthenticated()` - Verifica se usuário está autenticado
- `getCurrentUser()` - Obtém usuário atual

**Uso:**
```html
<script src="assets/js/utils.js"></script>
<script>
    // Exemplo: Validar email
    if (validateEmail('usuario@email.com')) {
        console.log('Email válido');
    }
    
    // Exemplo: Fazer logout
    document.getElementById('logoutBtn').addEventListener('click', logout);
    
    // Exemplo: Carregar dados do pré-cadastro
    const preData = loadPreRegistrationData();
    if (preData) {
        fillFormFields(preData, {
            nomeCompleto: 'nome',
            email: 'email',
            celular: 'telefone'
        });
    }
</script>
```

## Migração de Código Inline

Para migrar código JavaScript inline das páginas:

1. Identificar funções duplicadas
2. Mover para `masks.js` ou `utils.js` conforme apropriado
3. Remover código inline da página
4. Incluir script consolidado: `<script src="assets/js/nome-do-arquivo.js"></script>`

## Boas Práticas

- Sempre use os scripts consolidados em vez de duplicar código
- Adicione novas funções utilitárias em `utils.js`
- Adicione novas máscaras em `masks.js`
- Mantenha funções puras e reutilizáveis
- Documente funções complexas com comentários JSDoc

