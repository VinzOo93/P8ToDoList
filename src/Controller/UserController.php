<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function list(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/list.html.twig', ['users' => $entityManager->getRepository(User::class)->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager ,UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $userRepo = $entityManager->getRepository(User::class);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($userRepo->findOneBy(['username' => $form->get('username')->getData()])) {
                $this->addFlash('error', "l'utilisateur éxiste déjà.");
                return  $this->redirectToRoute('user_create');
            }
            $role = $form->get('roles');
            $user->setRoles($role->getData());
            $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');

        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function edit(User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('roles');
            $user->setRoles($role->getData());
            $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
