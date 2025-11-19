# Guia de Instalação - Vigged

Este guia explica como configurar o ambiente de desenvolvimento do projeto Vigged.

## Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Servidor web (Apache/Nginx) ou PHP built-in server
- Extensões PHP: PDO, PDO_MySQL, mbstring, fileinfo

## Passo 1: Clonar/Configurar o Projeto

```bash
cd /caminho/do/projeto
```

## Passo 2: Configurar Banco de Dados

### 2.1. Criar arquivo de configuração

Copie o arquivo de exemplo:
```bash
cp config/database.example.php config/database.php
```

### 2.2. Ajustar credenciais

Edite `config/database.php` e ajuste as configurações:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vigged_db');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 2.3. Criar banco de dados

Execute o script SQL:
```bash
mysql -u root -p < config/database.sql
```

Ou via phpMyAdmin/MySQL Workbench:
1. Abra o arquivo `config/database.sql`
2. Execute o script completo

## Passo 3: Criar Diretórios de Upload

Crie os diretórios necessários para uploads:

```bash
mkdir -p uploads/laudos
mkdir -p uploads/documentos
mkdir -p uploads/logos
```

**Permissões (Linux/Mac):**
```bash
chmod 755 uploads
chmod 755 uploads/laudos
chmod 755 uploads/documentos
chmod 755 uploads/logos
```

**Permissões (Windows):**
Os diretórios devem ter permissão de escrita para o usuário do servidor web.

## Passo 4: Verificar Configurações

### 4.1. Verificar conexão com banco

Crie um arquivo temporário `test_db.php`:

```php
<?php
require_once 'config/database.php';

if (testDBConnection()) {
    echo "✅ Conexão com banco de dados OK!";
} else {
    echo "❌ Erro na conexão com banco de dados.";
}
```

Acesse via navegador e verifique se a conexão está funcionando.

### 4.2. Verificar constantes

Edite `config/constants.php` e ajuste se necessário:
- `BASE_URL` - URL base do projeto
- `UPLOADS_PATH` - Caminho para uploads

## Passo 5: Configurar Servidor Web

### Opção A: PHP Built-in Server (Desenvolvimento)

```bash
php -S localhost:8000
```

Acesse: `http://localhost:8000`

### Opção B: Apache/Nginx

Configure virtual host apontando para o diretório do projeto.

## Passo 6: Criar Usuário Administrador

O script `database.sql` já cria um usuário administrador padrão:

- **Email:** admin@vigged.com
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere a senha do administrador após o primeiro login!

Para alterar a senha, execute no banco:
```sql
UPDATE users 
SET senha = '$2y$10$...' -- Gerar hash com password_hash('nova_senha', PASSWORD_DEFAULT)
WHERE email = 'admin@vigged.com';
```

## Passo 7: Testar Instalação

1. Acesse `http://localhost/vigged` (ou sua URL configurada)
2. Teste o cadastro de um candidato PCD
3. Teste o cadastro de uma empresa
4. Faça login com o administrador

## Troubleshooting

### Erro: "Call to undefined function getDBConnection()"
- Verifique se `config/database.php` existe
- Verifique se o arquivo está sendo incluído corretamente

### Erro: "Access denied for user"
- Verifique credenciais em `config/database.php`
- Verifique se o usuário MySQL tem permissões

### Erro: "Table doesn't exist"
- Execute o script `config/database.sql`
- Verifique se o banco de dados foi criado

### Erro ao fazer upload de arquivos
- Verifique permissões dos diretórios `uploads/`
- Verifique `upload_max_filesize` no php.ini
- Verifique `post_max_size` no php.ini

## Próximos Passos

Após a instalação:
1. Configure email para notificações (SMTP em `config/constants.php`)
2. Configure HTTPS em produção
3. Ajuste configurações de segurança
4. Revise logs de erro do PHP

## Suporte

Para problemas ou dúvidas, consulte:
- `memory-bank/` - Documentação completa do projeto
- `config/README.md` - Documentação de configuração
- Logs do PHP para erros detalhados

