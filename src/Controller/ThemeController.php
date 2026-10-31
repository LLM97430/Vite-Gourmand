<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/theme', name: 'app_theme_')]
final class ThemeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private ThemeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $theme = $this->serializer->deserialize($request->getContent(), Theme::class ,'json');

        $this->manager->persist($theme);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($theme, 'json', ['groups' => 'theme:read']);
        $location = $this->urlGenerator->generate(
            'app_theme_show',
            ['id' => $theme->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $theme = $this->repository->find($id);

        if($theme) 
            {
                $responseData = $this->serializer->serialize($theme, 'json', ['groups' => 'theme:read']);
                return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response
    {
        $theme = $this->repository->find($id);
        
        if($theme) 
            {
            $theme = $this->serializer->deserialize(
                $request->getContent(),
                Theme::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $theme]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $theme = $this->repository->find($id);

        if(!$theme) 
            {
            throw $this->createNotFoundException("Il n'y a pas de theme identifié {$id}");
        }

        $this->manager->remove($theme);
        $this->manager->flush();

        return $this->json(['message' => "Ce theme a été supprimé"]);
    }
}