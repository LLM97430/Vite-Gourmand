<?php

namespace App\Controller;

use App\Entity\Allergene;
use App\Repository\AllergeneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/allergene', name: 'app_allergene_')]
final class AllergeneController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AllergeneRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) 
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $allergene = $this->serializer->deserialize($request->getContent(), type: Allergene::class, format:'json');
!
        $this->manager->persist($allergene);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($allergene, 'json');
        $location = $this->urlGenerator->generate(
            'app_allergene_show',
            ['id' => $allergene->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $allergene = $this->repository->find($id);

        if($allergene) {
        $responseData = $this->serializer->serialize($allergene, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response{
        $allergene = $this->repository-> find($id);

        if($allergene) {
        
            $allergene = $this->serializer->deserialize(
                $request->getContent(),
                Allergene::class,
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $allergene]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response{
    $allergene = $this->repository->find($id);

    if(!$allergene){
        throw $this->createNotFoundException("Il n'y a pas d'allergene identifié {$id}");
    }
    $this->manager->remove($allergene);
    $this->manager->flush();

    return $this->json(['message'=>"L'allergene est supprimé."]);
    }
}
