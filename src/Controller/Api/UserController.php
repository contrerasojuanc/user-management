<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\UserManagement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function getUserAction($id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $view = $this->view([$user], 200);

        return $this->handleView($view);
    }

    public function postUserAction(Request $request)
    {
        $user = $this->processForm($request, new User());

        $view = $this->view([$user], 200);

        return $this->handleView($view);
    }

    public function putUserAction(Request $request, $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $user = $this->processForm($request, $user, false);

        $view = $this->view([$user], 200);

        return $this->handleView($view);
    }

    public function deleteUserAction(Request $request, $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $this->userManagement->delete($user);

        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    private function processForm(Request $request, User $user, $isCreating = true)
    {
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            if($isCreating)
                $this->userManagement->create($user);
            else
                $this->userManagement->update($user);
        }

        return !$form->isValid() ? $form : $user;
    }

}
