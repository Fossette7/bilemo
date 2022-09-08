<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    #[Route('/api/mobile', name: 'app_api', methods: ['GET'])]
    public function index(MobileRepository $mobileRepository, SerializerInterface $serializer ): JsonResponse
    {
      $mobiles = $mobileRepository->findAll();

      $jsonContent = $serializer->serialize($mobiles, 'json');

      $response = new JsonResponse($jsonContent, 200, [], true);

      return $response;
    }

  #[Route('/api/mobile/{id}', name: 'app_api_mobile_show', methods: ['GET'])]
  public function mobileShow(Mobile $mobile, SerializerInterface $serializer ): JsonResponse
  {
    $jsonContent = $serializer->serialize($mobile, 'json');

    $response = new JsonResponse($jsonContent, 200, [], true);
    return $response;
  }

    #[Route('/api/mobile', name: 'app_api_create', methods: ['POST'])]
    public function create(Request $request, MobileRepository $mobileRepository, EntityManagerInterface $em, SerializerInterface $serializer)
    {
      $jsonData = json_decode($request->getContent(),1);
      if(!empty($jsonData['brandName'])){
        $brandName = $jsonData['brandName'];

        // Check if brand already exist
        $mobileObject = $mobileRepository->findBy(['brandname' => $brandName]);

        if(!empty($mobileObject)){
          return new JsonResponse(['error' => sprintf('This %s already exist', $brandName)]);
        }

        $newMobile = new Mobile();
        $newMobile->setBrandname($brandName);
        $em->persist($newMobile);
        $em->flush();
        $jsonMobile = $serializer->serialize([
          'success' => sprintf('%s Add', $brandName),
          'object' => $newMobile
        ], 'json');

        $response = new JsonResponse($jsonMobile, 200, [], true);

        return $response;
      }

      return new JsonResponse(['error' => 'brandName data missing']);
    }
}
