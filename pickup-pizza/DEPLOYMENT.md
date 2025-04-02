# PISA Pizza Pickup App - Production Deployment Checklist

This document outlines the steps required to deploy the PISA Pizza Pickup App to a production environment.

## Pre-Deployment Tasks

-   [ ] Run all tests to ensure functionality
    ```
    php artisan test
    ```
-   [ ] Set environment variables in `.env.production`
    -   [ ] APP_ENV=production
    -   [ ] APP_DEBUG=false
    -   [ ] APP_URL=https://pickup.pisapizza.ca
    -   [ ] Database credentials
    -   [ ] Stripe API keys
    -   [ ] Mailgun API keys
    -   [ ] Other service credentials
-   [ ] Set proper file permissions
    -   [ ] `storage/` directory is writable
    -   [ ] `bootstrap/cache/` directory is writable
-   [ ] Ensure the server meets requirements:
    -   [ ] PHP 8.1+ with required extensions
    -   [ ] MySQL 8.0+
    -   [ ] Composer
    -   [ ] Node.js and npm for frontend assets

## Database Configuration

-   [ ] Create production database and user
-   [ ] Update database configuration in `.env.production`
-   [ ] Run migrations
    ```
    php artisan migrate --force
    ```
-   [ ] Seed initial data if needed
    ```
    php artisan db:seed --class=ProductionSeeder
    ```

## Optimization

-   [ ] Compile frontend assets for production
    ```
    npm run build
    ```
-   [ ] Optimize autoloader
    ```
    composer install --optimize-autoloader --no-dev
    ```
-   [ ] Generate application caches
    ```
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
-   [ ] Set up opcache for PHP
-   [ ] Configure your web server for static file caching

## Web Server Setup

-   [ ] Configure Nginx or Apache virtual host
-   [ ] Set up SSL certificate (Let's Encrypt or other)
-   [ ] Configure proper redirects from HTTP to HTTPS
-   [ ] Set up proper headers for security (HSTS, XSS Protection, etc.)
-   [ ] Configure access and error logs

## Security

-   [ ] Run security checks
    ```
    composer audit
    ```
-   [ ] Ensure sensitive configuration is not in source control
-   [ ] Verify rate limiting is properly configured
-   [ ] Configure CSRF protection
-   [ ] Set up a firewall on the server
-   [ ] Ensure only necessary ports are open

## Monitoring & Maintenance

-   [ ] Set up application logging
-   [ ] Configure error reporting
-   [ ] Set up backup system for database and files
-   [ ] Configure cron jobs for scheduled tasks:
    ```
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```
-   [ ] Set up health check endpoint
-   [ ] Configure monitoring alerts

## Post-Deployment Tasks

-   [ ] Verify the site is accessible via the intended URL
-   [ ] Test critical flows:
    -   [ ] Order placement
    -   [ ] Payment processing (both types)
    -   [ ] Admin functions
-   [ ] Test email sending functionality
-   [ ] Verify SSL certificate works correctly
-   [ ] Check for any console errors or 404s

## Emergency Procedures

-   [ ] Document rollback procedure
-   [ ] Prepare database backup restore procedure
-   [ ] Establish service outage communication plan

## Performance Testing

-   [ ] Run load testing to ensure the application can handle expected traffic
-   [ ] Verify response times are acceptable
-   [ ] Check resource utilization under load

## Documentation

-   [ ] Update system documentation
-   [ ] Document deployment process for future updates
-   [ ] Create runbook for common issues

---

## Deployment Commands Sequence

```bash
# Clone or pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Set up environment
cp .env.production .env

# Run database migrations
php artisan migrate --force

# Optimize the application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear old sessions (if needed)
php artisan session:gc

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```
