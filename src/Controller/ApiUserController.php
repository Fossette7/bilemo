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
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserController extends AbstractController
{
  /**
   * List of users of a customer.
   * @Route("/api/users", name="api_userslist", methods={"GET"})
   * @OA\Response(
   *     response=200,
   *     description="Returns the users list of a Registered Customer",
   *     @OA\JsonContent(
   *        type="array",
   *        @OA\Items(ref=@Model(type=User::class, groups={"show_users"}))
   *     )
   * )
   * )
   * @OA\Response(
   *     response=401,
   *     description="Unauthorized, Expired JWT Token",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="code",
   *         type="integer",
   *         example="401"
   *        ),
   *        @OA\Property(
   *         property="message",
   *         type="string",
   *         example="Expired JWT Token"
   *        ),
   *     )
   * )
   * @OA\Parameter(
   *     name="page",
   *     example="1",
   *     in="query",
   *     description="Page sélectionnée",
   *     @OA\Schema(type="int")
   * )
   * @OA\Parameter(
   *     name="limit",
   *     example="2",
   *     in="query",
   *     description="Nombre max d'élément à récupérer souhaité",
   *     @OA\Schema(type="int")
   * )
   *
   * @OA\Tag(name="Users")
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
   * Show the detail of a user
   * @Route("/api/user/{id}", name="api_show_user", methods={"GET"})
   * @OA\Response(
   *     response=Response::HTTP_OK,
   *     description="Return user according to id",
   *     @Model(type=User::class, groups={"show_users"})
   * )
   *
   * @OA\Response(
   *     response=401,
   *     description="Unauthorized, Expired JWT Token",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="code",
   *         type="integer",
   *         example="401"
   *        ),
   *        @OA\Property(
   *         property="message",
   *         type="string",
   *         example="Expired JWT Token"
   *        ),
   *     )
   * )
   * @OA\Response (
   *   response=404,
   *   description="No user found for this Id",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="error",
   *         type="string",
   *         example="Cet utilisateur n'existe pas"
   *        )
   *     )
   * )
   * )
   * @OA\Tag(name="Users")
   * @Security(name="Bearer")
   */
  public function getUserDetail(TokenStorageInterface $token, User $user, SerializerInterface $serializer): JsonResponse
  {
    if (empty($user)) {
      return new JsonResponse(json_encode(["error" => "Cet utilisateur n'existe pas"]), Response::HTTP_NOT_FOUND, [], true);
    }

    // Récupération du token pour avoir le customer
    /** @var Customer $logedCustomer */
    $logedCustomer = $token->getToken()->getUser();

    // Vérifier si le user $user dépend bien du customer récupéré via l'id (dump($user->getCustomer());
    // Si le customer est différent on retourne la réponse avec le message d erreur
    if ($user->getCustomer()->getId() !== $logedCustomer->getId()) {
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
   * Create a new user for a registered Customer
   * @Route("/api/user", name="api_create_user", methods={"POST"})
   *
   * @OA\RequestBody (
   *      required=true,
   *      @OA\MediaType(
   *        mediaType="application/json",
   *        @OA\Schema (
   *          @OA\Property(
   *            property="firstname",
   *            description="new user's firstname",
   *            type="string",
   *            example="Alain"
   *          ),
   *          @OA\Property(
   *            property="lastname",
   *            description="new user's lastname",
   *            type="string",
   *            example="Deloin"
   *          ),
   *          @OA\Property(
   *            property="email",
   *            description="new user's email",
   *            type="email",
   *            example="alaindeloin@gmail.com"
   *          )
   *        )
   *      )
   *   )
   *
   *
   * @OA\Response(
   *     response=201,
   *     description="Create a new user",
   *     @OA\JsonContent(
   *        @OA\Property(
   *          property="id",
   *          type="integer",
   *          example="43"
   *          ),
   *        @OA\Property(
   *          property="firstname",
   *          type="string",
   *          example="Alain"
   *          ),
   *          @OA\Property(
   *          property="lastname",
   *          type="string",
   *          example="Deloin"
   *          ),
   *          @OA\Property(
   *          property="email",
   *          type="string",
   *          example="alaindeloin@gmail.com"
   *          )
   *     )
   * )
   * @OA\Response(
   *     response=401,
   *     description="Unauthorized, Expired JWT Token",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="code",
   *         type="integer",
   *         example="401"
   *        ),
   *        @OA\Property(
   *         property="message",
   *         type="string",
   *         example="Expired JWT Token"
   *        ),
   *     )
   * )
   * @OA\Response(
   *     response=409,
   *     description="Entity already exist",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="errors",
   *         type="array",
   *         @OA\Items(
   *          type="string",
   *          example="Email already used"
   *          )
   *        )
   *     )
   * )
   * @OA\Tag(name="Users")
   * @Security(name="Bearer")
   * @IsGranted("ROLE_ADMIN")
   */
  public function createUser(
    Request $request,
    SerializerInterface $serializer,
    EntityManagerInterface $em,
    TokenStorageInterface $token,
    UserRepository $userRepository,
    ValidatorInterface $validator
  ): JsonResponse {
    /** @var User $user */
    $user = $serializer->deserialize($request->getContent(), User::class, 'json');
    $user->setCreatedAt(new \DateTime());
    $datas = json_decode($request->getContent(), 1);
    $logedCustomer = $token->getToken()->getUser();
    $user->setCustomer($logedCustomer);

    $errorText = ['errors' => []];
    $error = $validator->validate($user);
    if($error->count() > 0)
    {
      foreach ($error as $e){
        $errorText['errors'][] = $e->getMessage();
      }

      return new JsonResponse(json_encode($errorText), Response::HTTP_CONFLICT, [], true);
    }

    $em->persist($user);
    $em->flush();

    $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'show_users']);

    return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
  }

  /**
   * Delete a user for a registered Customer
   * @Route("/api/user/{id}", name="api_delete_user", methods={"DELETE"})
   *
   * @OA\Response(
   *     response=Response::HTTP_NO_CONTENT,
   *     description="No content"
   * )
   ** @OA\Response(
   *     response=Response::HTTP_UNAUTHORIZED,
   *     description="Unauthorized"
   * )
   * @OA\Tag(name="Users")
   * @Security(name="Bearer")
   * @IsGranted("ROLE_ADMIN")
   */
  public function deleteUser(TokenStorageInterface $token, User $user, EntityManagerInterface $em): JsonResponse
  {
    $logedCustomer = $token->getToken()->getUser();
    // Vérifier si le user $user dépend bien du customer récupéré via l'id (dump($user->getCustomer());
    // Si le customer est différent on retourne la réponse avec message d erreur
    if ($user->getCustomer()->getId() !== $logedCustomer->getId()) {
      return new JsonResponse(
        json_encode(['message' => 'Error. Unauthorized access']),
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
