<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{shortName}/{entityId}/history',
    methods: ['GET']
)]
final readonly class ListAuditHistory
{
    public function __construct(
        private EntityClassResolver $resolver,
        private Reader $reader,
        private int $defaultLimit,
        private int $maxLimit,
    ) {
        // constructor here
    }

    public function __invoke(
        string $shortName,
        string $entityId,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] ?int $size = null,
    ): JsonResponse {
        $page = max(1, $page);
        $size = max(1, min($size ?? $this->defaultLimit, $this->maxLimit));

        $fqcn = $this->resolver->resolve($shortName);

        $query = $this->reader->createQuery($fqcn, [
            'object_id' => $entityId,
            'page' => $page,
            'page_size' => $size,
        ]);

        return new JsonResponse([
            'page' => $page,
            'size' => $size,
            'total' => $query->count(),
            'records' => array_map(static fn (Entry $entry): array => [
                'id' => $entry->id,
                'type' => $entry->type,
                'objectId' => $entry->objectId,
                'discriminator' => $entry->discriminator,
                'transactionHash' => $entry->transactionHash,
                'diffs' => $entry->getDiffs(),
                'extraData' => $entry->extraData,
                'userId' => $entry->userId,
                'username' => $entry->username,
                'userFqdn' => $entry->userFqdn,
                'userFirewall' => $entry->userFirewall,
                'ip' => $entry->ip,
                'createdAt' => $entry->createdAt?->format(\DATE_ATOM),
            ], $query->execute()),
        ]);
    }
}
