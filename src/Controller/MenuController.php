<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

    #[Route('/menu', name: 'app_menu_')]
    final class MenuController extends AbstractController
    {
        public function __construct(
            private EntityManagerInterface $manager, 
            private MenuRepository $repository,
            private SerializerInterface $serializer,
            private UrlGeneratorInterface $urlGenerator,
            )
        {
        }

        #[Route(name: 'new', methods: ['POST'])]
        public function new(Request $request): Response
        {
            $menu = $this->serializer->deserialize($request->getContent(), Menu::class ,'json');

            $this->manager->persist($menu);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($menu, 'json');
            $location = $this->urlGenerator->generate(
                'app_menu_show',
                ['id' => $menu->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
            }

        #[Route('/{id}', name: 'show', methods: ['GET'])]
        public function show(int $id): Response
        {
            $menu = $this->repository->find($id);

            if($menu) 
                {
                    $responseData = $this->serializer->serialize($menu, 'json');
                    return new JsonResponse($responseData, Response::HTTP_OK, [], true);
            }

            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
            }

        #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
        public function edit(int $id, Request $request): Response
        {
            $menu = $this->repository->find($id);
            
            if($menu) 
                {
                $menu = $this->serializer->deserialize(
                    $request->getContent(),
                    Menu::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $menu]
                );

                $this->manager->flush();

                return new JsonResponse(null, Response::HTTP_NO_CONTENT);
            }

            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
        public function delete(int $id): Response
        {
            $menu = $this->repository->find($id);

            if(!$menu) 
                {
                throw $this->createNotFoundException("Il n'y a pas de menu identifié {$id}");
            }

            $this->manager->remove($menu);
            $this->manager->flush();

            return $this->json(['message' => "Ce menu a été supprimé"]);
        }
    }