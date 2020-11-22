<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use App\Entity\Note;
use App\Repository\NoteRepository;

class NoteController extends AbstractFOSRestController
{
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @var EntityManagerInterface
     */

    private $entityManager;

    public function __construct(NoteRepository $noteRepository, EntityManagerInterface $entityManager){
        $this->noteRepository= $noteRepository;
        $this->entityManager= $entityManager;
    }


    /**
     * @Rest\Get("notes/{note}", name="get_note")
     */
    public function getNote(Note $note){
        return $this->view($note, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("notes/{note}", name="delete_note")
     */
    public function deleteNote(Note $note){
        if($note){

            $this->entityManager->remove($note);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
