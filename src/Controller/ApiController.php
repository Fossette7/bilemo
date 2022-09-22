<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
}
