# Vigged - Plataforma de Inclusão e Oportunidades

## 📋 Sobre o Projeto

Vigged é uma plataforma web desenvolvida como Trabalho de Conclusão de Curso (TCC) que conecta profissionais PCD (Pessoas com Deficiência) às melhores oportunidades de trabalho do mercado brasileiro.

A plataforma foi desenvolvida com base no **Programa PCD**, focando em inclusão real, acessibilidade e conformidade com a legislação brasileira sobre inclusão de pessoas com deficiência.

## 🎯 Objetivo

Promover inclusão real no mercado de trabalho, conectando talentos PCD com empresas comprometidas com diversidade e acessibilidade, seguindo as diretrizes e melhores práticas do Programa PCD.

## ✨ Funcionalidades

### Para Candidatos PCD
- ✅ Busca de vagas com filtros acessíveis (integrado com API)
- ✅ Cadastro completo de perfil profissional
- ✅ Informações sobre deficiência, CID e laudos médicos
- ✅ Aplicação para vagas de forma simplificada
- ✅ Acompanhamento de candidaturas
- ✅ Perfil profissional completo com edição:
  - Foto de perfil (com redimensionamento automático)
  - Informações pessoais (nome, email, telefone, endereço)
  - Sobre, habilidades, formação acadêmica
  - Experiências profissionais
  - Upload de currículo
  - Alteração de senha

### Para Empresas
- ✅ Cadastro e gestão de perfil empresarial (integrado com API)
- ✅ Publicação de vagas inclusivas (com requisitos e detalhes)
- ✅ Sistema de planos (Gratuito, Essencial, Profissional, Enterprise)
- ✅ Gestão de candidatos e processos seletivos
  - Visualização de candidaturas por vaga
  - Aprovação/rejeição de candidatos
  - Gerenciamento de status de candidaturas
- ✅ Recursos de acessibilidade da empresa
- ✅ Estatísticas e relatórios (dashboard integrado)
- ✅ Gerenciamento completo de vagas (criar, editar, pausar, ativar, encerrar)

### Para Administradores
- ✅ Gestão completa de usuários e empresas (integrado com APIs)
  - Listagem com filtros e paginação
  - Atualização de status (ativo/inativo/pendente)
  - Visualização de detalhes
- ✅ Moderação de conteúdo
- ✅ Validação de documentos e laudos
- ✅ Análise e relatórios da plataforma
- ✅ Dashboard com métricas importantes em tempo real:
  - Total de usuários PCD
  - Total de empresas cadastradas
  - Total de vagas publicadas
  - Total de candidaturas
  - Registros recentes

## 🛠️ Tecnologias

- **Backend**: PHP (server-side rendering) ✅ Completo
- **Frontend**: HTML5, Tailwind CSS, JavaScript (Vanilla) ✅ Integrado
- **Banco de Dados**: MySQL/MariaDB ✅ Implementado
- **APIs**: REST APIs com JSON ✅ 11 endpoints funcionais
- **Segurança**: PDO, Prepared Statements, Hash de Senhas, CSRF Protection ✅
- **Cliente API**: JavaScript modular (`assets/js/api.js`) ✅ Completo
- **Futuro**: Possível migração para Next.js/React

## 📁 Estrutura do Projeto

```
Vigged/
├── includes/          # Componentes reutilizáveis (head.php, nav.php, footer.php)
│   ├── functions.php  # Funções utilitárias centralizadas
│   └── image-utils.php # Utilitários de processamento de imagens
├── assets/           # Recursos estáticos
│   ├── js/
│   │   ├── api.js     # Cliente JavaScript para todas as APIs REST
│   │   ├── masks.js   # Máscaras de input (CPF, CNPJ, telefone)
│   │   └── utils.js   # Utilitários JavaScript
│   └── css/          # Estilos customizados
├── api/              # Endpoints REST (11 APIs)
│   ├── buscar_vagas.php
│   ├── dados_pcd.php
│   ├── dados_empresa.php
│   ├── admin_usuarios.php
│   ├── admin_empresas.php
│   └── ... (outros endpoints)
├── config/           # Arquivos de configuração
│   ├── database.php  # Conexão com banco de dados
│   ├── auth.php      # Sistema de autenticação
│   ├── constants.php # Constantes do sistema
│   └── database.sql  # Script de criação do banco
├── uploads/          # Arquivos enviados pelos usuários
│   ├── laudos/       # Laudos médicos
│   ├── documentos/   # Documentos empresariais
│   ├── logos/        # Logos de empresas
│   └── curriculos/   # Currículos dos candidatos
├── install/          # Instalador web automático
├── memory-bank/      # Documentação completa do projeto
├── *.php            # Páginas principais da aplicação
└── package.json     # Dependências (possível migração futura)
```

