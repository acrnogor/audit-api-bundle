<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Command;

use DH\Auditor\Exception\AccessDeniedException;
use DH\Auditor\Exception\InvalidArgumentException;
use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Query;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'audit:logs',
    description: 'Displays all audit log entries for a single entity record.',
)]
final class AuditLogsCommand extends Command
{
    private const array OPERATION_COLORS = [
        // Plain strings: DH\Auditor\Model\TransactionType only exists in auditor 4.x
        'insert' => 'green',
        'update' => 'yellow',
        'remove' => 'red',
        'associate' => 'cyan',
        'dissociate' => 'magenta',
    ];

    public function __construct(
        private readonly Reader $reader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity FQCN, e.g. "App\Entity\User"')
            ->addArgument('object_id', InputArgument::REQUIRED, 'Identifier of the entity record');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $fqcn */
        $fqcn = $input->getArgument('entity');
        /** @var string $objectId */
        $objectId = $input->getArgument('object_id');

        try {
            $query = $this->reader->createQuery($fqcn, ['object_id' => $objectId, 'page_size' => null]);
        } catch (InvalidArgumentException|AccessDeniedException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $entries = $query
            ->resetOrderBy()
            ->addOrderBy(Query::CREATED_AT, 'ASC')
            ->addOrderBy(Query::ID, 'ASC')
            ->execute();

        if ([] === $entries) {
            $io->warning(\sprintf('No audit logs found for "%s" with id "%s".', $fqcn, $objectId));

            return Command::SUCCESS;
        }

        $io->title(\sprintf('Audit logs for %s #%s', $fqcn, $objectId));
        $io->table(
            ['Operation', 'Modified by', 'Modified at', 'Changes'],
            array_map($this->toRow(...), $entries),
        );

        return Command::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function toRow(Entry $entry): array
    {
        $color = self::OPERATION_COLORS[$entry->type] ?? 'white';

        return [
            \sprintf('<fg=%s;options=bold>%s</>', $color, $entry->type),
            $entry->username ?? (null !== $entry->userId ? (string) $entry->userId : '-'),
            $entry->createdAt?->format('Y-m-d H:i:s') ?? '-',
            implode(",\n", $this->formatChanges($entry->getDiffs())),
        ];
    }

    /**
     * Flattens the diff into "field=OLD  ==> field=NEW" lines. Nested (e.g. JSON) fields are dot-separated;
     * plain scalar values (remove/associate summaries) are rendered as "field=VALUE".
     *
     * @param array<array-key, mixed> $diffs
     *
     * @return list<string>
     */
    private function formatChanges(array $diffs, string $prefix = ''): array
    {
        $changes = [];

        foreach ($diffs as $field => $diff) {
            $path = $prefix.$field;

            if (!\is_array($diff)) {
                $changes[] = \sprintf('%s=%s', $path, $this->formatValue($diff));

                continue;
            }

            if ($this->isChange($diff)) {
                $changes[] = \sprintf(
                    '%1$s=%2$s  ==> %1$s=%3$s',
                    $path,
                    $this->formatValue($diff['old'] ?? null),
                    $this->formatValue($diff['new'] ?? null),
                );

                continue;
            }

            array_push($changes, ...$this->formatChanges($diff, $path.'.'));
        }

        return $changes;
    }

    /**
     * @param array<array-key, mixed> $diff
     */
    private function isChange(array $diff): bool
    {
        return [] !== $diff && [] === array_diff(array_keys($diff), ['old', 'new']);
    }

    private function formatValue(mixed $value): string
    {
        return match (true) {
            null === $value => 'NULL',
            \is_bool($value) => $value ? 'true' : 'false',
            \is_scalar($value) => (string) $value,
            default => json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '?',
        };
    }
}
