<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Question\Question;
use App\Entity\User;

class CreateUserCommand extends Command {
    protected static $defaultName = 'app:create-user';

    protected static $defaultDescription = "Create the first User (admin). If a user already exists in DB, nothing happens";

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher) {
        $this->em = $entityManager;
        $this->hasher = $hasher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $helper = $this->getHelper('question');
        $questionLogin = new Question("username ? ");
        $questionPassword = new Question("password ? ");
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $login = $helper->ask($input, $output, $questionLogin);
        $password = $helper->ask($input, $output, $questionPassword);

        $output->writeln("Username : " . $login);
        $output->writeln("Password : " . $password);

        // No user must be in DB
        $users = $this->em->getRepository(user::class)->findAll();
        if ($users) {
            $output->writeln(count($users) . 'user(s) in DB. No creation allowed');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($login);
        $user->setPassword($password);

        $hash = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("Success !");
        return Command::SUCCESS;

        return Command::SUCCESS;
    }
}