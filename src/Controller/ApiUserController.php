<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/user', name: 'api_create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em) :JsonResponse
    {
      /** @var User $user */
      $user = $serializer->deserialize($request->getContent(), User::class, 'json');
      $datas = json_decode($request->getContent(),1);
      $customer = $em->getRepository(Customer::class)->find($datas['customer']);

      $user->setCustomer($customer);

      $em->persist($user);
      $em->flush();

      $jsonUser = $serializer->serialize($user, 'json',['groups' => 'show_users']);

      return new JsonResponse($jsonUser, Response::HTTP_CREATED,[], true);

    }

    #[Route('/api/user/{id}', name: 'api_delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {

      $em->remove($user);
      $em->flush();
      return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
