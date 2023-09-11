<?php

namespace App\Controller;

use App\Entity\TypeTransport;
use App\Repository\TypeTransportRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TypeTransportController extends AbstractController
{
    #[Route('api/typeTransports', name: 'app_type_transport', methods: ['GET'])]
    public function getTypeTransport(TypeTransportRepository $typeTransportRepository, SerializerInterface $serializer): JsonResponse
    {
        $typeTransportList = $typeTransportRepository->findAll();
        $jsonTypeTransport = $serializer->serialize($typeTransportList, 'json', ['groups' => 'getTypeTransports']);
        return new JsonResponse($jsonTypeTransport, Response::HTTP_OK, [], true);
    }

    #[Route('api/typeTransports/{id}', name: 'detailTypeTransport', methods: ['GET'])]
    public function getDetailTransport(TypeTransport $typeTransport, SerializerInterface $serializer): JsonResponse {
        $jsonTransport = $serializer->serialize($typeTransport, 'json', ['groups' => 'getTypeTransports']);
        return new JsonResponse($jsonTransport, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
