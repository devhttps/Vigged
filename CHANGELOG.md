# Changelog - Vigged

## [1.2.0] - Correções e Melhorias de UX

### ✅ Correções Implementadas

#### Campo "Requisitos" ao Publicar Vagas
- **Problema:** Campo "Requisitos" não estava sendo salvo ao publicar vagas
- **Solução:** 
  - Criada função `sanitizeTextarea()` para preservar quebras de linha
  - Campo agora usa sanitização apropriada para textarea
  - JavaScript melhorado para garantir envio do campo
  - Logs de debug adicionados

#### Erro ao Aprovar Candidatos
- **Problema:** Erro SQL "Unknown column 'feedback'" ao aprovar candidatos
- **Causa:** Migração não havia sido executada, colunas faltando no banco
- **Solução:**
  - Script de migração automatizado criado (`migrate_candidaturas.php`)
  - Verificação dinâmica de colunas na API
  - Tratamento de erros melhorado com mais detalhes
  - Documentação completa sobre migrações

### ✨ Novas Funcionalidades

#### Busca Automática de CEP
- **API de CEP:** `api/buscar_cep.php` criada
  - Integração com ViaCEP (API pública e gratuita)
  - Validação de CEP (8 dígitos)
  - Retorno formatado de dados de endereço
  
- **Integração no Perfil PCD:**
  - Busca automática ao digitar CEP completo
  - Preenchimento automático de campos (logradouro, bairro, cidade, estado)
  - Indicador visual de carregamento
  - Mensagens de sucesso/erro
  - Debounce de 500ms para otimizar requisições

#### Sistema de Migrações
- Script automatizado para atualizar estrutura do banco
- Verificação inteligente de colunas existentes
- Funciona via navegador ou linha de comando
- Documentação completa no README

### 📝 Documentação

- **README.md:** Seção completa sobre migrações adicionada
  - Quando executar migrações
  - Como executar (navegador ou CLI)
  - Troubleshooting de problemas comuns
  - Avisos de segurança

### 🔧 Melhorias Técnicas

- Função `sanitizeTextarea()` para campos de texto longo
- Verificação dinâmica de colunas no banco antes de usar
- Tratamento de erros HTTP melhorado no frontend
- Logs de debug mais detalhados

### 📦 Arquivos Criados

- `migrate_candidaturas.php` - Script de migração automatizado
- `api/buscar_cep.php` - API para busca de CEP
- `memory-bank/sessao-hoje.md` - Documentação desta sessão

### 📝 Arquivos Modificados

- `processar_vaga.php` - Função sanitizeTextarea(), tratamento melhorado
- `perfil-empresa.php` - JavaScript melhorado, tratamento de erros
- `api/atualizar_status_candidatura.php` - Verificação dinâmica, erros melhorados
- `assets/js/api.js` - Função buscarCep() adicionada
- `perfil-pcd.php` - Integração completa de busca de CEP
- `README.md` - Seção sobre migrações adicionada

---

## [1.1.0] - Melhorias e Consolidação

### ✅ Melhorias Implementadas

#### Consolidação de Código
- **includes/functions.php** criado com funções utilitárias centralizadas
  - sanitizeInput(), validateEmail(), validateCPF(), validateCNPJ()
  - formatCPF(), formatCNPJ(), formatPhone()
  - Redução de duplicação de código
  - Manutenção facilitada

#### Backend de Perfil PCD Completo
- **processar_perfil_pcd.php** completamente reescrito
  - Alteração de senha opcional com validação completa
  - Verificação de duplicação de email e CPF
  - Remoção automática de arquivos antigos ao fazer upload
  - Query dinâmica otimizada
  - Validações aprimoradas

#### Formulários de Cadastro
- Campos de senha adicionados em `cadastro.php` e `cadastro-empresa.php`
- Validação client-side e server-side de senhas
- Remoção de senhas temporárias automáticas
- Usuários agora definem sua própria senha no cadastro

#### Integração de Cadastro
- Formulário simplificado de cadastro integrado em `login.php`
- Tabs para alternar entre login e cadastro
- Redirecionamento inteligente baseado em tipo de usuário

#### Correções de Bugs
- Corrigido uso incorreto de `user_id` vs `id` em:
  - `processar_perfil_pcd.php`
  - `api/dados_pcd.php`
- Removida duplicação de código em múltiplos arquivos

### 📝 Notas

- Funções utilitárias agora centralizadas em `includes/functions.php`
- Todos os arquivos de processamento devem usar `includes/functions.php`
- Backend de perfil PCD agora suporta alteração de senha completa

---

## [1.0.0] - Backend Completo

### ✅ Implementado

#### Backend Completo
- Sistema de autenticação com RBAC
- 8 arquivos de processamento de formulários
- 11 APIs REST funcionais
- Sistema de vagas completo
- Sistema de candidaturas completo
- Gestão de perfis (PCD e Empresa)
- Recuperação de senha
- Painel administrativo
- Estatísticas e relatórios
- Instalador web automatizado

#### Segurança
- Prepared statements em todas as queries
- Hash de senhas com bcrypt
- Sessões seguras
- Sanitização de inputs
- Validação de uploads
- Proteção .htaccess

#### Documentação
- README.md completo com instruções XAMPP e aaPanel
- INSTALL.md detalhado
- Documentação de APIs
- Memory Bank completo

### 📝 Notas de Instalação

- **XAMPP**: Configuração padrão (root/senha vazia)
- **aaPanel**: Requer configuração de banco de dados personalizada
- **.htaccess**: Incluído com configurações de segurança
- **Uploads**: Diretórios devem ter permissão de escrita
- **Instalador Web**: Disponível em `/install` para instalação automatizada

### 🔒 Segurança

- Arquivo `config/database.php` não deve ser commitado (já no .gitignore)
- Senha padrão do admin deve ser alterada após instalação
- SSL recomendado em produção

