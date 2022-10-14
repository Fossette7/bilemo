<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ApiUserController extends AbstractController
{
    #[Route('/api/users', name: 'api_userslist', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
       $users = $userRepository->findAll();

       $jsonContent = $serializer->serialize($users, 'json', ['groups' => 'show_users']);

       return new JsonResponse($jsonContent,Response::HTTP_OK, [], true);

    }

    #[Route('/api/user/{id}', name: 'api_show_user', methods: ['GET'])]
    public function getUserDetail(User $user, SerializerInterface $serializer): JsonResponse
    {

      $jsonContent = $serializer->serialize($user, 'json',['groups' => 'show_users']);

      return new JsonResponse($jsonContent,Response::HTTP_OK, [], true);

    }
}
