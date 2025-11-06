# Deploying to Laravel Vapor (AWS Serverless)

This guide covers a production deployment on Laravel Vapor (AWS Lambda, API Gateway, SQS, RDS, S3).

## Prerequisites
- Laravel Vapor account and CLI (`composer global require laravel/vapor-cli`)
- AWS account with Vapor connected
- Domain/DNS managed via Route53 or provider you can delegate to Vapor

## 1) Vapor Project Setup
1. `vapor login`
2. `vapor init` → follow prompts to create `vapor.yml`

Example `vapor.yml`:
```
id: semera-asset-manager
name: semera-asset-manager
environments:
  staging:
    runtime: 'php-8.2'
    memory: 1024
    timeout: 60
    cli-timeout: 600
    database: semera-staging
    cache: semera-staging
    queue: semera-staging
    storage: semera
    build:
      - 'composer install --no-dev --prefer-dist --optimize-autoloader'
      - 'php artisan config:cache'
      - 'npm ci && npm run build'
    deploy:
      - 'php artisan migrate --force'
  production:
    runtime: 'php-8.2'
    memory: 1024
    timeout: 60
    cli-timeout: 600
    database: semera-prod
    cache: semera-prod
    queue: semera-prod
    storage: semera
    domain: app.example.com
    build:
      - 'composer install --no-dev --prefer-dist --optimize-autoloader'
      - 'php artisan config:cache'
      - 'npm ci && npm run build'
    deploy:
      - 'php artisan migrate --force'
```

## 2) Services (AWS)
- RDS (MySQL 8): Create via Vapor “Databases” and attach to env
- SQS: Create via Vapor “Queues”; set `QUEUE_CONNECTION=sqs`
- Redis (ElastiCache) optional: use for cache/session
- S3: Create via Vapor “Storage” for file uploads; set `FILESYSTEM_DISK=s3`

## 3) Environment Variables
- Vapor Dashboard → Project → Environments → Variables
- Set:
  - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://app.example.com`
  - DB vars are handled by Vapor, but you can add overrides
  - `QUEUE_CONNECTION=sqs`, `CACHE_STORE=redis|dynamodb|file`, `SESSION_DRIVER=cookie|dynamodb|redis`
  - `MAIL_MAILER=ses` and SES region/access keys or SMTP
  - Broadcasting/WebSockets (Reverb unsupported on Lambda; use Pusher or Ably if needed)

## 4) Scheduling & Queues
- Vapor Scheduler: Project → Schedules → Add schedule: `php artisan schedule:run` every minute
- Vapor Queues: enable SQS; artisan jobs run automatically via SQS consumer

## 5) Domains & SSL
- Project → Domains → Attach `app.example.com`; Vapor provisions ACM certificate
- Update DNS per Vapor’s guidance (Route53 or external CNAME)

## 6) Deploy
- `vapor deploy production` (or staging)
- Tail logs: `vapor logs production`

## 7) Files & Storage
- Use S3 for `FILESYSTEM_DISK`; no `storage:link` needed
- Ensure upload size limits fit Lambda/API Gateway defaults (configure binary types if needed)

## 8) Mail
- Prefer AWS SES in same region; verify sender domain; set MAIL_* vars

## 9) Post‑Deploy Checks
- Verify DB migrations ran; test critical flows: Vendors/Products/POs/Software/Contracts CRUD, Reports, Imports/Exports (exports will be synchronous CSV downloads)
- Confirm alerts are generated (scheduler) and notifications are delivered (SES)

## Notes & Caveats
- Long‑running tasks: offload to SQS; Lambda timeout is limited
- WebSockets: use Pusher/Ably instead of Reverb on Lambda
- Caching config/routes/views reduces cold‑start impact

## Rollback
- `vapor deploy production --rollback`

