<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskListRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use App\Entity\TaskList;
use App\Repository\TaskRepository;

class ListController extends AbstractFOSRestController
{   

    /**
     * @var TaskListRepository
     */
    private $taskListRepository;

    /**
     * @var EntityManagerInterface
     */

    private $entityManager;


    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(TaskListRepository $taskListRepository, TaskRepository $taskRepository, EntityManagerInterface $entityManager){
        $this->taskListRepository = $taskListRepository;
        $this->taskRepository= $taskRepository;
        $this->entityManager= $entityManager;
    }

    /**
     * @Rest\Get("/lists", name="list")
     * @return \App\Entity\TaskList[]
     */
    public function getLists(){
        $data = $this->taskListRepository->findAll();
        
        return $this->view($data, Response::HTTP_OK);

    
    }

    /**
     * @Rest\Get("/lists/{list}", name="list_id")
     */
    public function getList(TaskList $list){
        
        if($list){
        return $this->view($list, Response::HTTP_OK);}

        return $this->view(['id' => 'Not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Post("/lists", name="post_list")
     * @Rest\RequestParam(name="title", description="Title of the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     */
    public function postLists(ParamFetcher $paramFetcher){
        $title= $paramFetcher->get('title');
        
        if($title){
            $list= new TaskList();

            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' =>'This value cannot be empty'], Response::HTPP_BAD_REQUEST);
        
        
    }


    

    /**
     * @Rest\Post("/lists/{list}/task", name="post_list_task")
     * @Rest\RequestParam(name="title", description="Title of the new task", nullable=false)
     */
    public function postListTask(ParamFetcher $paramFetcher, TaskList $list){
        

        if($list){
            $title = $paramFetcher->get('title');

            $task= new Task();
            $task->setTitle($title);
            $task->setList($list);
            $list->addTask($task);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task, Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }



    /**
     * @Rest\Delete("/lists/{list}", name="delete_lists")
     */
    public function deleteLists(TaskList $list){
        
        $this->entityManager->remove($list);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Patch("/lists/{list}/title", name="patch_lists")
     * @Rest\RequestParam(name="title", description="New title for the list", nullable=false)
     * @return \FOS\RestBundle\View\View
     */
    public function pacthListTitle(ParamFetcher $paramFetcher, TaskList $list){
        
        $errors=[];
        

        $title=$paramFetcher->get('title');
        if(trim($title) !== ''){
            if($list){
                $list->setTitle($title);

                $this->entityManager->persist($list);
                $this->entityManager->flush();

                return $this->view(null, Response::HTTP_NO_CONTENT);

            }
            $errors[]=[
                'list' => 'List not found'
            ];

        }

        $errors[]=[
            'title' => 'This value cannot be empty'
        ];

        return $this->view($errors, Response::HTTP_NO_CONTENT);
    }
    /**
     * @Rest\Get("/lists/{list}/tasks", name="task_list")
     */
    public function getListsTasks(TaskList $list){
        

        return $this->view($list->getTasks(), Response::HTTP_OK);
    }
}
