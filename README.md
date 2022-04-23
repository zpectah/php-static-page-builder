# PHP static page builder

!! In development !!

## Motivation
Motivation was to create simple page builder with PHP, some few settings and generate page view with Blade templating system. Also using latest Vue v.3 for Javascript component features.

## Description
Simple PHP static page builder with multi-language content and basic page features

## Dependencies
- Composer
- NPM / Yarn package manager
- PHP v7.4+
- Vue 3
- BladeOne
- Gulp
- Webpack

## Features
- PHP templating with BladeOne
- Javascript options with Vue components
- Multi-language content, just add new language content to configuration
- Easy page managing from configuration

## Development configuration
### Apache Server for development
#### Virtual Host
```
<VirtualHost *:80>
    DocumentRoot "/path-to-project-root/.../php-static-page-builder/dev/"
    ServerName php-static-page-builder
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "/path-to-project-root/.../php-static-page-builder/test/"
    ServerName test.php-static-page-builder
</VirtualHost>
```
#### Hosts
```
127.0.0.1		php-static-page-builder
127.0.0.1		test.php-static-page-builder
```

## Development
### Install packages
- ``% yarn install`` - Install node packages

### Prepare vendors
- ``% yarn initial`` - Prepare PHP vendors

### Watch
- ``% yarn start`` - Watching changes for whole project

### Build
- ``% yarn build:dev`` - Create development bundle
- ``% yarn build:test`` - Create test bundle
- ``% yarn build:prod`` - Create production bundle

## Deployment
Just run build and copy ``/prod`` folder to your destination.