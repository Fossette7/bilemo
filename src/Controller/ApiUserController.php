<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;


class ApiUserController extends AbstractController
{
  /**
   * @Route("/api/users", name="api_userslist", methods={"GET"})
   * @Security(name="Bearer")
   */
  public function getUserList(
    UserRepository $userRepository,
    Request $request,
    SerializerInterface $serializer,
    TokenStorageInterface $token
  ): JsonResponse {
    // Récupération de l'utilisateur connecté à l'api via le token associé au service TokenStorageInterface (JWT)
    $customer = $token->getToken()->getUser();
    if (!empty($page = $request->get('page', 1)) && !empty($limit = $request->get('limit', 3))) {
      // On envoie l'objet Customer dans la méthode findAllPagination
      $users = $userRepository->findAllPagination($customer, $page, $limit);
    }

    $jsonContent = $serializer->serialize($users, 'json', ['groups' => 'show_users']);

    return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);

  }

  /**
   * @Route("/api/user/{id}", name="api_show_user", methods={"GET"})
   * @Security(name="Bearer")
   */
  public function getUserDetail(TokenStorageInterface $token, User $user, SerializerInterface $serializer): JsonResponse
  {
    // Récupération du token pour avoir le customer
    /** @var Customer $logedCustomer */
    $logedCustomer = $token->getToken()->getUser();

    // Vérifier si le user $user dépend bien du customer récupéré via l'id (dump($user->getCustomer());
    // Si le customer est différent on retourne la réponse avec le message d erreur
    if($user->getCustomer()->getId() !== $logedCustomer->getId())
    {
      return new JsonResponse(
        json_encode(['message' => 'Error. Customer not associate to your account']),
        Response::HTTP_UNAUTHORIZED,
        [],
        true
      );
    }

    $jsonContent = $serializer->serialize($user, 'json', ['groups' => 'show_users']);

    return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);

  }

  /**
   * @Route("/api/user", name="api_create_user", methods={"POST"})
   * @Security(name="Bearer")
   * @IsGranted("ROLE_ADMIN")
   */
  public function createUser(
    Request $request,
    SerializerInterface $serializer,
    EntityManagerInterface $em
  ): JsonResponse {
    /** @var User $user */
    $user = $serializer->deserialize($request->getContent(), User::class, 'json');
    $datas = json_decode($request->getContent(), 1);
    $customer = $em->getRepository(Customer::class)->find($datas['customer']);

    $user->setCustomer($customer);

    $em->persist($user);
    $em->flush();

    $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'show_users']);

    return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);

  }

  /**
   * @Route("/api/user", name="api_create_user", methods={"DELETE"})
   * @Security(name="Bearer")
   * @IsGranted("ROLE_ADMIN")
   */
  public function deleteUser(TokenStorageInterface $token, User $user, EntityManagerInterface $em): JsonResponse
  {

    // Vérifier si le user $user dépend bien du customer récupéré via l'id (dump($user->getCustomer());
    // Si le customer est différent on retourne la réponse avec message d erreur
    if($user->getCustomer()->getId() !== $logedCustomer->getId())
    {
      return new JsonResponse(
        json_encode(['message' => 'Error. Customer not associate to your account']),
        Response::HTTP_UNAUTHORIZED,
        [],
        true
      );
    }

    $em->remove($user);
    $em->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
