<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Repository\MobileRepository;
use App\Entity\Mobile;

class ApiMobileController extends AbstractController
{
  /**
   * Obtention d'une liste de mobile
   *
   * @Route("/api/mobiles", name="app_api", methods={"GET"})
   *
   * @OA\Response(
   *     response=Response::HTTP_OK,
   *     description="Retourne tous les mobiles.",
   *     @OA\JsonContent(
   *        type="array",
   *        @OA\Items(ref=@Model(type=Mobile::class))
   *     )
   * )
   * @OA\Parameter(
   *     name="page",
   *     example="1",
   *     in="query",
   *     description="Page sélectionnée",
   *     @OA\Schema(type="string")
   * )
   * @OA\Parameter(
   *     name="limit",
   *     example="2",
   *     in="query",
   *     description="Nombre max d'élément à récupérer souhaité",
   *     @OA\Schema(type="string")
   * )
   *
   * @Security(name="Bearer")
   */
  public function getMobileListPagination(MobileRepository $mobileRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool ): JsonResponse
  {
    if(!empty($page = $request->get('page', 1)) && !empty($limit = $request->get('limit', 3))){

      $idCache = "getAllMobiles-" . $page . "-" . $limit;
      $mobileList = $cachePool->get($idCache, function (ItemInterface $item) use ($mobileRepository, $page, $limit){
        $item->tag("mobilesCache");
        return $mobileRepository->findAllPagination($page, $limit);
      });

      $jsonContent = $serializer->serialize($mobileList, 'json');

      $response = new JsonResponse($jsonContent, 200, [], true);
    }

    return $response;

  }

  /**
   * Obtention des informations liée au mobile.
   *
   * @Route("/api/mobile/{id}", name="api_show_mobile", methods={"GET"})
   * @OA\Response(
   *     response=Response::HTTP_OK,
   *     description="Retourne le mobile correspondant l'id",
   *     @Model(type=Mobile::class)
   *
   * )
   * @OA\Parameter(
   *     name="id",
   *     example="3",
   *     in="path",
   *     description="Id du mobile",
   *     @OA\Schema(type="string")
   * )
   *
   * @Security(name="Bearer")
   */
  public function showMobile(Mobile $mobile, SerializerInterface $serializer): JsonResponse
  {

    $jsonContent = $serializer->serialize($mobile, 'json');

    $response = new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    return $response;
  }

  /**
   * Création d'un mobile.
   *
   * @Route("/api/mobile", name="api_create_mobile", methods={"POST"})
   * @OA\Response(
   *     response=Response::HTTP_CREATED,
   *     description="Retourne le mobile créé",
   *     @Model(type=Mobile::class)
   *
   * )
   *  @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="application/json",
   *       @OA\Schema(
   *     @OA\Property(
   *           property="brandname",
   *           description="Marque du téléphone.",
   *           example="noquia",
   *           type="string",
   *         ),
   *      @OA\Property(
   *           property="model",
   *           description="Modèle du téléphone.",
   *           type="string",
   *           example="33310"
   *         ),
   *     @OA\Property(
   *           property="description",
   *           description="Information technique du téléphone",
   *           type="string",
   *           example="Le nouveau Noquia 33310."
   *         ),
   *     @OA\Property(
   *           property="price",
   *           description="Prix public du téléphone.",
   *           type="integer",
   *           example="79.9"
   *         ),
   *     @OA\Property(
   *           property="createdAt",
   *           description="Date d'ajout du télèphone.",
   *           type="string",
   *           format= "date-time",
   *           example="2017-10-11T22:14:17.554Z"
   *         ),
   *     @OA\Property(
   *           property="picture",
   *           description="Image de présentation du télèphone.",
   *           type="string",
   *           example="no-3094-01.jpg"
   *
   *         ),
   *     @OA\Property(
   *           property="stock",
   *           description="Stock disponible du télèphone.",
   *           type="integer",
   *           example="72"
   *         ),
   *       ),
   *     ),
   *   )
   *
   * @Security(name="Bearer")
   */
  public function createMobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $em) :JsonResponse
  {
    $mobile = $serializer->deserialize($request->getContent(), Mobile::class, 'json');

    $em->persist($mobile);
    $em->flush();

    $jsonMobile = $serializer->serialize($mobile, 'json',[]);

    return new JsonResponse($jsonMobile, Response::HTTP_CREATED,[], true);

  }

  /**
   * Mise à jour total d'un mobile.
   *
   * @Route("/api/mobile/{id}", name="api_update_mobile", methods={"PUT"})
   * @OA\Response(
   *     response=JsonResponse::HTTP_NO_CONTENT,
   *     description="Mise à jour global du télèphone."
   * )
   * @OA\Parameter(
   *     name="id",
   *     example="2",
   *     in="path",
   *     description="Id du mobile",
   *     @OA\Schema(type="string")
   * )
   *  @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="application/json",
   *       @OA\Schema(
   *     @OA\Property(
   *           property="brandname",
   *           description="Marque du télèphone.",
   *           example="samsung",
   *           type="string",
   *         ),
   *      @OA\Property(
   *           property="model",
   *           description="Modèle du téléphone.",
   *           type="string",
   *           example="S22"
   *         ),
   *     @OA\Property(
   *           property="description",
   *           description="Information technique du telèphone",
   *           type="string",
   *           example="Une super modèle."
   *         ),
   *     @OA\Property(
   *           property="price",
   *           description="Prix public du télèphone.",
   *           type="integer",
   *           example="799.99"
   *         ),
   *     @OA\Property(
   *           property="createdAt",
   *           description="Date d'ajout du téléphone.",
   *           type="string",
   *           format= "date-time",
   *           example="2022-10-11T22:14:17.554Z"
   *         ),
   *     @OA\Property(
   *           property="picture",
   *           description="Image de présentation du téléphone.",
   *           type="string",
   *           example="sa-94029-01.jpg"
   *
   *         ),
   *     @OA\Property(
   *           property="stock",
   *           description="Stock disponible du téléphone.",
   *           type="integer",
   *           example="400"
   *         ),
   *       ),
   *     ),
   *   )
   *
   * @Security(name="Bearer")
   */


    public function updateMobile(Request $request, SerializerInterface $serializer, Mobile $currentMobile,
    EntityManagerInterface $em): JsonResponse
  {
    $updatedMobile = $serializer->deserialize($request->getContent(), Mobile::class, 'json',
      [AbstractNormalizer::OBJECT_TO_POPULATE => $currentMobile]);

    $content = $request->toArray();

    $em->persist($updatedMobile);
    $em->flush();

    return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
  }


  #[Route('/api/mobile/{id}', name: 'api_delete_mobile', methods: ['DELETE'])]
  #[Security(name: 'bearer')]
  public function deleteMobile(Mobile $mobile, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
  {
    $cachePool->invalidateTags(["mobilesCache"]);
    $em->remove($mobile);
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

}
