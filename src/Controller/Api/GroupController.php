<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Service\GroupManagement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;

class GroupController extends FOSRestController
{
    private $groupRepository;

    public function __construct(GroupManagement $groupManagement, GroupRepository $groupRepository)
    {
        $this->groupManagement = $groupManagement;
        $this->groupRepository = $groupRepository;
    }

    public function getGroupsAction()
    {
        $groups = $this->groupRepository->findAll();
        $view = $this->view($groups, 200);

        return $this->handleView($view);
    }

    public function getGroupAction($id)
    {
        $group = $this->groupRepository->findOneBy(['id' => $id]);

        $view = $this->view([$group], 200);

        return $this->handleView($view);
    }

    public function postGroupAction(Request $request)
    {
        $group = $this->processForm($request, new Group());

        $view = $this->view([$group], 200);

        return $this->handleView($view);
    }

    public function putGroupAction(Request $request, $id)
    {
        $group = $this->groupRepository->findOneBy(['id' => $id]);

        $group = $this->processForm($request, $group, false);

        $view = $this->view([$group], 200);

        return $this->handleView($view);
    }

    public function deleteGroupAction(Request $request, $id)
    {
        $group = $this->groupRepository->findOneBy(['id' => $id]);

        $this->groupManagement->delete($group);

        $view = $this->view([], 200);

        return $this->handleView($view);
    }

    private function processForm(Request $request, User $group, $isCreating = true)
    {
        $form = $this->createForm(UserType::class, $group, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            if($isCreating)
                $this->groupManagement->create($group);
            else
                $this->groupManagement->update($group);
        }

        return !$form->isValid() ? $form : $group;
    }

}
