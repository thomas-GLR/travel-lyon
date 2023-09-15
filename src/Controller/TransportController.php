<?php

namespace App\Controller;

use App\Entity\TransportEnCommun;
use App\Repository\TransportEnCommunRepository;
use App\Repository\TypeTransportRepository;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class TransportController extends AbstractController
{

    /**
     * Cette méthode permet de récupérer l'ensemble des transports.
     *
     * @param TransportEnCommunRepository $transportRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des transports',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: TransportEnCommun::class, groups: ['getTransports']))
        )
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'La page que l\'on veut récupérer',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Le nombre d\'éléments que l\'on veut récupérer',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag(name: 'Transports')]
    #[Route('/api/transports', name: 'transport', methods: ['GET'])]
    public function getTransportList(TransportEnCommunRepository $transportRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {

        //$transportList = $transportRepository->findAll();

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        if ($page <= 0) {

            $error = [
                'status'=> Response::HTTP_BAD_REQUEST,
                'message' => 'La page renseignée ne peut être inférieure à 1'
            ];

            return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $idCache = "getAllTransports-" . $page . "-" . $limit;
        $jsonTransportList = $cachePool->get($idCache, function (ItemInterface $item) use ($transportRepository, $page, $limit, $serializer) {
            $item->tag("transportsCache");
             $transportList = $transportRepository->findAllWithPagination($page, $limit);
             $context = SerializationContext::create()->setGroups(['groups' => 'getTransports']);
             return $serializer->serialize($transportList, 'json', $context);
        });
        //$transportList = $transportRepository->findAllWithPagination($page, $limit);

        // Je sérialise ma liste d'objet pour l'envoyer ne JSON
        //$jsonTransportList = $serializer->serialize($transportList, 'json', ['groups' => 'getTransports']);

        return new jsonResponse($jsonTransportList,Response::HTTP_OK, [],true);
    }

    #[Route('api/transports/{id}', name: 'detailTransport', methods: ['GET'])]
    public function getDetailTransport(TransportEnCommun $transport, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse {
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['groups' => 'getTransports']);
        //$context->setVersion("1.0");
        $context->setVersion($version);
        $jsonTransport = $serializer->serialize($transport, 'json', $context);
        return new JsonResponse($jsonTransport, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('api/transports/{id}', name: 'deleteTransport', methods: ['DELETE'])]
    public function deleteTransport(TransportEnCommun $transport, EntityManagerInterface $entityManager, TagAwareCacheInterface $cachePool): JsonResponse {
        $cachePool->invalidateTags(["transportsCache"]);
        $entityManager->remove($transport);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/transports', name: 'createTransport', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un livre')]
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

        $context = SerializationContext::create()->setGroups(['getTransports']);
        $jsonTransport = $serializer->serialize($transport, 'json', $context);

        $location = $urlGenerator->generate('detailTransport', ['id' => $transport->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonTransport, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/transports/{id}', name: 'updateTransport', methods: ['PUT'])]
    public function updateTransport(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TransportEnCommun $currentTransport, TypeTransportRepository $typeTransportRepository): JsonResponse {

        $newTransport = $serializer->deserialize($request->getContent(), TransportEnCommun::class, 'json');
        $currentTransport->setNomTransport($newTransport->getNomTransport());
        $currentTransport->setTerminusDepart($newTransport->getTerminusDepart());

        // --> FOnctionne plus avec le nouveau serializer
        /*
        // AbstractNormalizer::OBJECT_TO_POPULATE permet de désérialiser $request->getContent() dans $currentTransport
        // ce qui correspond au transport passé dans l'URL
        $updatedTransport = $serializer->deserialize(
          $request->getContent(),
          TransportEnCommun::class,
          'json',
          [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTransport]
        );
        */

        $content = $request->toArray();

        $idTypeTransport = $content['idTypeTransport'] ?? -1;

        $currentTransport->setTypeTransport($typeTransportRepository->find($idTypeTransport));

        $entityManager->persist($currentTransport);
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
