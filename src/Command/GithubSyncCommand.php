<?php
declare(strict_types=1);
namespace App\Command;

use App\Service\GithubSyncService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Throwable;

final class GithubSyncCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        try {
            $result = (new GithubSyncService())->synchronize();
            $io->out(sprintf('Imported %d public contributions, %d repos.', $result['imported'], $result['repositories']));
            return static::CODE_SUCCESS;
        } catch (Throwable $error) {
            $io->err('Import failed: ' . $error->getMessage());
            return static::CODE_ERROR;
        }
    }
}
