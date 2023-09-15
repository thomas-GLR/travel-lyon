<?php

namespace App\Controller;

use App\Entity\TypeTransport;
use App\Repository\TypeTransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TypeTransportController extends AbstractController
{
    #[Route('api/typeTransports', name: 'typeTransport', methods: ['GET'])]
    public function getTypeTransport(TypeTransportRepository $typeTransportRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        if ($page <= 0) {

            $error = [
                'status'=> Response::HTTP_BAD_REQUEST,
                'message' => 'La page renseignée ne peut être inférieure à 1'
            ];

            return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $idCache = "getAllTypeTransport-" . $page . "-" . $limit;
        $jsonTypeTransport = $cachePool->get($idCache, function (ItemInterface $item) use ($typeTransportRepository, $page, $limit, $serializer) {
            $item->tag("transportsCache");
            $typeTransportList = $typeTransportRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['getTypeTransports']);
            return $serializer->serialize($typeTransportList, 'json', $context);
        });

        //$typeTransportList = $typeTransportRepository->findAll();
        //$jsonTypeTransport = $serializer->serialize($typeTransportList, 'json', ['groups' => 'getTypeTransports']);
        return new JsonResponse($jsonTypeTransport, Response::HTTP_OK, [], true);
    }

    #[Route('api/typeTransports/{id}', name: 'detailTypeTransport', methods: ['GET'])]
    public function getDetailTransport(TypeTransport $typeTransport, SerializerInterface $serializer): JsonResponse {
        $context = SerializationContext::create()->setGroups(['getTypeTransports']);
        $jsonTransport = $serializer->serialize($typeTransport, 'json', $context);
        return new JsonResponse($jsonTransport, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('api/typeTransports/{id}', name: 'deleteTypeTransport', methods: ['DELETE'])]
    public function deleteTypeTransport(TypeTransport $typeTransport, EntityManagerInterface $entityManager, TagAwareCacheInterface $cachePool): JsonResponse {

        $entityManager->remove($typeTransport);
        $entityManager->flush();

        $cachePool->invalidateTags(["transportsCache"]);

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

        $context = SerializationContext::create()->setGroups(['getTypeTransports']);
        $jsonTypeTransport = $serializer->serialize($typeTransport, 'json', $context);

        $location = $urlGenerator->generate('detailTransport', ['id' => $typeTransport->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonTypeTransport, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/typeTransports/{id}', name: 'updateTypeTransport', methods: ['PUT'])]
    public function updateTypeTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TypeTransport $currentTypeTransport): JsonResponse {

        $newTypeTransport = $serializer->deserialize($request->getContent(), TypeTransport::class, 'json');
        $currentTypeTransport->setlibelle($newTypeTransport->getLibelle());

        /*
        $updatedTypeTransport = $serializer->deserialize(
            $request->getContent(),
            TypeTransport::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTypeTransport]
        );
        */

        $entityManager->persist($currentTypeTransport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
