# API - Vigged

Documentação das APIs REST do sistema Vigged.

## Endpoints Disponíveis

### Busca e Vagas

#### `GET /api/buscar_vagas.php`
Busca vagas com filtros.

**Parâmetros:**
- `q` (string, opcional) - Termo de busca
- `localizacao` (string, opcional) - Filtro por localização
- `tipo_contrato` (string, opcional) - CLT, PJ, Estagio, Temporario
- `destacada` (int, opcional) - 1 para apenas destacadas, 0 para todas
- `page` (int, opcional) - Página (padrão: 1)
- `limit` (int, opcional) - Itens por página (padrão: 10, máx: 50)

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "titulo": "Desenvolvedor PHP",
      "descricao": "...",
      "localizacao": "São Paulo, SP",
      "tipo_contrato": "CLT",
      "faixa_salarial": "R$ 5.000 - R$ 8.000",
      "destacada": 1,
      "empresa_nome": "Tech Solutions",
      ...
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 50,
    "pages": 5
  }
}
```

**Exemplo:**
```javascript
const vagas = await fetch('api/buscar_vagas.php?q=desenvolvedor&localizacao=São Paulo&page=1')
  .then(r => r.json());
```

### Empresa

#### `GET /api/dados_empresa.php`
Obtém dados da empresa logada.

**Autenticação:** Requer login como empresa

**Resposta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "razao_social": "Tech Solutions Ltda",
    "nome_fantasia": "Tech Solutions",
    "cnpj": "12.345.678/0001-90",
    "total_vagas": 5,
    "vagas_ativas": 3,
    "total_candidaturas": 12,
    ...
  }
}
```

#### `GET /api/vagas_empresa.php`
Lista vagas da empresa logada.

**Autenticação:** Requer login como empresa

**Parâmetros:**
- `status` (string, opcional) - ativa, pausada, encerrada, todas (padrão: ativa)

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "titulo": "Desenvolvedor PHP",
      "status": "ativa",
      "total_candidaturas": 5,
      "candidaturas_pendentes": 2,
      ...
    }
  ]
}
```

### Candidato PCD

#### `GET /api/dados_pcd.php`
Obtém dados do candidato PCD logado.

**Autenticação:** Requer login como PCD

**Resposta:**
```json
{
  "success": true,
  "data": {
    "usuario": {
      "id": 1,
      "nome": "João Silva",
      "email": "joao@email.com",
      "tipo_deficiencia": "fisica",
      ...
    },
    "candidaturas": [
      {
        "id": 1,
        "vaga_titulo": "Desenvolvedor PHP",
        "status": "pendente",
        "created_at": "2024-01-15 10:00:00",
        ...
      }
    ],
    "total_candidaturas": 5,
    "candidaturas_pendentes": 2
  }
}
```

### Administrador

#### `GET /api/admin_usuarios.php?action=list`
Lista usuários PCD.

**Autenticação:** Requer login como admin

**Parâmetros:**
- `status` (string, opcional) - ativo, inativo, pendente, todas
- `page` (int, opcional) - Página
- `limit` (int, opcional) - Itens por página

#### `POST /api/admin_usuarios.php?action=update_status`
Atualiza status de usuário.

**Body:**
```json
{
  "user_id": 1,
  "status": "ativo"
}
```

#### `GET /api/admin_empresas.php?action=list`
Lista empresas.

**Autenticação:** Requer login como admin

**Parâmetros:** Mesmos de admin_usuarios.php

#### `POST /api/admin_empresas.php?action=update_status`
Atualiza status de empresa.

**Body:**
```json
{
  "company_id": 1,
  "status": "ativa"
}
```

## Uso com JavaScript

Use o arquivo `assets/js/api.js` que fornece funções helper:

```javascript
// Buscar vagas
const resultado = await ViggedAPI.buscarVagas({
  q: 'desenvolvedor',
  localizacao: 'São Paulo',
  page: 1
});

// Obter dados da empresa
const empresa = await ViggedAPI.obterDadosEmpresa();

// Publicar vaga
const resultado = await ViggedAPI.salvarVaga({
  titulo: 'Desenvolvedor PHP',
  descricao: '...',
  localizacao: 'São Paulo, SP',
  tipo_contrato: 'CLT',
  faixa_salarial: 'R$ 5.000 - R$ 8.000'
});
```

## Autenticação

APIs que requerem autenticação verificam:
- Sessão PHP ativa
- Tipo de usuário correto (via `requireAuth()`)

## Tratamento de Erros

Todas as APIs retornam JSON com:
- `success: true/false`
- `error` ou `errors` em caso de erro
- `data` com os dados em caso de sucesso

Códigos HTTP:
- `200` - Sucesso
- `400` - Requisição inválida
- `401` - Não autenticado
- `404` - Não encontrado
- `500` - Erro interno do servidor

## Segurança

- Todas as APIs usam prepared statements
- Validação de autenticação em todas as rotas protegidas
- Sanitização de inputs
- Verificação de permissões por tipo de usuário

