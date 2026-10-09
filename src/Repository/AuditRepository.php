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

    public function findByEntityId(string $auditTableName, string $entityId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->connection->getDatabasePlatform()->quoteSingleIdentifier($auditTableName))
            ->where('object_id = :objectId')
            ->orderBy('created_at', 'DESC')
            ->setParameter('objectId', $entityId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findById(string $auditTableName, int $recordId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->connection->getDatabasePlatform()->quoteSingleIdentifier($auditTableName))
            ->where('id = :recordId')
            ->setParameter('recordId', $recordId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: [];
    }
}
