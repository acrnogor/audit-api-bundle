<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Serializer;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Snake-cases Entry attributes and maps the user* accessors back to the blame_* column names.
 */
final readonly class EntryNameConverter implements NameConverterInterface
{
    private const array MAP = [
        'userId' => 'blame_id',
        'username' => 'blame_user',
        'userFqdn' => 'blame_user_fqdn',
        'userFirewall' => 'blame_user_firewall',
    ];

    private CamelCaseToSnakeCaseNameConverter $inner;

    public function __construct()
    {
        $this->inner = new CamelCaseToSnakeCaseNameConverter();
    }

    public function normalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        return self::MAP[$propertyName] ?? $this->inner->normalize($propertyName);
    }

    public function denormalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        $key = array_search($propertyName, self::MAP, true);

        return false !== $key ? $key : $this->inner->denormalize($propertyName);
    }
}
