<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/', name: 'app_security_')]
final class SecurityController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $manager,
    )
    {
    }

    #[Route('/registration', name: 'app_registration_', methods:['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHascher): JsonResponse
    {
        $utilisateur = $this->serializer->deserialize($request->getContent(), Utilisateur::class, 'json');

        if(!$utilisateur->getApiToken()) {
            $utilisateur->setApiToken((bin2hex(random_bytes(20))));
            }

        $this->manager->persist($utilisateur);
        $this->manager->flush();

        return new JsonResponse(
            [
                'utilisateur' => $utilisateur->getUserIdentifier(), 
                'apiToken' => $utilisateur->getApiToken(), 
                'roles'=>$utilisateur->getRoles()
                ],
            Response::HTTP_CREATED
        ); 
    }

    #[Route('/login', name: 'app_login_', methods:['POST'])]
    public function login(#[CurrentUser] ?Utilisateur $utilisateur): JsonResponse
    {
        if (null === $utilisateur) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse(
            [
                'utilisateur' => $utilisateur->getUserIdentifier(), 
                'apiToken' => $utilisateur->getApiToken(), 
                'roles'=>$utilisateur->getRoles()
            ],
            Response::HTTP_CREATED
        ); 
    }
}
