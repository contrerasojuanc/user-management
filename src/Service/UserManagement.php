<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserGroup;
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
    private $users;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator, UserRepository $users)
    {
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->users = $users;
    }

    /**
     * For creating users
     */
    public function create(User $user, $groups, $isAdmin = false)
    {
        $fullName = $user->getFullName();
        $username = $user->getUsername();
        $plainPassword = $user->getPassword() ?? random_bytes(10);
        $email = $user->getEmail();

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
            foreach ($groups as $group) {
                $userGroup = new UserGroup();
                $userGroup->setGroupId($group);
                $user->addGroup($userGroup);
                $this->entityManager->persist($userGroup);
            }
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * For updating users
     */
    public function update(User $user, $groups)
    {

        try {
            $this->entityManager->persist($user);

            foreach ($user->getGroups() as $group) {
                $userGroup = new UserGroup();
                $userGroup->setGroupId($group);
                $user->addGroup($userGroup);

                $user->removeGroup($userGroup);
            }

            foreach ($groups as $group) {
                $userGroup = new UserGroup();
                $userGroup->setGroupId($group);
                $user->addGroup($userGroup);
                $this->entityManager->persist($userGroup);
            }
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * For listing users
     */
    public function listing()
    {

        $usres = $this->users->findAll();

        return $users;

    }

    /**
     * For reading users
     */
    public function read($username)
    {

        $existingUser = $this->users->findOneBy(['username' => $username]);

        return $existingUser;

    }

    /**
     * For deleting users
     */
    public function delete(User $user)
    {

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

    }

    public function validateUserData(User $user)
    {
        // first check if a user with the same username already exists.
        $existingUser = $this->users->findOneBy(['username' => $user->getUsername()]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" username.', $user->getUsername()));
        }

        $this->validator->validateEmail($user->getEmail());
        $this->validator->validateFullName($user->getFullName());

        // check if a user with the same email already exists.
        $existingEmail = $this->users->findOneBy(['email' => $user->getEmail()]);

        if (null !== $existingEmail) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $user->getEmail()));
        }
    }

}
