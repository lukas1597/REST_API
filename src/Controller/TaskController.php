<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskController extends AbstractFOSRestController
{
    
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var EntityManagerInterface
     */

    private $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager){
        $this->taskRepository= $taskRepository;
        $this->entityManager= $entityManager;
    }

    /**
     * @Rest\Get("tasks/{task}", name="get_task")
     */
    public function getTask(Task $task){
        return $this->view($task, Response::HTTP_OK);

    }
    /**
     * @Rest\Get("tasks/{task}/notes", name="get_tasks_notes")
     */
    public function getTaskNotes(Task $task){
        

        if($task){
            return $this->view($task->getNotes(), Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }   


    /**
     * @Rest\Delete("/task/{task}", name="delete_task")
     * @return \FOS\RestBundle\View\View
     */
    public function deleteTask(Task $task){
        

        if($task){

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * @Rest\Patch("/task/{task}/status", name="patch_task")
     * @return \FOS\RestBundle\View\View
     */
    public function statusTask(Task $task){
        

        if($task){

            $task->setIsComplete(!$task->getIsComplete());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task->getIsComplete(), Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR]);
    }


    /**
     * @Rest\Post("/task/{task}/note", name="post_task_note")
     * @Rest\RequestParam(name="note", description="Note for the task", nullable=false)
     * @return \FOS\RestBundle\View\View
     */
    public function postTaskNote(ParamFetcher $paramFetcher, Task $task){

        $noteString = $paramFetcher->get('note');
        if($noteString){
            if($task){
                $note= new Note();

                $note->setNote($noteString);
                $note->setTask($task);

                $task->addNote($note);

                $this->entityManager->persist($note);
                $this->entityManager->flush();

                return $this->view($note, Response::HTTP_OK);
            }
    }
        return $this->view(['message' => 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR]);
    }
}
