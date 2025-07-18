# Checklist de Testes da API - Sistema de Contatos

## Como usar este checklist:
1. Execute cada teste no arquivo `api-teste.http` usando o VS Code + REST Client
2. Preencha o status (✔️ ou ❌) e o resumo da resposta
3. Me envie o resultado final

---

## Testes de Autenticação

| Teste | Status | HTTP | Resumo |
|-------|:------:|:----:|--------|
| Login ADMIN |  |  |  |
| Login USUAL |  |  |  |

## Testes de CRUD (ADMIN)

| Teste | Status | HTTP | Resumo |
|-------|:------:|:----:|--------|
| Criar contato (ADMIN) |  |  |  |
| Listar contatos (ADMIN) |  |  |  |
| Atualizar contato (ADMIN) |  |  |  |
| Deletar contato (ADMIN) |  |  |  |

## Testes de CRUD (USUAL)

| Teste | Status | HTTP | Resumo |
|-------|:------:|:----:|--------|
| Criar contato (USUAL) |  |  |  |
| Tentar atualizar contato (USUAL) |  |  |  |
| Tentar deletar contato (USUAL) |  |  |  |

## Testes de Validação

| Teste | Status | HTTP | Resumo |
|-------|:------:|:----:|--------|
| Criar contato com CEP inválido |  |  |  |
| Criar contato com campos obrigatórios faltando |  |  |  |

---

## Resultados Esperados:

### ✅ Sucessos esperados:
- Login ADMIN/USUAL: 200 OK
- Criar contato: 201 Created
- Listar contatos: 200 OK
- Atualizar contato (ADMIN): 200 OK
- Deletar contato (ADMIN): 200 OK
- Criar contato com CEP inválido: 201 Created (sem dados ViaCep)

### ❌ Falhas esperadas:
- Tentar UPDATE/DELETE com usuário USUAL: 403 Forbidden
- Validação de campos obrigatórios: 422 Unprocessable Entity

---

## Como executar:

1. **Instalar VS Code**: https://code.visualstudio.com/
2. **Instalar extensão REST Client** no VS Code
3. **Abrir arquivo** `api-teste.http`
4. **Executar login** e copiar tokens
5. **Substituir** `{{token_admin}}` e `{{token_user}}` pelos tokens reais
6. **Executar** cada teste clicando em "Send Request"
7. **Preencher** este checklist com os resultados 

**Agora você pode:**

1. **Colar este conteúdo** no arquivo `api-teste.http` no VS Code
2. **Salvar o arquivo** (Ctrl+S)
3. **Executar os testes** clicando em "Send Request" acima de cada requisição
4. **Preencher o checklist** com os resultados

**Lembre-se:**
- Execute primeiro os logins para obter os tokens
- Substitua `{{token_admin}}` e `{{token_user}}` pelos tokens reais
- Use o arquivo `checklist-testes.md` para anotar os resultados

Quando terminar os testes, me envie o checklist preenchido! 