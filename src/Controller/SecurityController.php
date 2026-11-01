<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Entity\Utilisateur;
use App\Repository\HoraireRepository;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA; 

#[Route('/api/account', name: 'app_security_')]
final class SecurityController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $manager,
    )
    {
    }

    #[Route('/registration', name: 'app_registration_', methods:['POST'])]

#[OA\Post(
    path: '/api/user/registration',
    summary: "Inscription d'un nouvel utilisateur",
    requestBody: new OA\RequestBody(
        required: true,
        description: "Données de l'utilisateur à inscrire",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "email", 
                    type:"string",
                    example:"adresse@mail.com"),
                
                new OA\Property(
                    property:"password",
                    type:"string",
                    example:"M0!d3p45sE"),
                    
                new OA\Property(
                    property:"prenom",
                    type:"string",
                    example:"Jean"),
                    
                new OA\Property(
                    property:"telephone",
                    type:"string",
                    example:"06 01 02 03 04"),
                    
                new OA\Property(
                    property:"ville",
                    type:"string",
                    example:"Paris"),
                    
                new OA\Property(
                    property:"pays",
                    type:"string",
                    example:"France"),
                    
                new OA\Property(
                    property:"adresse postale",
                    type:"string",
                    example:"1 rue de la République")        
            ],
            type:'object'
        )
    ),

    responses: [
        new OA\Response(
            response: 201,
            description: "Utilisateur inscrit avec succès",
            content: new OA\JsonContent(
                properties:[
                    new OA\Property(
                        property:"prenom",
                        type:"string",
                        example:"Jean"),
                        
                    new OA\Property(
                        property:"apiToken",
                        type:"string",
                        example:"42d018368z73210mcs74v102845r7492oen"),
                    ],
                type:   "object"
            )
        )
    ]
)]

    public function register(Request $request, UserPasswordHasherInterface $passwordHascher): JsonResponse
    {
        $utilisateur = $this->serializer->deserialize($request->getContent(), Utilisateur::class, 'json');

        $haschedPassword = $passwordHascher->hashPassword(
            $utilisateur,
            $utilisateur->getPassword()
        );
        $utilisateur->setPassword($haschedPassword);
        $utilisateur->setRoles(['ROLE_UTILISATEUR']);

        $this->manager->persist($utilisateur);
        $this->manager->flush();

        return new JsonResponse(
            [
                'utilisateur' => $utilisateur->getUserIdentifier(), 
                'roles'=>$utilisateur->getRoles()
                ],
            Response::HTTP_CREATED
        ); 
    }

    #[Route('/login', name: 'app_login_', methods:['POST'])]
    #[OA\Post(
        path:"/api/user/login",
        summary:"Connexion d'un utilisateur",
        requestBody: new OA\RequestBody(
            required:true,
            description:"Données nécessaires pour la connexion",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property:'email',
                        type:"string",
                        example:"mail@adresse.com"
                    ),
                    new OA\Property(
                        property:'password',
                        type:"string",
                        example:"M0!d3p45sE"
                    )
                ],
                type:'object'
            )
        ),

        responses: [
            new OA\Response(
                response:201,
                description:"Utilisateur connecté avec succès",
                content: new OA\JsonContent(
                    properties:[
                        new OA\Property(
                            property:"email",
                            type:"string",
                            example:"adresse@mail.com",
                        ),
                        new OA\Property(
                            property:"apiToken",
                            type:"string",
                            example:"42d018368z73210mcs74v102845r7492oen"
                        )
                    ],
                    type:"object"
                )
            )
        ]
    )]

    public function login(#[CurrentUser] ?Utilisateur $utilisateur): JsonResponse
    {
        if (null === $utilisateur) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $utilisateur->setApiToken((bin2hex(random_bytes(20))));
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


    #[Route('/me', name: 'app_me_', methods:['PATCH'])]
    public function me(#[CurrentUser] ?Utilisateur $utilisateur, CommandeRepository $commandesRepository): JsonResponse
    {
        if (null === $utilisateur) {
            return new JsonResponse(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }
        $commande = $commandesRepository->findBy(['utilisateur'=>$utilisateur]);
        $data = [
                'prenom' => $utilisateur->getPrenom(), 
                'telephone'=>$utilisateur->getTelephone(),
                'ville'=>$utilisateur->getVille(),
                'pays'=>$utilisateur->getPays(),
                'adresse postale'=>$utilisateur->getAdressePostale(),
                'commandes' => $commande,
            ];
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'commande:read']);
    }
    #[Route('/employe', name: 'app_employe_', methods:['PATCH'])]
    public function employe(#[CurrentUser] ?Utilisateur $utilisateur, MenuRepository $menusRepository, PlatRepository $platsRepository, HoraireRepository $horairesRepository): JsonResponse
    {
        if (null === $utilisateur) {
            return new JsonResponse(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }
        $menus = $menusRepository;
        $plats = $platsRepository;
        $horaires = $horairesRepository;
        $data = [
                'menus' => $menus,
                'plats' => $plats,
                'horaires' => $horaires,
            ];
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'commande:read']);
    }
}
