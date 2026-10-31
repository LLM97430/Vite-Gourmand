<?php

namespace App\Controller;

use App\Entity\Regime;
use App\Repository\RegimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/regime', name: 'app_regime_')]
final class RegimeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RegimeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $regime = $this->serializer->deserialize($request->getContent(), Regime::class ,'json');

        $this->manager->persist($regime);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($regime, 'json');
        $location = $this->urlGenerator->generate(
            'app_regime_show',
            ['id' => $regime->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $regime = $this->repository->find($id);

        if($regime) 
            {
                $responseData = $this->serializer->serialize($regime, 'json');
                return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response
    {
        $regime = $this->repository->find($id);
        
        if($regime) 
            {
            $regime = $this->serializer->deserialize(
                $request->getContent(),
                Regime::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $regime]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $regime = $this->repository->find($id);

        if(!$regime) 
            {
            throw $this->createNotFoundException("Il n'y a pas de regime identifié {$id}");
        }

        $this->manager->remove($regime);
        $this->manager->flush();

        return $this->json(['message' => "Ce regime a été supprimé"]);
    }
}