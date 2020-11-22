<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



/**
 * @Route("api", name="api_")
 */
class RegistrationController extends AbstractFOSRestController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager){
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function index(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $this->userRepository->findOneBy([
            'username' => $username
        ]);

        if(!is_null($user)){
            return $this->view([
                'message' => 'User already exists'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();

        $user->setUsername($username);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->view($user, Response::HTTP_CREATED)->setContext((new Context())->setGroups(['public']));
    }
}
