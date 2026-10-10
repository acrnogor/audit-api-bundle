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
        private int $limit,
        private int $maxLimit,
    ) {
    }

    public function __invoke(
        string $shortName,
        string $entityId,
        #[MapQueryParameter] ?int $page = null,
        #[MapQueryParameter] ?int $size = null,
    ): JsonResponse {
        $page = max(1, $page ?? 1);
        $size = max(1, min($size ?? $this->limit, $this->maxLimit));

        $fqcn = $this->resolver->resolve($shortName);

        $criteria = [
            'object_id' => $entityId,
            'page' => $page,
            'page_size' => $size,
        ];
        $query = $this->reader->createQuery($fqcn, $criteria);

        $total = $query->count();
        $records = array_map($this->normalizeEntry(...), $query->execute());

        $results = [
            'page' => $page,
            'size' => $size,
            'total' => $total,
            'records' => $records,
        ];

        return new JsonResponse($results);
    }

    private function normalizeEntry(Entry $entry): array
    {
        // auditor 3.x exposes data via getters, auditor 4.x via public (hooked) properties
        if (method_exists($entry, 'getId')) {
            $createdAt = $entry->getCreatedAt();

            return [
                'id' => $entry->getId(),
                'type' => $entry->getType(),
                'object_id' => $entry->getObjectId(),
                'discriminator' => $entry->getDiscriminator(),
                'transaction_hash' => $entry->getTransactionHash(),
                'diffs' => $entry->getDiffs(),
                'blame_id' => $entry->getUserId(),
                'blame_user' => $entry->getUsername(),
                'blame_user_fqdn' => $entry->getUserFqdn(),
                'blame_user_firewall' => $entry->getUserFirewall(),
                'ip' => $entry->getIp(),
                'created_at' => $createdAt?->format(\DateTimeInterface::ATOM),
            ];
        }

        return [
            'id' => $entry->id,
            'type' => $entry->type,
            'object_id' => $entry->objectId,
            'discriminator' => $entry->discriminator,
            'transaction_hash' => $entry->transactionHash,
            'diffs' => $entry->getDiffs(),
            'blame_id' => $entry->userId,
            'blame_user' => $entry->username,
            'blame_user_fqdn' => $entry->userFqdn,
            'blame_user_firewall' => $entry->userFirewall,
            'ip' => $entry->ip,
            'created_at' => $entry->createdAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
