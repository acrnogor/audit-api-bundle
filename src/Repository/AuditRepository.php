<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Repository;

use Doctrine\DBAL\Connection;

final readonly class AuditRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findByObjectId(string $auditTableName, string $objectId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->connection->quoteSingleIdentifier($auditTableName))
            ->where('object_id = :objectId')
            ->orderBy('created_at', 'DESC')
            ->setParameter('objectId', $objectId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
