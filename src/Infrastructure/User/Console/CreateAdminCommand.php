<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Console;

use App\Application\User\Command\CreateAdmin\CreateAdminCommand as CreateAdminApplicationCommand;
use App\Application\User\Command\CreateAdmin\CreateAdminCommandHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command: Créer un administrateur.
 *
 * Pourquoi une commande console ?
 * - Automatisation (cron jobs, CI/CD)
 * - Administration système
 * - Développement et débogage
 * - Migrations de données
 */
#[AsCommand(
    name: 'app:user:create-admin',
    description: 'Crée un nouvel administrateur'
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly CreateAdminCommandHandler $handler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'administrateur')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom d\'utilisateur (doit être unique)')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        try {
            $command = new CreateAdminApplicationCommand(
                email: $email,
                name: $name,
                password: $password
            );

            $userId = ($this->handler)($command);

            $io->success(\sprintf(
                'Administrateur créé avec succès ! ID: %s',
                $userId->value()
            ));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
