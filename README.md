# AcrnogorAuditApiBundle

Symfony bundle providing an audit API.

## Requirements

- PHP 8.2+
- Symfony 6.4 or 7.x

## Installation

```bash
composer require acrnogor/audit-api-bundle
```

If you are not using Symfony Flex, enable the bundle in `config/bundles.php`:

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

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT — see [LICENSE](LICENSE).
