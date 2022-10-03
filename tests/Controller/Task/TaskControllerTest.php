<?php

namespace App\Tests\Controller\Task;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends WebTestCase
{
    CONST ADD_TASK_TITLE_1 = 'aller à la plage';
    CONST ADD_TASK_TITLE_2 = 'aller à la pêche';
    CONST ADD_TASK_CONTENT_1 = 'planter le parasol';
    CONST ADD_TASK_CONTENT_2 = 'avec le parasol';

    private ?KernelBrowser $client = null;
    private TaskRepository $taskRepo;
    private User $user;
    private User $admin;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->taskRepo = static::getContainer()->get(TaskRepository::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $userRepository->findOneByUsername('helloUser');
        $this->admin = $userRepository->findOneByUsername('Admin');

        $this->urlGenerator = $this->client->getContainer()->get('router.default');

        $this->client->followRedirects();
    }

    public function testListPageIsUp()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));

        $this->assertResponseIsSuccessful();
    }

    public function testCreateNewTaskUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = self::ADD_TASK_TITLE_1;
        $form['task[content]'] = self::ADD_TASK_CONTENT_1;
        $this->client->submit($form);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "La tâche a bien été ajoutée."
        );

        $this->client->loginUser($this->user);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = self::ADD_TASK_TITLE_2;
        $form['task[content]'] = self::ADD_TASK_CONTENT_2;
        $this->client->submit($form);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "La tâche a bien été ajoutée."
        );
    }

    public function testEditExistingTask()
    {
        $taskToEdit = $this->taskRepo->findOneByTitle(self::ADD_TASK_TITLE_1);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_edit', ['id' => $taskToEdit->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = self::ADD_TASK_CONTENT_2;
        $this->client->submit($form);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "La tâche a bien été modifiée."
        );
    }

    public function testToggleTaskAction()
    {
        $taskToEdit = $this->taskRepo->findOneByTitle(self::ADD_TASK_TITLE_1);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_toggle', ['id' => $taskToEdit->getId()]));
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            self::ADD_TASK_TITLE_1
        );
    }

    public function testDeleteTaskAction()
    {

        $taskToDeleteUser = $this->taskRepo->findOneByTitle(self::ADD_TASK_TITLE_2);

        $this->client->loginUser($this->user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $taskToDeleteUser->getId()]));
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "La tâche a bien été supprimée."
        );

        $taskToEdit = $this->taskRepo->findOneByTitle(self::ADD_TASK_TITLE_1);
        $this->client->loginUser($this->admin);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $taskToEdit->getId()]));
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "La tâche a bien été supprimée."
        );
    }

}
