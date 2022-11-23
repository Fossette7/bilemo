<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiMobileController extends AbstractController
{
  /**
   * Get a mobiles list
   *
   * @Route("/api/mobiles", name="app_mobiles_list", methods={"GET"})
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
   *     @OA\Schema(type="int")
   * )
   * @OA\Parameter(
   *     name="limit",
   *     example="2",
   *     in="query",
   *     description="Nombre max d'élément à récupérer souhaité",
   *     @OA\Schema(type="int")
   * )
   * @OA\Tag(name="Mobile_product")
   * @Security(name="Bearer")
   */
  public function getMobileListPagination(
    MobileRepository $mobileRepository,
    SerializerInterface $serializer,
    Request $request,
    TagAwareCacheInterface $cachePool
  ): JsonResponse {
    if (!empty($page = $request->get('page', 1)) && !empty($limit = $request->get('limit', 3))) {

      $idCache = "getAllMobiles-".$page."-".$limit;
      $mobileList = $cachePool->get($idCache, function (ItemInterface $item) use ($mobileRepository, $page, $limit) {
        $item->tag("mobilesCache");

        return $mobileRepository->findAllPagination($page, $limit);
      });

      $jsonContent = $serializer->serialize($mobileList, 'json');

      $response = new JsonResponse($jsonContent, 200, [], true);
    }

    return $response;

  }

  /**
   * Get a mobile's detail
   *
   * @Route("/api/mobile/{id}", name="api_show_mobile", methods={"GET"})
   * @OA\Response(
   *     response=Response::HTTP_OK,
   *     description="Retourne le mobile correspondant l'id",
   *     @Model(type=Mobile::class)
   * )
   * @OA\Response (
   *   response=404,
   *   description="No product found for this Id",
   *     @OA\JsonContent(
   *        @OA\Property(
   *         property="error",
   *         type="string",
   *         example="Ce produit n'existe pas"
   *        )
   *     )
   * )
   *
   * @Security(name="Bearer")
   * @OA\Tag(name="Mobile_product")
   *
   */
  public function showMobile(Mobile $mobile, SerializerInterface $serializer): JsonResponse
  {
    if ($mobile) {
      $jsonContent = $serializer->serialize($mobile, 'json');
      return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    return JsonResponse(json_encode(["error" => "Ce produit n'existe pas"]), Response::HTTP_NOT_FOUND);
  }

}
