<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Exception;

final class EntityClassNotFoundException extends \InvalidArgumentException
{
    public static function forShortname(string $shortname): self
    {
        return new self(\sprintf('No entity class is mapped to shortname "%s" in "acrnogor_audit_api.entity_classmap".', $shortname));
    }
}
