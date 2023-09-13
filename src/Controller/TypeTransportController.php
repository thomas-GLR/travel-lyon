<?php

namespace App\Controller;

use App\Entity\TypeTransport;
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

class TypeTransportController extends AbstractController
{
    #[Route('api/typeTransports', name: 'typeTransport', methods: ['GET'])]
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

    #[Route('api/typeTransports/{id}', name: 'deleteTypeTransport', methods: ['DELETE'])]
    public function deleteTypeTransport(TypeTransport $typeTransport, EntityManagerInterface $entityManager): JsonResponse {
        $entityManager->remove($typeTransport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/typeTransports', name: 'createTypeTransport', methods: ['POST'])]
    public function createTypeTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse {
        $typeTransport = $serializer->deserialize($request->getContent(), TypeTransport::class, 'json');

        $errors = $validator->validate($typeTransport);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($typeTransport);
        $entityManager->flush();

        $jsonTypeTransport = $serializer->serialize($typeTransport, 'json', ['groups' => 'getTypeTransports']);

        $location = $urlGenerator->generate('detailTransport', ['id' => $typeTransport->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonTypeTransport, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/typeTransports/{id}', name: 'updateTypeTransport', methods: ['PUT'])]
    public function updateTypeTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TypeTransport $currentTypeTransport): JsonResponse {
        $updatedTypeTransport = $serializer->deserialize(
            $request->getContent(),
            TypeTransport::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTypeTransport]
        );

        $entityManager->persist($updatedTypeTransport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
