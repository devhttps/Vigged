# Config - Arquivos de Configuração

Esta pasta contém arquivos de configuração essenciais do sistema.

## Arquivos Disponíveis

### database.php
Configuração de conexão com o banco de dados MySQL/MariaDB.

**Configurações:**
- `DB_HOST` - Host do banco de dados (padrão: localhost)
- `DB_NAME` - Nome do banco de dados (padrão: vigged_db)
- `DB_USER` - Usuário do banco de dados
- `DB_PASS` - Senha do banco de dados
- `DB_CHARSET` - Charset (padrão: utf8mb4)

**Funções:**
- `getDBConnection()` - Retorna instância PDO (singleton)
- `testDBConnection()` - Testa conexão com banco

**Uso:**
```php
require_once 'config/database.php';
$pdo = getDBConnection();
```

### database.example.php
Arquivo de exemplo para configuração do banco de dados. Copie para `database.php` e ajuste as credenciais.

### database.sql
Script SQL completo para criar o banco de dados e todas as tabelas. Execute este script no MySQL/MariaDB antes de usar o sistema.

**Como usar:**
```bash
mysql -u root -p < config/database.sql
```

Ou via phpMyAdmin/MySQL Workbench importando o arquivo.

### constants.php
Constantes do sistema (tipos de usuário, status, limites, URLs, etc.).

**Principais constantes:**
- Tipos de usuário: `USER_TYPE_PCD`, `USER_TYPE_COMPANY`, `USER_TYPE_ADMIN`
- Status: `STATUS_ATIVO`, `STATUS_INATIVO`, `STATUS_PENDENTE`
- Limites de upload: `MAX_UPLOAD_SIZE`, `MAX_LAUDO_SIZE`, `MAX_DOCUMENTO_SIZE`
- Diretórios: `ROOT_PATH`, `INCLUDES_PATH`, `ASSETS_PATH`, `UPLOADS_PATH`

### auth.php
Sistema de autenticação e autorização RBAC.

**Funções principais:**
- `startSecureSession()` - Inicia sessão segura
- `isAuthenticated()` - Verifica se usuário está autenticado
- `isAdmin()`, `isPCD()`, `isCompany()` - Verifica tipo de usuário
- `requireAuth($type)` - Requer autenticação (redireciona se não autenticado)
- `login($userId, $userType, $data)` - Faz login do usuário
- `logout()` - Faz logout
- `validateCredentials($email, $password, $userType)` - Valida credenciais
- `getCurrentUser()` - Obtém dados do usuário logado

**Uso:**
```php
require_once 'config/auth.php';

// Proteger página apenas para PCD
requireAuth(USER_TYPE_PCD);

// Ou verificar tipo
if (isAdmin()) {
    // Código apenas para admin
}
```

## Configuração Inicial

1. **Copiar exemplo de configuração:**
   ```bash
   cp config/database.example.php config/database.php
   ```

2. **Ajustar credenciais em `database.php`**

3. **Criar banco de dados:**
   ```bash
   mysql -u root -p < config/database.sql
   ```

4. **Verificar conexão:**
   ```php
   require_once 'config/database.php';
   if (testDBConnection()) {
       echo "Conexão OK!";
   }
   ```

## Segurança

- ⚠️ **NUNCA** commite `database.php` com credenciais reais no Git
- Use `.gitignore` para ignorar `database.php`
- Use variáveis de ambiente em produção
- Mantenha senhas seguras e diferentes por ambiente

## Estrutura de Pastas Necessária

Certifique-se de que existem os diretórios:
- `uploads/laudos/` - Para uploads de laudos médicos
- `uploads/documentos/` - Para documentos de empresas
- `uploads/logos/` - Para logos de empresas

Esses diretórios serão criados automaticamente pelos scripts de processamento, mas você pode criá-los manualmente com permissões adequadas.

