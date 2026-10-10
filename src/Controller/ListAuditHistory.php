<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
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
        $records = new \ArrayIterator($query->execute());

        $results = [
            'page' => $page,
            'size' => $size,
            'total' => $total,
            'records' => $records,
        ];

        return new JsonResponse($results);
    }
}
