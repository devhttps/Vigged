# 🚀 Instalador Web - Vigged

## Instalação Rápida em 3 Passos

### 1️⃣ Acesse o Instalador
```
http://localhost/vigged/install
```

### 2️⃣ Preencha o Formulário
- **Banco de Dados:** Host, nome, usuário, senha
- **URL Base:** Detectada automaticamente (ajuste se necessário)
- **Administrador:** Email e senha do primeiro admin

### 3️⃣ Clique em "Instalar Vigged"
O instalador fará tudo automaticamente! ✨

---

## O que o Instalador Faz

✅ Verifica pré-requisitos (PHP, extensões, permissões)  
✅ Cria banco de dados automaticamente  
✅ Importa estrutura completa (tabelas, índices, dados iniciais)  
✅ Cria arquivo `config/database.php` com suas credenciais  
✅ Atualiza `BASE_URL` automaticamente  
✅ Cria diretórios de upload necessários  
✅ Configura administrador com senha personalizada  
✅ Testa conexão final  

---

## Após a Instalação

### ⚠️ IMPORTANTE: Remover Instalador

Por segurança, remova o instalador após instalação bem-sucedida:

```
http://localhost/vigged/install/remove.php
```

Ou delete manualmente a pasta `install/`

---

## Testar Instalação

Acesse para verificar se tudo está funcionando:

```
http://localhost/vigged/install/test.php
```

---

## Troubleshooting

### Instalador não aparece
- Verifique se a pasta `install/` existe
- Verifique permissões do diretório
- Verifique se já está instalado (arquivo `.installed` existe)

### Erro ao criar banco
- Verifique credenciais do MySQL
- Verifique se usuário tem permissão CREATE DATABASE
- No XAMPP: usuário padrão é `root` com senha vazia

### Erro ao importar SQL
- Verifique se arquivo `config/database.sql` existe
- Verifique logs de erro do PHP
- Tente importar manualmente via phpMyAdmin

### Reinstalar
1. Remova arquivo `.installed` da raiz
2. Delete banco de dados `vigged_db`
3. Acesse o instalador novamente

---

## Documentação Completa

- `install/README.md` - Documentação detalhada do instalador
- `memory-bank/instalador-web.md` - Documentação técnica completa

---

**Desenvolvido para facilitar a instalação do Vigged** 🎯