## 🔌 Arquitetura e Integração

### Backend (PHP)
- **8 Processadores**: `processar_cadastro.php`, `processar_cadastro_empresa.php`, `processar_login.php`, `processar_vaga.php`, `processar_candidatura.php`, `processar_perfil_pcd.php`, `processar_perfil_empresa.php`, `processar_recuperar_senha.php`
- **11 APIs REST**: Endpoints JSON para todas as operações principais
- **Autenticação**: Sistema completo com RBAC (Role-Based Access Control)
- **Validação**: Server-side validation em todos os formulários

### Frontend (JavaScript)
- **Cliente API Unificado**: `assets/js/api.js` com funções para todas as APIs
- **Integração Completa**: Todas as páginas principais conectadas ao backend
- **Componentes Reutilizáveis**: `includes/head.php`, `includes/nav.php`, `includes/footer.php`
- **Validação Client-side**: Máscaras de input e validação de formulários

### Fluxo de Dados
1. **Usuário interage** com formulário/página
2. **JavaScript** captura evento e valida client-side
3. **API REST** recebe requisição (via `api.js`)
4. **Backend PHP** valida, processa e retorna JSON
5. **Frontend** atualiza interface com resposta

## 🚀 Status do Projeto

**Fase Atual**: Sistema Completo - Pronto para Produção ✅
- ✅ Interface visual completa (13 páginas)
- ✅ Estrutura de pastas organizada
- ✅ Documentação completa no Memory Bank
- ✅ Backend 100% implementado (8 processadores + 11 APIs REST)
- ✅ Banco de dados estruturado e documentado
- ✅ Sistema de autenticação completo com RBAC
- ✅ Sistema de vagas e candidaturas funcional
- ✅ Painel administrativo completo e integrado
- ✅ Integração frontend com APIs completa
  - ✅ Perfil PCD (`perfil-pcd.php`) conectado ao backend
  - ✅ Perfil Empresa (`perfil-empresa.php`) conectado ao backend
  - ✅ Painel Admin (`admin.php`) conectado às APIs administrativas
  - ✅ Busca de vagas (`vagas.php`) integrada com API de busca
  - ✅ Cliente JavaScript completo (`assets/js/api.js`) para todas as APIs

## 📦 Instalação

### 🚀 Instalação Rápida (Recomendado)

**Use o instalador web automático:**

1. Acesse: `http://localhost/vigged/install` (ou sua URL)
2. Siga as instruções na tela
3. Preencha os dados do banco de dados
4. Configure o administrador
5. Clique em "Instalar Vigged"

O instalador irá:
- ✅ Verificar pré-requisitos automaticamente
- ✅ Criar o banco de dados
- ✅ Importar estrutura do banco
- ✅ Criar arquivos de configuração
- ✅ Criar diretórios necessários
- ✅ Configurar administrador

**Após a instalação:** Remova o instalador acessando `http://localhost/vigged/install/remove.php`

---

### Instalação Manual

Se preferir instalar manualmente ou o instalador não funcionar:

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Extensões PHP: PDO, PDO_MySQL, mbstring, fileinfo, json
- Servidor web (Apache/Nginx) ou ambiente de desenvolvimento

---

## 🖥️ Instalação via XAMPP (Windows/Linux/Mac)

### Passo 1: Instalar XAMPP

1. Baixe o XAMPP em: https://www.apachefriends.org/
2. Instale o XAMPP (inclui Apache, MySQL, PHP e phpMyAdmin)
3. Inicie o Apache e MySQL pelo painel de controle do XAMPP

### Passo 2: Configurar o Projeto

