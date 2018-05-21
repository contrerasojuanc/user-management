<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * A service for User management with CRUD methods
 */
class GroupManagement
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $groups;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator, GroupRepository $groups)
    {
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->groups = $groups;
    }

    /**
     * For creating groups
     */
    public function create(Group $group)
    {
        try {
            $this->entityManager->persist($group);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * For updating groups
     */
    public function update(Group $group)
    {

        try {
            $this->entityManager->persist($group);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * For listing groups
     */
    public function listing()
    {

        $usres = $this->groups->findAll();

        return groups;

    }

    /**
     * For reading groups
     */
    public function read($name)
    {

        $group = $this->groups->findOneBy(['name' => $name]);

        return $group;

    }

    /**
     * For deleting groups
     */
    public function delete(Group $group)
    {
        //Allows delete only if no members
        if($group->getUsers()->isEmpty()) {
            try {
                $this->entityManager->remove($group);
                $this->entityManager->flush();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

}
