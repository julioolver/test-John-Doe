# Pagamentos Simplificados API

Projeto backend para transferências entre usuarios com carteiras, seguindo regras de negocio, transação atômica e integrações externas de autorização e notificação. Foi utlizado seeder do Laravel para popular usuários e carteiras, sendo IDs 1 e 2 para usuários comuns e ID 30 para lojista. 
## Visao geral

- Usuários comuns (CPF) podem transferir
- Lojistas (CNPJ) apenas recebem
- Carteiras com saldo em centavos
- Autorização externa antes da transferência
- Notificação externa apos a transferência

## Como rodar o projeto

```bash
# copiar o arquivo de ambiente
cp .env.example .env

# subir containers
 docker compose up -d --build

# dependências do projeto
 docker compose exec app composer install

# migrations e seeders
 docker compose exec app php artisan migrate --seed
```

## Endpoint principal

```http request
POST http://localhost:84/api/transfers
Content-Type: application/json

{
  "value": 100.0,
  "payer": 1,
  "payee": 3
}
```

## Estrutura de pastas (Clean Architecture)

```
app/
  Domain/          # regras de negócio puras (entidades, VOs, excecoes)
  Application/     # casos de uso, DTOs e contratos (interfaces)
  Infrastructure/  # implementacoes (Eloquent, HTTP, DB)
  Http/            # controllers e requests
  Exceptions/      # mapeamento de exceções para respostas HTTP
```

## Regras de negócio implementadas

- Usuários comuns podem transferir; lojistas apenas recebem
- Validação de saldo antes da transferência
- Transação atomica (rollback em falha)
- Autorizador externo (GET)
- Notificação externa (POST) sem impactar a transação

## Integrações externas

- Autorizador: `AUTHORIZATION_URL`
- Notificacao: `NOTIFICATION_URL`

Os timeouts podem ser ajustados via `AUTHORIZATION_TIMEOUT` e `NOTIFICATION_TIMEOUT`.

## Comandos uteis

```bash
# subir containers
 docker compose up -d --build

# migrations e seeders
 docker compose exec app php artisan migrate --seed

# testes unitarios
 docker compose exec app php artisan test

# PHPStan
 docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G
```

## Proximo desafio

Reimplementar este mesmo projeto usando Hyperf.
