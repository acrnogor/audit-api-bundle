<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{shortName}/{entityId}/history?page={page}&size={size}',
    methods: ['GET']
)]
final readonly class ListAuditHistory
{
    public function __construct(
        private EntityClassResolver $resolver,
        private Reader $reader,
        private array $pagination,
    ) {
        // constructor here
    }


    public function __invoke(string $shortName, string $entityId, int $page = 1, int $size = 24): JsonResponse
    {
        $size = min($size, $this->pagination['max_limit'] ?? 100);
        $page = max(1, $page);

        $fqcn = $this->resolver->resolve($shortName);

        $criteria = [
            'object_id' => $entityId,
            'page' => $page,
            'size' => $size,
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
