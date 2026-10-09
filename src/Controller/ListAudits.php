<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Acrnogor\AuditApiBundle\Repository\AuditRepository;
use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{shortName}/{entityId}',
    methods: ['GET']
)]
final readonly class ListAudits
{
    public function __construct(
        private EntityClassResolver $resolver,
        private EntityManagerInterface $entityManager,
        private AuditRepository $auditRepository,
    ) {
        // constructor here
    }


    public function __invoke(string $shortName, string $entityId): JsonResponse
    {
        $fqcn = $this->resolver->resolve($shortName);

        // get table name from entity class
        $entityTableName = $this->entityManager->getClassMetadata($fqcn)->getTableName();
        $auditTableName = sprintf('audit_%s', $entityTableName);

        $results = $this->auditRepository->findByEntityId($auditTableName, $entityId);

        return new JsonResponse($results);
    }
}
