<?php

namespace App\Controller;

use App\Entity\TransportEnCommun;
use App\Repository\TransportEnCommunRepository;
use App\Repository\TypeTransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransportController extends AbstractController
{
    #[Route('/api/transports', name: 'transport', methods: ['GET'])]
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

    #[Route('api/transports/{id}', name: 'deleteTransport', methods: ['DELETE'])]
    public function deleteTransport(TransportEnCommun $transport, EntityManagerInterface $entityManager): JsonResponse {
        $entityManager->remove($transport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/transports', name: 'createTransport', methods: ['POST'])]
    public function createTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, TypeTransportRepository $typeTransportRepository, ValidatorInterface $validator): JsonResponse {
        $transport = $serializer->deserialize($request->getContent(), TransportEnCommun::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($transport);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        // je récupère le contenu de ma requête dans un tableau
        $content = $request->toArray();

        // Je récupère l'idTypeTransport si celui est renseigne dans ma requête
        $idTypeTransport = $content['idTypeTransport'] ?? -1;

        // On cherche le type de transport qui correspond et on l'assigne au transport.
        // Si "find" ne trouve pas le type, alors null sera retourné.
        $transport->setTypeTransport($typeTransportRepository->find($idTypeTransport));

        $entityManager->persist($transport);
        $entityManager->flush();

        $jsonTransport = $serializer->serialize($transport, 'json', ['groups' => 'getTransports']);

        $location = $urlGenerator->generate('detailTransport', ['id' => $transport->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonTransport, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/transports/{id}', name: 'updateTransport', methods: ['PUT'])]
    public function updateTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TransportEnCommun $currentTransport, TypeTransportRepository $typeTransportRepository): JsonResponse {

        // AbstractNormalizer::OBJECT_TO_POPULATE permet de désérialiser $request->getContent() dans $currentTransport
        // ce qui correspond au transport passé dans l'URL
        $updatedTransport = $serializer->deserialize(
          $request->getContent(),
          TransportEnCommun::class,
          'json',
          [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTransport]
        );

        $content = $request->toArray();

        $idTypeTransport = $content['idTypeTransport'] ?? -1;

        $updatedTransport->setTypeTransport($typeTransportRepository->find($idTypeTransport));

        $entityManager->persist($updatedTransport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
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
