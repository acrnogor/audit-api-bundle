<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Resolver;

use Acrnogor\AuditApiBundle\Exception\EntityClassNotFoundException;

final readonly class EntityClassResolver
{
    /**
     * @var array<string, class-string> shortname => entity FQCN
     */
    private readonly array $shortnameToClass;

    /**
     * @param array<class-string, string> $entityClassmap entity FQCN => shortname, as configured under "entity_classmap"
     */
    public function __construct(
        array $entityClassmap,
    ) {
        $shortnameToClass = [];
        foreach ($entityClassmap as $class => $shortname) {
            if (isset($shortnameToClass[$shortname])) {
                throw new \InvalidArgumentException(\sprintf('Shortname "%s" is mapped to both "%s" and "%s" in "acrnogor_audit_api.entity_classmap".', $shortname, $shortnameToClass[$shortname], $class));
            }

            $shortnameToClass[$shortname] = $class;
        }

        $this->shortnameToClass = $shortnameToClass;
    }

    /**
     * @return class-string
     *
     * @throws EntityClassNotFoundException
     */
    public function resolve(string $shortname): string
    {
        return $this->shortnameToClass[$shortname] ?? throw EntityClassNotFoundException::forShortname($shortname);
    }

    public function supports(string $shortname): bool
    {
        return isset($this->shortnameToClass[$shortname]);
    }
}
