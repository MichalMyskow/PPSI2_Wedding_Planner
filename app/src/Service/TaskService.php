<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use App\Entity\Task;


class TaskService
{
    private $em;
    private $model;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->model = $em->getRepository(Task::class);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getOne($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model->findAll();
    }

    public function saveTask(Task $task)
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function deleteTask($task)
    {   
        $this->em->remove($task);
        $this->em->flush();
    }

    public function changeState(Task $task, bool $completed)
    {   
        $task->setCompleted($completed);
        $this->saveTask($task);
    }
}