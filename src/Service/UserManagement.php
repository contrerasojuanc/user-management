<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * A service for User management with CRUD methods
 */
class UserManagement
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator, UserRepository $users)
    {
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function create(Request $input)
    {
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $fullName = $input->getArgument('full-name');
        $isAdmin = $input->getOption('admin');

        // make sure to validate the user data is correct
        $this->validateUserData($username, $plainPassword, $email, $fullName);

        // create the user and encode its password
        $user = new User();
        $user->setFullName($fullName);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private function validateUserData($username, $plainPassword, $email, $fullName)
    {
        // first check if a user with the same username already exists.
        $existingUser = $this->users->findOneBy(['username' => $username]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" username.', $username));
        }

        // validate password and email if is not this input means interactive.
        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateFullName($fullName);

        // check if a user with the same email already exists.
        $existingEmail = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingEmail) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }
    }

}
