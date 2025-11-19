# Instalador Web - Vigged

## Como Usar

### Acesso ao Instalador

1. Acesse: `http://localhost/vigged/install` (ou sua URL)
2. O instalador verificará automaticamente os pré-requisitos
3. Preencha o formulário com:
   - **Dados do Banco de Dados:** Host, nome, usuário, senha
   - **URL Base:** URL do seu site
   - **Administrador:** Email e senha do primeiro admin
4. Clique em "Instalar Vigged"
5. Aguarde a conclusão da instalação

### O que o Instalador Faz

- ✅ Verifica pré-requisitos (PHP, extensões, permissões)
- ✅ Cria banco de dados automaticamente
- ✅ Importa estrutura do banco (tabelas, índices, dados iniciais)
- ✅ Cria arquivo `config/database.php` com suas credenciais
- ✅ Atualiza `BASE_URL` em `config/constants.php`
- ✅ Cria diretórios de upload necessários
- ✅ Configura administrador com senha personalizada
- ✅ Testa conexão com banco de dados
- ✅ Cria arquivo `.installed` para marcar instalação

### Após a Instalação

1. **Remover Instalador (IMPORTANTE):**
   - Acesse: `http://localhost/vigged/install/remove.php`
   - Confirme a remoção
   - O instalador será removido por segurança

2. **Fazer Login:**
   - Use as credenciais configuradas durante a instalação
   - Altere a senha do administrador após o primeiro login

### Segurança

- O instalador é automaticamente bloqueado após instalação (via `.htaccess`)
- Arquivo `.installed` marca que o sistema foi instalado
- Credenciais são armazenadas de forma segura em `config/database.php`
- O arquivo `config/database.php` está protegido pelo `.gitignore`

### Troubleshooting

**Erro: "Call to undefined function"**
- Verifique se todas as extensões PHP estão instaladas
- Verifique versão do PHP (mínimo 7.4)

**Erro: "Access denied for user"**
- Verifique credenciais do banco de dados
- Verifique se o usuário MySQL tem permissão para criar bancos

**Erro: "Table already exists"**
- O banco pode já ter sido criado parcialmente
- Delete o banco e tente novamente, ou continue (o instalador ignora erros de tabelas existentes)

**Instalador não aparece**
- Verifique se a pasta `install/` existe
- Verifique permissões do diretório
- Verifique se `.htaccess` está configurado corretamente

### Arquivos do Instalador

- `index.php` - Interface do instalador
- `check.php` - Verificação de pré-requisitos
- `install.php` - Processamento da instalação
- `success.php` - Página de sucesso
- `remove.php` - Remoção do instalador
- `.htaccess` - Proteção do instalador

### Reinstalação

Para reinstalar:
1. Remova o arquivo `.installed` da raiz do projeto
2. Delete o banco de dados `vigged_db` (ou o nome que você usou)
3. Acesse o instalador novamente

---

**⚠️ IMPORTANTE:** Sempre remova o instalador após a instalação bem-sucedida por questões de segurança!

