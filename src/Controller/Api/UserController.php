<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Service\UserManagement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;

class UserController extends FOSRestController
{
    private $userRepository;

    public function __construct(UserManagement $userManagement, UserRepository $userRepository)
    {
        $this->userManagement = $userManagement;
        $this->userRepository = $userRepository;
    }

    public function getUsersAction()
    {
        $users = $this->userRepository->findAll();
        $view = $this->view($users, 200);

        return $this->handleView($view);
    }

    public function getUserAction(Request $request)
    {
        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    public function postUserAction(Request $request)
    {
        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    public function putUserAction(Request $request)
    {
        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    public function deleteUserAction(Request $request)
    {
        $view = $this->view([], 200);

        return $this->handleView($view);
    }

}
