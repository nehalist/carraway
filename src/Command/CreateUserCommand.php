<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    protected $em;

    protected $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em      = $em;
        $this->encoder = $encoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('name')
             ->addArgument('password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name     = $input->getArgument('name');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setUsername($name);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("<info>User $name successfully created</info>");

        return 0;
    }
}
