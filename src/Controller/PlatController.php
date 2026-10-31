<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/plat', name: 'app_plat_')]
final class PlatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private PlatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $plat = $this->serializer->deserialize($request->getContent(), Plat::class ,'json');

        $this->manager->persist($plat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($plat, 'json');
        $location = $this->urlGenerator->generate(
            'app_plat_show',
            ['id' => $plat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $plat = $this->repository->find($id);

        if($plat) 
            {
                $responseData = $this->serializer->serialize($plat, 'json');
                return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response
    {
        $plat = $this->repository->find($id);
        
        if($plat) 
            {
            $plat = $this->serializer->deserialize(
                $request->getContent(),
                Plat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $plat]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $plat = $this->repository->find($id);

        if(!$plat) 
            {
            throw $this->createNotFoundException("Il n'y a pas de plat identifié {$id}");
        }

        $this->manager->remove($plat);
        $this->manager->flush();

        return $this->json(['message' => "Ce plat a été supprimé"]);
    }
}