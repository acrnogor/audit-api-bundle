# AcrnogorAuditApiBundle

Symfony bundle providing an audit API.

## Requirements

- PHP 8.2+
- Symfony 6.4 or 7.x

## Installation

```bash
composer require acrnogor/audit-api-bundle
```

With Symfony Flex, the bundle's recipe (a contrib recipe, so Flex asks you to
confirm it) registers the bundle and creates the two files below.

Without Flex, enable the bundle in `config/bundles.php` and create those files
yourself:

```php
return [
    // ...
    Acrnogor\AuditApiBundle\AcrnogorAuditApiBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/acrnogor_audit_api.yaml
acrnogor_audit_api:
    enabled: true
```

## Routes

```yaml
# config/routes/acrnogor_audit_api.yaml
acrnogor_audit_api:
    resource: '@AcrnogorAuditApiBundle/config/routes.php'
    prefix: /audit
```

| Method | Path     | Response                     |
|--------|----------|------------------------------|
| GET    | `/hello` | `{"message": "Hello World"}` |

## Flex recipe

The Flex recipe lives in [`recipe/`](recipe/) and is mirrored to
[symfony/recipes-contrib](https://github.com/symfony/recipes-contrib) under
`acrnogor/audit-api-bundle/<version>/`. Flex only reads recipes from recipe
repositories, so any change here has to be submitted there to take effect.

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT — see [LICENSE](LICENSE).
