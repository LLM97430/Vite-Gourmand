<?php 
//Controller permettant de gérer les routes des avis

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvisRepository;
use App\Entity\Avis;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/avis', name: 'app_avis_')]
final class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) 
    {
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request, #[CurrentUser] ?Utilisateur $utilisateur): Response
    {
        $avis = $this->serializer->deserialize($request->getContent(), type: Avis::class, format:'json');

        $avis->setUtilisateur($utilisateur);

        $this->manager->persist($avis);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($avis, 'json');
        $location = $this->urlGenerator->generate(
            'app_avis_show',
            ['id' => $avis->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $avis = $this->repository->find($id);

        if($avis) {
        $responseData = $this->serializer->serialize($avis, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): Response{
        $avis = $this->repository-> find($id);

        if($avis) {
        
            $avis = $this->serializer->deserialize(
                $request->getContent(),
                Avis::class,
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $avis]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response{
    $avis = $this->repository->find($id);

    if(!$avis){
        throw $this->createNotFoundException("Il n'y a pas d'avis identifié {$id}");
    }
    $this->manager->remove($avis);
    $this->manager->flush();

    return $this->json(['message'=>"L'avis est supprimé."]);
    }
}
