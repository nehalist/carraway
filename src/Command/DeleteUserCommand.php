<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteUserCommand extends Command
{
    protected static $defaultName = 'app:delete-user';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em      = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name     = $input->getArgument('name');

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $name]);
        if (! $user) {
            $output->writeln("<error>User $name not found</error>");
            return 1;
        }

        $this->em->remove($user);
        $this->em->flush();

        $output->writeln("<info>User $name successfully deleted</info>");

        return 0;
    }
}
