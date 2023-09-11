<?php

namespace App\Controller;

use App\Entity\TransportEnCommun;
use App\Repository\TransportEnCommunRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TransportController extends AbstractController
{
    #[Route('/api/transports', name: 'app_transport', methods: ['GET'])]
    public function getTransportList(TransportEnCommunRepository $transportRepository, SerializerInterface $serializer): JsonResponse
    {

        $transportList = $transportRepository->findAll();
        // Je sérialise ma liste d'objet pour l'envoyer ne JSON
        $jsonTransportList = $serializer->serialize($transportList, 'json', ['groups' => 'getTransports']);

        return new jsonResponse($jsonTransportList,Response::HTTP_OK, [],true);
    }

    #[Route('api/transports/{id}', name: 'detailTransport', methods: ['GET'])]
    public function getDetailTransport(TransportEnCommun $transport, SerializerInterface $serializer): JsonResponse {
        $jsonTransport = $serializer->serialize($transport, 'json', ['groups' => 'getTransports']);
        return new JsonResponse($jsonTransport, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    // point d'entrée sans le paramConverter de symfony
    /*
    #[Route('api/transports/{idTransport}', name: 'detailTransport', methods: ['GET'])]
    public function getDetailTransport(int $idTransport, SerializerInterface $serializer, TransportEnCommunRepository $transpotRepository): JsonResponse {

        $transport = $transpotRepository->find($idTransport);

        if ($transport) {
            $jsonTransport = $serializer->serialize($transport, 'json');
            return new JsonResponse($jsonTransport, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    */
}
