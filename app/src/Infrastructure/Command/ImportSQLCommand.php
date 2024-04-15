<?php
declare(strict_types=1);

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class ImportSQLCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:import-sql')
            ->setDescription('Import data from a SQL file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the SQL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = $input->getArgument('file');

            $sql = file_get_contents($file);

            $this->connection->executeQuery($sql);

            $output->writeln('Data imported successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error importing data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}