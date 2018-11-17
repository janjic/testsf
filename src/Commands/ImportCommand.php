<?php
// src/Command/CreateUserCommand.php
namespace App\Commands;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * 1. Extend Command
 * 2. configure
 * 3. execute
 * Class ImportCommand
 * @package App\Command
 */
class ImportCommand extends Command
{
    private $passwordEncoder;

    private $userRepository;

    /**
     * ImportCommand constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository               $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('test:import')

            // the short description shown while running "php bin/console list"
            ->setDescription('Importovanje')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...');
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function insertUsers() {
        $users = array();
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $users[]= $user;
        }
        $this->userRepository->insertUsers($users);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $this->insertUsers();

    }
}
