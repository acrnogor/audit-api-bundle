<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class HelloController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello World']);
    }
}
