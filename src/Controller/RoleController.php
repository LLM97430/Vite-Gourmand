<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/role', name: 'app_role_')]
final class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $role = $this->serializer->deserialize($request->getContent(), Role::class ,'json');

        $this->manager->persist($role);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($role, 'json');
        $location = $this->urlGenerator->generate(
            'app_role_show',
            ['id' => $role->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $role = $this->repository->find($id);

        if($role) 
            {
                $responseData = $this->serializer->serialize($role, 'json');
                return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response
    {
        $role = $this->repository->find($id);
        
        if($role) 
            {
            $role = $this->serializer->deserialize(
                $request->getContent(),
                Role::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $role]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $role = $this->repository->find($id);

        if(!$role) 
            {
            throw $this->createNotFoundException("Il n'y a pas de role identifié {$id}");
        }

        $this->manager->remove($role);
        $this->manager->flush();

        return $this->json(['message' => "Ce role a été supprimé"]);
    }
}