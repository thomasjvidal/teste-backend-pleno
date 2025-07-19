# 🏦 API de Contatos - HBI Crédito

API RESTful desenvolvida em Laravel para gerenciamento de contatos com autenticação JWT e controle de acesso baseado em roles.

## 🚀 Tecnologias Utilizadas

- **Laravel 11** - Framework PHP
- **JWT (tymon/jwt-auth)** - Autenticação
- **MySQL** - Banco de dados
- **PHPUnit** - Testes automatizados
- **ViaCEP API** - Integração para busca de endereços

## 📋 Funcionalidades

### ✅ Implementadas
- [x] Autenticação JWT com roles (ADMIN/USUAL)
- [x] CRUD completo de contatos
- [x] Validação robusta de dados
- [x] Integração com ViaCEP
- [x] Relacionamentos entre entidades
- [x] Soft deletes
- [x] Testes automatizados
- [x] Respostas padronizadas em JSON
- [x] Logs estruturados

### 🔒 Controle de Acesso
- **Usuários USUAL**: Podem criar e listar contatos
- **Usuários ADMIN**: Podem criar, listar, atualizar e deletar contatos

## 🛠️ Instalação e Configuração

### Pré-requisitos
- PHP 8.2+
- Composer
- MySQL 8.0+

#### Extensões PHP Obrigatórias
Certifique-se de que as seguintes extensões PHP estejam habilitadas:
- `mbstring` - Manipulação de strings multibyte
- `dom` - Manipulação de documentos XML
- `json` - Codificação/decodificação JSON
- `libxml` - Biblioteca XML
- `tokenizer` - Análise de tokens PHP
- `xml` - Suporte XML
- `xmlwriter` - Escrita de XML

**Para verificar as extensões instaladas:**
```bash
php -m | grep -E "(mbstring|dom|json|libxml|tokenizer|xml|xmlwriter)"
```

**Para instalar no Windows (XAMPP/WAMP):**
- Habilite as extensões no arquivo `php.ini`
- Reinicie o servidor web

**Para instalar no Linux:**
```bash
sudo apt-get install php8.2-mbstring php8.2-xml php8.2-json
```

**Para instalar no macOS:**
```bash
brew install php@8.2
```

### 1. Clone o repositório
```bash
git clone [URL_DO_REPOSITORIO]
cd backend-api
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados
Edite o arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hbi_credito
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Configure o JWT
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

### 6. Execute as migrações e seeders
```bash
php artisan migrate:fresh --seed
```

### 7. Inicie o servidor
```bash
php artisan serve --host=127.0.0.1 --port=3000
```

### 8. Usuários de teste criados automaticamente
- **Admin**: `admin` / `password`
- **Usuário**: `user` / `password`
- **Teste**: `testuser` / `password`

## 📚 Documentação da API

### Base URL
```
http://127.0.0.1:3000/api
```

### Autenticação
Todas as rotas protegidas requerem o header:
```
Authorization: Bearer {token}
```

### Endpoints

#### 🔐 Autenticação

**POST /login**
```json
{
    "username": "admin",
    "password": "password"
}
```

**Resposta:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

#### 👥 Contatos

**GET /contacts** - Listar contatos do usuário
```bash
curl -H "Authorization: Bearer {token}" \
     http://127.0.0.1:3000/api/contacts
```

**POST /contacts** - Criar contato (USUAL/ADMIN)
```json
{
    "name": "João Silva",
    "description": "Cliente importante",
    "address": {
        "zip_code": "12345-678",
        "address_number": "123",
        "street_address": "Rua das Flores"
    },
    "phones": [
        {"phone": "(11) 99999-9999"}
    ],
    "emails": [
        {"email": "joao@email.com"}
    ]
}
```

**PUT /contacts/{id}** - Atualizar contato (ADMIN apenas)
```json
{
    "name": "João Silva Atualizado",
    "description": "Cliente muito importante",
    "address": {
        "zip_code": "54321-876",
        "address_number": "456",
        "street_address": "Rua das Palmeiras"
    },
    "phones": [
        {"phone": "(11) 88888-8888"}
    ],
    "emails": [
        {"email": "joao.atualizado@email.com"}
    ]
}
```

**DELETE /contacts/{id}** - Deletar contato (ADMIN apenas)
```bash
curl -X DELETE \
     -H "Authorization: Bearer {admin_token}" \
     http://127.0.0.1:3000/api/contacts/1
```

### 📊 Estrutura de Resposta

Todas as respostas seguem o padrão:
```json
{
    "status": "success|error",
    "message": "Mensagem descritiva",
    "data": {
        // Dados da resposta
    },
    "meta": {
        // Metadados (quando aplicável)
    }
}
```

### 🔍 Integração ViaCEP

Ao fornecer apenas o CEP, a API automaticamente busca e preenche os dados de endereço:
```json
{
    "address": {
        "zip_code": "12345-678",
        "address_number": "123"
        // Outros campos serão preenchidos automaticamente
    }
}
```

## 🧪 Testes

### Executar todos os testes
```bash
php artisan test
```

### Executar testes específicos
```bash
# Testes de autenticação
php artisan test --filter=AuthTest

# Testes de contatos
php artisan test --filter=ContactApiTest

# Testes de validação
php artisan test --filter=ContactValidationTest
```

### Cobertura de testes
- ✅ **AuthTest**: Testes de login, logout e proteção de rotas
- ✅ **ContactApiTest**: Testes completos de CRUD de contatos
- ✅ **Validação**: Testes de validação de dados
- ✅ **Controle de acesso**: Testes de roles (ADMIN/USUAL)
- ✅ **Integração ViaCEP**: Testes de busca de endereços

### Exemplo de execução
```bash
# Executar testes com detalhes
php artisan test --verbose

