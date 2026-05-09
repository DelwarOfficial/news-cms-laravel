# NewsCore CMS — Final Deployment Checklist

## Pre-Deployment

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY`
- [ ] Configure production database (MySQL/PostgreSQL)
- [ ] Set up Redis for cache, session, and queue
- [ ] Configure queue worker (`php artisan queue:work`)
- [ ] Set up scheduled tasks (cron)
- [ ] Configure mail (SMTP / SES / Mailgun)
- [ ] Set up SSL/HTTPS
- [ ] Configure backup system (daily database + files)

## Security

- [ ] Enable rate limiting on login and API
- [ ] Set secure file upload validation
- [ ] Enable CSRF protection
- [ ] Configure Content Security Policy (CSP)
- [ ] Regular security audits

## Performance

- [ ] Enable OPcache
- [ ] Configure Redis caching
- [ ] Enable queue for heavy jobs (image processing, emails)
- [ ] Optimize database queries (indexes, eager loading)
- [ ] Set up CDN for static assets (optional)

## Monitoring

- [ ] Set up error tracking (Sentry / Bugsnag)
- [ ] Configure logging (daily logs + rotation)
- [ ] Set up uptime monitoring
- [ ] Configure performance monitoring

## Final Steps

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan storage:link`
- [ ] Test all critical flows (login, post publishing, media upload)
- [ ] Create first backup

---

**Deployment Date:** ________________
**Deployed By:** ________________
**Version:** 1.0.0

*Generated for NewsCore CMS — May 2026*