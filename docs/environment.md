# Environment

Key `.env` variables

- App: APP_NAME, APP_ENV, APP_KEY, APP_URL, APP_LOCALE (`en|lt|da`), APP_FALLBACK_LOCALE
- Logging: LOG_CHANNEL, LOG_LEVEL
- DB: DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Cache/Queue/Session: CACHE_DRIVER, QUEUE_CONNECTION (`database|redis`), SESSION_DRIVER
- Mail: MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS, MAIL_FROM_NAME
- Redis (optional): REDIS_HOST, REDIS_PORT, REDIS_PASSWORD
- Filesystems (S3): FILESYSTEM_DISK=s3, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET, AWS_URL
- Billing (Stripe): STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET
- Trials: TRIAL_DAYS=14

Profiles
- Local: APP_ENV=local, APP_DEBUG=true
- Staging/Prod: APP_DEBUG=false, correct APP_URL, set secure session/cookie

Never commit real secrets. Use `.env` and a secrets manager.
 
Notes
- Password reset and verification emails (Fortify) require a working mailer configuration.