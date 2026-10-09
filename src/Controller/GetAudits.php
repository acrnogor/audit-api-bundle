<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/audits/{entityShortname}/{entityIdentifier}',
    name: 'acrnogor_audit_api_get_audits',
    methods: ['GET']
)]
final class GetAudits
{
    public function __invoke(string $shortName, string $id): JsonResponse
    {
        // resolve entity shortname to entity class

        // get table name from entity class

        // fetch data for given criteria
        return new JsonResponse([$shortName => $id]);
    }
}
