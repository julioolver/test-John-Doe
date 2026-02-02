# Pagamentos Simplificados API

Projeto backend para transferencias entre usuarios com carteiras, seguindo regras de negocio, transacao atomica e integracoes externas de autorizacao e notificacao.

## Visao geral

- Usuarios comuns (CPF) podem transferir
- Lojistas (CNPJ) apenas recebem
- Carteiras com saldo em centavos
- Autorizacao externa antes da transferencia
- Notificacao externa apos a transferencia

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
  Domain/          # regras de negocio puras (entidades, VOs, excecoes)
  Application/     # casos de uso, DTOs e contratos (interfaces)
  Infrastructure/  # implementacoes (Eloquent, HTTP, DB)
  Http/            # controllers e requests
  Exceptions/      # mapeamento de excecoes para respostas HTTP
```

## Regras de negocio implementadas

- Usuarios comuns podem transferir; lojistas apenas recebem
- Validacao de saldo antes da transferencia
- Transacao atomica (rollback em falha)
- Autorizador externo (GET)
- Notificacao externa (POST) sem impactar a transacao

## Integracoes externas

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
