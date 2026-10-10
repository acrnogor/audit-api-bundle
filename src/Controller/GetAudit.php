<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Filter\SimpleFilter;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Query;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route(
    path: '/{shortName}/{entityId}/{recordId}',
    requirements: ['recordId' => '\d+'],
    methods: ['GET']
)]
final readonly class GetAudit
{
    public function __construct(
        private EntityClassResolver $resolver,
        private Reader $reader,
        private NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(string $shortName, string $entityId, int $recordId): JsonResponse
    {
        $fqcn = $this->resolver->resolve($shortName);

        $entry = $this->reader->createQuery($fqcn, ['object_id' => $entityId])
            ->addFilter(new SimpleFilter(Query::ID, $recordId))
            ->resetOrderBy()
            ->execute()[0] ?? null;

        if (null === $entry) {
            throw new NotFoundHttpException(sprintf('Audit record "%d" not found.', $recordId));
        }

        return new JsonResponse($this->normalizer->normalize($entry));
    }
}