1. **Copiar projeto para htdocs:**
   ```bash
   # Windows
   C:\xampp\htdocs\vigged\
   
   # Linux/Mac
   /opt/lampp/htdocs/vigged/
   ```

2. **Ou criar link simbólico (recomendado):**
   ```bash
   # Windows (PowerShell como Administrador)
   New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\vigged" -Target "C:\caminho\completo\do\projeto\Vigged"
   
   # Linux/Mac
   ln -s /caminho/completo/do/projeto/Vigged /opt/lampp/htdocs/vigged
   ```

### Passo 3: Configurar Banco de Dados

1. **Acessar phpMyAdmin:**
   - Abra: `http://localhost/phpmyadmin`
   - Usuário padrão: `root`
   - Senha: (deixe em branco ou a senha que você configurou)

2. **Criar banco de dados:**
   - Clique em "Novo" no menu lateral
   - Nome do banco: `vigged_db`
   - Collation: `utf8mb4_unicode_ci`
   - Clique em "Criar"

3. **Importar estrutura:**
   - Selecione o banco `vigged_db`
   - Clique na aba "Importar"
   - Escolha o arquivo `config/database.sql`
   - Clique em "Executar"

   **OU** execute via SQL:
   ```sql
   -- Copie e cole todo o conteúdo de config/database.sql no SQL do phpMyAdmin
   ```

### Passo 4: Configurar Conexão

1. **Copiar arquivo de exemplo:**
   ```bash
   # Windows (PowerShell)
   Copy-Item config\database.example.php config\database.php
   
   # Linux/Mac
   cp config/database.example.php config/database.php
   ```

2. **Editar `config/database.php`:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'vigged_db');
   define('DB_USER', 'root');        // Padrão XAMPP
   define('DB_PASS', '');            // Padrão XAMPP (vazio)
   ```

3. **Ajustar URL base em `config/constants.php`:**
   ```php
   define('BASE_URL', 'http://localhost/vigged');
   ```

### Passo 5: Criar Diretórios de Upload

```bash
# Windows (PowerShell)
New-Item -ItemType Directory -Path "uploads\laudos" -Force
New-Item -ItemType Directory -Path "uploads\documentos" -Force
New-Item -ItemType Directory -Path "uploads\logos" -Force
New-Item -ItemType Directory -Path "uploads\curriculos" -Force

