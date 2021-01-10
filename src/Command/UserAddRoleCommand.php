<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserAddRoleCommand extends Command
{
    protected static $defaultName = 'user:add-role';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        string $name = null
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager  = $entityManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add role to user')
            ->addArgument('email', InputArgument::OPTIONAL, 'user email')
            ->addArgument('role', InputArgument::OPTIONAL, 'role title');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $argEmail = $input->getArgument('email');
        $argRole  = $input->getArgument('role');

        if (null === $argEmail) {
            $email = $io->ask('Enter user email', 'user@gmail.com');

            $input->setArgument('email', $email);
        }

        $user = $this->userRepository->findOneBy(['email' => $input->getArgument('email')]);

        if (!$user) {
            $io->error('User does not exist!');

            return Command::FAILURE;
        }

        if (null === $argRole) {
            $role = $io->ask('Enter user role', 'ROLE_USER');

            $input->setArgument('role', $role);
        }

        $user->setRoles([$input->getArgument('role')]);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException $e) {
        }

        $io->success('Role successful added');

        return Command::SUCCESS;
    }
}
