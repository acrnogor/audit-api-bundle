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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
        private NormalizerInterface $normalizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity FQCN, e.g. "App\\Entity\\User"')
            ->addArgument('object_id', InputArgument::REQUIRED, 'Identifier of the entity record')
            ->addOption('limit', 'l', InputArgument::OPTIONAL, 'Limit the number of audit logs displayed', 24);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fqcn = (string) $input->getArgument('entity');
        $objectId = (int) $input->getArgument('object_id');
        $limit = (int) $input->getOption('limit');

        $query = $this->reader->createQuery($fqcn, [
            'object_id' => $objectId,
            'page_size' => $limit
        ]);

        $entries = $query
            ->resetOrderBy()
            ->addOrderBy(Query::CREATED_AT)
            ->addOrderBy(Query::ID)
            ->execute();

        if ([] === $entries) {
            $io->warning(\sprintf('No audit logs found for "%s" with id "%s".', $fqcn, $objectId));

            return Command::SUCCESS;
        }

        $tableData = [];

        foreach ($entries as $entry) {
            $result = $this->normalizer->normalize($entry);

            // encode this to json and pretty print
            $changes = json_encode($result['diffs']);

            $tableData[] = [
                'operation'     => $result['type'],
                'modified_by'   => substr($result['blame_user'], 0, 32) . '...',
                'modified_at'   => $result['created_at'],
                'changes'       => $changes,
            ];
        }

        $io->title(\sprintf('Audit logs for %s #%s', $fqcn, $objectId));
        $io->table(
            ['Operation', 'Modified by', 'Modified at', 'Changes'],
            $tableData,
        );

        return Command::SUCCESS;
    }
}
