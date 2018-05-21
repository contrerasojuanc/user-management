<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\GroupGroup;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Service\GroupManagement;
use App\Utils\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin/group")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class GroupController extends AbstractController
{

    public function __construct(GroupManagement $groupManagement)
    {
        $this->groupManagement = $groupManagement;
    }

    /**
     * Lists all Groups entities.
     *
     * @Route("/", defaults={"page": "1"}, name="admin_group_index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="index_paginated")
     * @Method("GET")
     */
    public function index(GroupRepository $groups, $page): Response
    {
        $groups = $groups->findByPage($page);

        return $this->render('admin/group/index.html.twig', ['groups' => $groups]);
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/new", name="admin_group_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(Request $request): Response
    {
        $group = new Group();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(GroupType::class, $group)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            //Using Service
            $this->groupManagement->create($group);

            // Flash messages are used to notify the group about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'Group created successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_group_new');
            }

            return $this->redirectToRoute('admin_group_index');
        }

        return $this->render('admin/group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_group_show")
     * @Method("GET")
     */
    public function show(Group $group): Response
    {
        return $this->render('admin/group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_group_edit")
     * @Method({"GET", "POST"})
     */
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Using Service
            $this->groupManagement->update($group);

            $this->addFlash('success', 'Group updated successfully');

            return $this->redirectToRoute('admin_group_edit', ['id' => $group->getId()]);
        }

        return $this->render('admin/group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}/delete", name="admin_group_delete")
     * @Method("POST")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the group accessing this resource).
     */
    public function delete(Request $request, Group $group): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_group_index');
        }

        //Using Service
        $this->groupManagement->delete($group);

        $this->addFlash('success', 'Group deleted successfully');

        return $this->redirectToRoute('admin_group_index');
    }

    /**
     * @Route("/search", name="admin_group_search")
     * @Method("GET")
     */
    public function search(Request $request, GroupRepository $groups): Response
    {

        $foundGroups = $groups->findBySearch($request);

        return $this->render('admin/group/index.html.twig', ['groups' => $foundGroups]);

    }
}
