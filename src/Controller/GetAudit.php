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
        $criteria = [
            'object_id' => $entityId,
            'id' => $recordId,
        ];
        $query = $this->reader->createQuery($fqcn, $criteria);
        $result = $query->execute();

        if (empty($result) || !isset($result[0])) {
            $message = sprintf('Audit record "%d" for "%s" not found.', $recordId, $fqcn);
            throw new NotFoundHttpException($message);
        }

        return new JsonResponse($this->normalizer->normalize($result[0]));
    }
}