# Executar testes com cobertura (se disponível)
php artisan test --coverage
```

## 🔧 Troubleshooting

### Erro: "PHPUnit requires the extensions"
Se você encontrar o erro:
```
PHPUnit requires the "dom", "json", "libxml", "mbstring", "tokenizer", "xml", "xmlwriter" extensions
```

**Solução:**
1. Verifique se as extensões PHP estão habilitadas (veja seção Pré-requisitos)
2. Reinicie o servidor web após habilitar as extensões
3. Execute: `composer dump-autoload`

### Erro: "Call to undefined function"
Se encontrar erros de funções não definidas:
1. Verifique se o PHP está na versão 8.2+
2. Habilite as extensões necessárias
3. Execute: `php artisan config:clear && php artisan cache:clear`

### Erro de conexão com banco de dados
1. Verifique se o MySQL está rodando
2. Confirme as credenciais no arquivo `.env`
3. Execute: `php artisan migrate:status`

### Erro de JWT
1. Execute: `php artisan jwt:secret`
2. Verifique se o arquivo `.env` tem a chave JWT_SECRET
3. Execute: `php artisan config:clear`

## 🏗️ Arquitetura

### Estrutura de Pastas
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── ContactController.php
│   ├── Requests/
│   │   ├── StoreContactRequest.php
│   │   └── UpdateContactRequest.php
│   ├── Resources/
│   │   └── ContactResource.php
│   └── Middleware/
│       ├── Authenticate.php
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Contact.php
│   ├── Address.php
│   ├── Phone.php
│   └── Email.php
└── Services/
    └── ContactService.php
```

### Padrões Utilizados
- **Repository Pattern** - Separação de lógica de negócio
- **Form Request Validation** - Validação robusta
- **API Resources** - Padronização de respostas
- **Service Layer** - Lógica de negócio isolada
- **Middleware** - Controle de acesso

## 🔒 Segurança

- ✅ Autenticação JWT
- ✅ Controle de acesso baseado em roles
- ✅ Validação de entrada
- ✅ Proteção contra SQL Injection (Eloquent ORM)
- ✅ Proteção contra XSS
- ✅ Logs de auditoria

## 📝 Logs

Os logs são estruturados e incluem:
- Criação, atualização e exclusão de contatos
- Tentativas de acesso não autorizado
- Erros de validação
- Integração com ViaCEP

## 🚀 Deploy

### Produção
1. Configure as variáveis de ambiente
2. Execute `composer install --optimize-autoloader --no-dev`
3. Configure o servidor web (Apache/Nginx)
4. Configure o supervisor para filas (se necessário)

### Docker (opcional)
```dockerfile
FROM php:8.2-fpm
# ... configuração do Docker
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📋 Notas Técnicas

### Decisões Arquiteturais

#### 🔗 Relacionamento User-Contact
**Decisão:** Adicionado campo `user_id` na tabela `contacts`
- **Justificativa:** Permite vincular contatos ao usuário autenticado
- **Benefício:** Segurança e isolamento de dados por usuário
- **Implementação:** Foreign key com cascade delete

#### 🏗️ Padrão Repository + Service Layer
**Decisão:** Implementação de Repository Pattern com Service Layer
- **Justificativa:** Separação clara de responsabilidades
- **Benefício:** Código testável, manutenível e escalável
- **Implementação:** ContactRepository encapsula queries, ContactService orquestra operações

#### 🔐 Autenticação JWT
**Decisão:** Uso de JWT (tymon/jwt-auth) em vez de Sanctum
- **Justificativa:** Melhor para APIs stateless
- **Benefício:** Performance e escalabilidade
- **Implementação:** Middleware `auth:api` + `CheckRole`

#### 📍 Integração ViaCEP
**Decisão:** Integração automática ao fornecer apenas CEP
- **Justificativa:** Melhora UX e reduz erros de digitação
- **Benefício:** Dados de endereço consistentes
- **Implementação:** AddressService com fallback e logs

#### 🧪 Estratégia de Testes
**Decisão:** Testes de integração com Feature Tests
- **Justificativa:** Cobertura completa do fluxo da API
- **Benefício:** Garantia de qualidade e regressão
- **Implementação:** PHPUnit com RefreshDatabase

### Melhorias Implementadas

#### ✅ Extras Implementados
- **Soft Deletes:** Contatos não são deletados permanentemente
- **Logs Estruturados:** Auditoria completa de operações
- **Validações Robustas:** Regex para CEP e telefone
- **Respostas Padronizadas:** Estrutura JSON consistente
- **Índices de Performance:** Otimização de consultas

#### 🔧 Configurações de Segurança
- **CORS:** Configurado para APIs
- **Rate Limiting:** Proteção contra abuso
- **Validação de Entrada:** Form Requests com mensagens customizadas
- **Proteção SQL Injection:** Eloquent ORM
- **Controle de Acesso:** Middleware de roles

### Estrutura de Banco de Dados

#### Tabelas Principais
```sql
-- Estrutura mínima exigida + melhorias
contacts: id, user_id, name, description, created_at, updated_at, deleted_at
addresses: id, id_contact, zip_code, country, state, street_address, address_number, city, address_line, neighborhood, created_at, updated_at
phones: id, id_contact, phone, created_at, updated_at
emails: id, id_contact, email, created_at, updated_at
users: id, username, password, role, created_at, updated_at
```

#### Relacionamentos
- `users` → `contacts` (1:N)
- `contacts` → `addresses` (1:1)
- `contacts` → `phones` (1:N)
- `contacts` → `emails` (1:N)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👨‍💻 Desenvolvedor

Desenvolvido para o teste técnico da **HBI Crédito** - Vaga de Desenvolvedor Backend Pleno.

---

**Status do Projeto**: ✅ **COMPLETO E FUNCIONAL**