# Linux/Mac
mkdir -p uploads/laudos uploads/documentos uploads/logos uploads/curriculos
chmod 755 uploads uploads/*/
```

### Passo 6: Verificar Configurações PHP

1. **Editar `php.ini` do XAMPP:**
   - Localização: `C:\xampp\php\php.ini` (Windows) ou `/opt/lampp/etc/php.ini` (Linux/Mac)
   - Ajustar valores:
     ```ini
     upload_max_filesize = 10M
     post_max_size = 10M
     max_execution_time = 300
     memory_limit = 256M
     ```
   - Reiniciar Apache

2. **Verificar extensões habilitadas:**
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=fileinfo
   extension=json
   ```

### Passo 7: Configurar .htaccess (Opcional)

O arquivo `.htaccess` já está incluído no projeto com configurações de segurança. Se o Apache não estiver configurado para usar `.htaccess`, você pode ignorar este passo.

**Verificar se mod_rewrite está ativo:**
- No XAMPP: Já vem ativado por padrão
- Se necessário, edite `httpd.conf` e descomente: `LoadModule rewrite_module modules/mod_rewrite.so`

### Passo 8: Acessar o Sistema

**Opção 1: Usar Instalador Web (Recomendado)**
1. Acesse: `http://localhost/vigged/install`
2. Siga as instruções do instalador
3. Após instalação, remova o instalador: `http://localhost/vigged/install/remove.php`

**Opção 2: Acesso Manual**
1. Abra o navegador: `http://localhost/vigged`
2. Faça login com o administrador padrão:
   - **Email:** `admin@vigged.com`
   - **Senha:** `admin123`
3. ⚠️ **IMPORTANTE:** Altere a senha do administrador após o primeiro login!

---

## 🌐 Instalação via aaPanel (Servidor Linux)

### Passo 1: Instalar aaPanel

1. Acesse seu servidor via SSH
2. Execute o script de instalação:
   ```bash
   wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh && sudo bash install.sh aapanel
   ```
3. Acesse o painel: `http://SEU_IP:7800`
4. Configure usuário e senha do painel

### Passo 2: Instalar Ambiente PHP

1. No aaPanel, vá em **App Store**
2. Instale:
   - **PHP 7.4** ou superior (recomendado PHP 8.0+)
   - **MySQL 5.7+** ou **MariaDB 10.3+**
   - **phpMyAdmin** (opcional, mas recomendado)

3. **Configurar PHP:**
   - Vá em **App Store** → **PHP** → **Settings**
   - Clique em **Install Extensions**
   - Instale: `pdo_mysql`, `mbstring`, `fileinfo`, `json`
   - Clique em **Configuration** → **php.ini**
   - Ajuste:
     ```ini
     upload_max_filesize = 10M
     post_max_size = 10M
     max_execution_time = 300
     memory_limit = 256M
     ```

### Passo 3: Criar Site

1. No aaPanel, vá em **Website** → **Add Site**
2. Preencha:
   - **Domain:** `seu-dominio.com` ou `vigged.local`
   - **PHP Version:** Selecione PHP 7.4+ instalado
   - **Database:** Marque "Create Database"
   - **Database Name:** `vigged_db`
   - **Database User:** `vigged_user` (ou deixe gerar automaticamente)
   - **Database Password:** (anote a senha gerada)
3. Clique em **Submit**

### Passo 4: Fazer Upload do Projeto

**Opção A: Via Git (Recomendado)**
```bash
cd /www/wwwroot/seu-dominio.com
git clone https://github.com/seu-usuario/vigged.git .
```

**Opção B: Via FTP/SFTP**
1. Use FileZilla ou WinSCP
2. Conecte ao servidor
3. Faça upload de todos os arquivos para: `/www/wwwroot/seu-dominio.com/`

**Opção C: Via Terminal**
```bash
cd /www/wwwroot/seu-dominio.com
# Faça upload via scp ou rsync
```

### Passo 5: Configurar Banco de Dados

1. **Acessar phpMyAdmin:**
   - No aaPanel: **Database** → **phpMyAdmin**
   - Ou acesse: `http://SEU_IP:888/phpmyadmin`

2. **Importar estrutura:**
   - Selecione o banco `vigged_db` criado
   - Clique em **Import**
   - Escolha o arquivo `config/database.sql`
   - Clique em **Go**

   **OU** execute via SQL:
   ```sql
   -- Cole o conteúdo de config/database.sql
   ```

### Passo 6: Configurar Conexão

1. **Criar arquivo de configuração:**
   ```bash
   cd /www/wwwroot/seu-dominio.com
   cp config/database.example.php config/database.php
   ```

2. **Editar `config/database.php`:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'vigged_db');           // Nome do banco criado
   define('DB_USER', 'vigged_user');        // Usuário criado no passo 3
   define('DB_PASS', 'senha_gerada');       // Senha anotada no passo 3
   ```

3. **Ajustar URL em `config/constants.php`:**
   ```php
   define('BASE_URL', 'https://seu-dominio.com');  // Ou http:// se sem SSL
   ```

### Passo 7: Configurar Permissões

```bash
cd /www/wwwroot/seu-dominio.com

# Criar diretórios de upload
mkdir -p uploads/laudos uploads/documentos uploads/logos uploads/curriculos

# Ajustar permissões
chown -R www:www uploads/
chmod -R 755 uploads/
chmod -R 755 config/
```

### Passo 8: Configurar SSL (Opcional mas Recomendado)

1. No aaPanel: **Website** → Selecione seu site → **SSL**
2. Clique em **Let's Encrypt**
3. Marque **Force HTTPS**
4. Clique em **Apply**

### Passo 9: Configurar .htaccess

O arquivo `.htaccess` já está incluído no projeto. No aaPanel, certifique-se de que o Apache está configurado para permitir `.htaccess`:

1. No aaPanel: **Website** → Selecione seu site → **Settings**
2. Marque **Enable .htaccess** (geralmente já vem marcado)
3. Clique em **Save**

### Passo 10: Acessar o Sistema

**Opção 1: Usar Instalador Web (Recomendado)**
1. Acesse: `https://seu-dominio.com/install` (ou `http://` se sem SSL)
2. Siga as instruções do instalador
3. Após instalação, remova o instalador: `https://seu-dominio.com/install/remove.php`

**Opção 2: Acesso Manual**
1. Abra: `https://seu-dominio.com` (ou `http://` se sem SSL)
2. Faça login com:
   - **Email:** `admin@vigged.com`
   - **Senha:** `admin123`
3. ⚠️ **IMPORTANTE:** Altere a senha imediatamente!

---

## 🔄 Migrações do Banco de Dados

### O que são Migrações?

Migrações são scripts que atualizam a estrutura do banco de dados, adicionando novas colunas, tabelas ou funcionalidades sem perder dados existentes.

### Quando Executar Migrações?

Execute migrações quando:
- ✅ Você atualizou o código do projeto e precisa atualizar o banco de dados
- ✅ Você recebeu erros como "Unknown column 'feedback'" ou "Table doesn't exist"
- ✅ Novas funcionalidades foram adicionadas ao sistema

### Migração: Campos de Feedback e Avaliação

Esta migração adiciona campos necessários para o sistema de gerenciamento de candidaturas:

**Campos adicionados na tabela `applications`:**
- `feedback` - Texto para feedback da empresa ao candidato
- `avaliacao` - Avaliação de 1 a 5 estrelas
- `avaliado_em` - Data/hora da avaliação

**Tabelas criadas:**
- `notifications` - Sistema de notificações para usuários
- `application_status_history` - Histórico de mudanças de status de candidaturas

### Como Executar a Migração

**Opção 1: Via Navegador (Recomendado)**

1. Acesse no navegador:
   ```
   http://localhost/vigged/migrate_candidaturas.php
   ```

2. O script irá:
   - ✅ Verificar se as colunas já existem
   - ✅ Adicionar colunas necessárias se não existirem
   - ✅ Criar tabelas necessárias se não existirem
   - ✅ Exibir mensagens de sucesso ou erro

3. **IMPORTANTE:** Após executar com sucesso, **delete o arquivo** `migrate_candidaturas.php` por segurança!

**Opção 2: Via Linha de Comando**

```bash
# Windows (XAMPP)
C:\xampp\php\php.exe migrate_candidaturas.php

# Linux/Mac
php migrate_candidaturas.php
```

### Verificar se a Migração Foi Executada

Você pode verificar se a migração foi executada verificando se as colunas existem:

**Via phpMyAdmin:**
1. Acesse `http://localhost/phpmyadmin`
2. Selecione o banco `vigged_db`
3. Clique na tabela `applications`
4. Verifique se as colunas `feedback`, `avaliacao` e `avaliado_em` existem

**Via SQL:**
```sql
SHOW COLUMNS FROM applications LIKE 'feedback';
SHOW COLUMNS FROM applications LIKE 'avaliacao';
SHOW TABLES LIKE 'notifications';
```

### Troubleshooting de Migrações

**Erro: "Column already exists"**
- ✅ Isso é normal! Significa que a migração já foi executada
- Você pode ignorar este erro ou deletar o arquivo de migração

**Erro: "Access denied"**
- Verifique as credenciais em `config/database.php`
- Verifique se o usuário MySQL tem permissão ALTER TABLE

**Erro: "Table doesn't exist"**
- Execute primeiro o `config/database.sql` para criar a estrutura base
- Depois execute as migrações

### Segurança

⚠️ **IMPORTANTE:** Sempre delete os arquivos de migração após executá-los com sucesso!

Os arquivos de migração podem ser usados para modificar o banco de dados e devem ser removidos após uso por segurança.

---

## ✅ Verificação Pós-Instalação

### Teste de Conexão com Banco

Crie um arquivo `test_db.php` na raiz do projeto:

```php
<?php
require_once 'config/database.php';

if (testDBConnection()) {
    echo "✅ Conexão com banco de dados OK!";
} else {
    echo "❌ Erro na conexão. Verifique config/database.php";
}
```

Acesse: `http://localhost/vigged/test_db.php` (ou sua URL)

### Teste de Upload

1. Faça login como empresa
2. Tente fazer upload de logo
3. Verifique se o arquivo aparece em `uploads/logos/`

---

## 🔧 Troubleshooting

### Erro: "Call to undefined function getDBConnection()"
- Verifique se `config/database.php` existe
- Verifique se o caminho está correto

### Erro: "Access denied for user"
- Verifique credenciais em `config/database.php`
- No aaPanel: Verifique usuário e senha do banco em **Database**

### Erro: "Table doesn't exist"
- Execute o script `config/database.sql` novamente
- Verifique se o banco foi criado corretamente

### Erro ao fazer upload
- Verifique permissões: `chmod 755 uploads/` e subpastas
- Verifique `upload_max_filesize` no php.ini
- No aaPanel: Verifique limites em **PHP** → **Settings** → **Configuration**

### Página em branco
- Ative exibição de erros temporariamente em `config/database.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Verifique logs do PHP no aaPanel: **Files** → `/www/wwwroot/seu-dominio.com/runtime/`

### Erro 404 em rotas
- Verifique se o `.htaccess` está configurado (se necessário)
- No aaPanel: Verifique configuração do site em **Website** → **Settings**

---

## 📝 Configurações Adicionais

### Alterar Senha do Administrador

Via SQL no phpMyAdmin:
```sql
-- Gerar hash da nova senha em PHP primeiro:
-- <?php echo password_hash('nova_senha', PASSWORD_DEFAULT); ?>

UPDATE users 
SET senha = '$2y$10$hash_gerado_aqui'
WHERE email = 'admin@vigged.com';
```

### Configurar Email (Futuro)

Edite `config/constants.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-app');
define('EMAIL_FROM', 'noreply@vigged.com.br');
```

---

## 📚 Documentação

### Documentação Técnica
- `INSTALL.md` - Guia de instalação detalhado
- `install/README.md` - Documentação do instalador web
- `config/README.md` - Documentação de configuração
- `api/README.md` - Documentação das APIs REST
- `memory-bank/backend-completo.md` - Resumo técnico do backend
- `memory-bank/instalador-web.md` - Documentação completa do instalador

### Documentação do Projeto (memory-bank/)
- `projectbrief.md` - Visão geral e objetivos
- `productContext.md` - Contexto do produto
- `systemPatterns.md` - Padrões e arquitetura
- `techContext.md` - Stack tecnológico
- `activeContext.md` - Contexto atual e próximos passos
- `progress.md` - Status e progresso
- `code-analysis.md` - Análise detalhada do código
- `programa-pcd.md` - Diretrizes do Programa PCD
- `database-schema.md` - Esquema completo do banco de dados

## 🔒 Segurança e Conformidade

### Segurança Implementada
- ✅ SQL Injection: Proteção via Prepared Statements (PDO)
- ✅ XSS: Sanitização de todos os inputs
- ✅ Senhas: Hash bcrypt, nunca em texto plano
- ✅ Sessões: Cookies HttpOnly, regeneração de ID
- ✅ Uploads: Validação de tipo e tamanho
- ✅ RBAC: Controle de acesso baseado em papéis
- ✅ CSRF: Proteção contra Cross-Site Request Forgery (tokens em todos os formulários)
- ✅ .htaccess: Headers de segurança configurados
- ✅ Validação server-side: Todas as operações críticas validadas no backend

### Conformidade Legal
- LGPD compliance (Lei Geral de Proteção de Dados)
- Conformidade com Lei Brasileira de Inclusão
- Alinhamento com Lei de Cotas (Lei 8.213/1991)
- Acessibilidade WCAG 2.1

### ⚠️ Importante
- **NUNCA** commite o arquivo `config/database.php` com credenciais reais
- Altere a senha do administrador padrão após instalação
- Configure SSL/HTTPS em produção
- Revise configurações de segurança antes de deploy

## 📝 Licença

Este projeto é um Trabalho de Conclusão de Curso (TCC).

## 👥 Desenvolvimento

Projeto desenvolvido como TCC, focado em inclusão, acessibilidade e melhores práticas de desenvolvimento web.

---

**Vigged** - Conectando talentos PCD às melhores oportunidades do mercado.
