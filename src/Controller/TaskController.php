<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function list(EntityManagerInterface $entityManager): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $entityManager->getRepository(Task::class)->findAll()]);
    }

    /**
     * @Route("/profile/tasks/create", name="task_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task->setAuthor($user);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("profile/tasks/{id}/edit", name="task_edit")
     */
    public function edit(Task $task, EntityManagerInterface $entityManager, Request $request)
    {

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTask(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTask(Task $task, EntityManagerInterface $entityManager ): RedirectResponse
    {
        if ($this->getUser() === $task->getAuthor()){
        $this->removeTask($task, $entityManager);
        }
        if ($this->getUser()->getRoles() == 'ROLES_ADMIN' && $task->getId() == null){
            $this->removeTask($task, $entityManager);
        }

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    private function removeTask(Task $task,  EntityManagerInterface $entityManager)
    {
        $entityManager->remove($task);
        $entityManager->flush();
    }
}
