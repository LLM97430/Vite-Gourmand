<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/commande', name: 'app_commande_')]
final class CommandeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CommandeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) 
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request, #[CurrentUser] ?Utilisateur $utilisateur): Response
    {
        $commande = $this->serializer->deserialize($request->getContent(), type: Commande::class, format:'json');

        $commande->setDateCommande(new DateTime());
        $commande->setStatut("accepté");
        $commande->setUtilisateur($utilisateur);

        $this->manager->persist($commande);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($commande, 'json');
        $location = $this->urlGenerator->generate(
            'app_commande_show',
            ['numeroCommande' => $commande->getNumeroCommande()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{numeroCommande}', name: 'show', methods: ['GET'])]
    public function show(int $numeroCommande): Response
    {
        $commande = $this->repository->find($numeroCommande);

        if($commande) {
        $responseData = $this->serializer->serialize($commande, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{numeroCommande}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $numeroCommande, Request $request): Response{
        $commande = $this->repository-> find($numeroCommande);

        if($commande) {
        
            $commande = $this->serializer->deserialize(
                $request->getContent(),
                Commande::class,
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $commande]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{numeroCommande}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $numeroCommande): Response{
    $commande = $this->repository->find($numeroCommande);

    if(!$commande){
        throw $this->createNotFoundException("Il n'y a pas d'commande identifié {$numeroCommande}");
    }
    $this->manager->remove($commande);
    $this->manager->flush();

    return $this->json(['message'=>"La commande est supprimée."]);
    }
}
