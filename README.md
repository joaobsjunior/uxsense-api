# uxsense-api

REST API da plataforma UXSense (Laravel 13, PHP 8.3+).

## Requisitos

- PHP >= 8.3 com as extensões `pdo_mysql`, `mbstring`, `openssl`, `curl`, `ctype`, `tokenizer`, `xml`
- Composer 2
- Node.js 22+ (apenas para compilar os assets com Vite)
- MySQL com o schema legado da aplicação (`administrator`, `client`, `device`, `scheduler`, ...)

## Instalação

```bash
composer install
cp .env.development .env      # ajuste DB_*, MAIL_*, FCM_SERVER_KEY, PUSH_CHECK_TOKEN
php artisan key:generate
php artisan migrate --force   # cria a tabela de cache e alarga as colunas de senha/token
npm install && npm run build  # opcional: assets
php artisan serve
```

## Testes

```bash
php vendor/bin/phpunit
composer audit && npm audit
```

## Autenticação

| Consumidor        | Cabeçalhos                          | Middleware    |
|-------------------|-------------------------------------|---------------|
| Painel (admin)    | `GSX-CODE` + `GSX-TOKEN`            | `auth.admin`  |
| Aplicativo        | `GSX-DEVICE` + `GSX-TOKEN`          | `auth.client` |
| Cron (push)       | `GSX-CRON-TOKEN` = `PUSH_CHECK_TOKEN` | `auth.cron` |

As senhas são armazenadas com bcrypt. Contas antigas (hash `sha1(md5())`)
continuam funcionando e são migradas automaticamente para bcrypt no
primeiro login bem-sucedido. Por isso a migration que alarga as colunas
`password`/`token` **precisa** ser executada antes do deploy.

## Variáveis de ambiente relevantes

- `APP_DEBUG=false` em produção (com `true`, as respostas de erro incluem classe, arquivo e linha da exceção).
- `CORS_ALLOWED_ORIGINS` – origens permitidas, separadas por vírgula (`*` libera todas).
- `TRUSTED_PROXIES` – IPs/CIDRs dos proxies reversos confiáveis (ou `*`).
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` – SMTP usado pelo PHPMailer.
- `FCM_SERVER_KEY` – chave do Firebase Cloud Messaging para notificações push.
- `PUSH_CHECK_TOKEN` – segredo compartilhado exigido em `GET /api/push/check`.
