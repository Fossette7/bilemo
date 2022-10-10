<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApiController extends AbstractController
{

    #[Route('/api/mobiles', name: 'app_api', methods: ['GET'])]
    public function getMobileList(MobileRepository $mobileRepository, SerializerInterface $serializer ): JsonResponse
    {
      $mobiles = $mobileRepository->findAll();

      $jsonContent = $serializer->serialize($mobiles, 'json');

      $response = new JsonResponse($jsonContent, 200, [], true);

      return $response;

    }

  #[Route('/api/mobile/{id}', name: 'api_show_mobile', methods: ['GET'])]
  public function showMobile(Mobile $mobile, SerializerInterface $serializer ): JsonResponse
  {
    $jsonContent = $serializer->serialize($mobile, 'json');

    $response = new JsonResponse($jsonContent, 200, [], true);
    return $response;
  }

    #[Route('/api/mobile', name: 'api_create_mobile', methods: ['POST'])]
    public function createMobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $em) :JsonResponse
    {
      $mobile = $serializer->deserialize($request->getContent(), Mobile::class, 'json');
      $em->persist($mobile);
      $em->flush();

      $jsonMobile = $serializer->serialize($mobile, 'json',[]);

      return new JsonResponse($jsonMobile, Response::HTTP_CREATED,[], true);

    }

  #[Route('/api/mobile/{id}', name: 'api_update_mobile', methods: ['PUT'])]
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
    public function deleteMobile(Mobile $mobile, EntityManagerInterface $em): JsonResponse
    {
      /*  POP UNE ERREUR VOIR AVEC LE PARAMETER CONVERTER
     if (empty($brandName) {
       throw $this->createNotFoundException(
         'There are no mobiles with the following id: '.$id
       );
     }*/

      $em->remove($mobile);
      $em->flush();
      return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}

