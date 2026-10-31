    <?php

    namespace App\Controller;

    use App\Entity\Horaire;
    use App\Repository\HoraireRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
    use Symfony\Component\Serializer\SerializerInterface;

    #[Route('/horaire', name: 'app_horaire_')]
    final class HoraireController extends AbstractController
    {
        public function __construct(
            private EntityManagerInterface $manager, 
            private HoraireRepository $repository,
            private SerializerInterface $serializer,
            private UrlGeneratorInterface $urlGenerator,
            )
        {
        }

        #[Route(name: 'new', methods: ['POST'])]
        public function new(Request $request): Response
        {
            $horaire = $this->serializer->deserialize($request->getContent(), Horaire::class ,'json');

            $this->manager->persist($horaire);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($horaire, 'json');
            $location = $this->urlGenerator->generate(
                'app_horaire_show',
                ['id' => $horaire->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
            }

        #[Route('/{id}', name: 'show', methods: ['GET'])]
        public function show(int $id): Response
        {
            $horaire = $this->repository->find($id);

            if($horaire) 
                {
                    $responseData = $this->serializer->serialize($horaire, 'json');
                    return new JsonResponse($responseData, Response::HTTP_OK, [], true);
            }

            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
            }

        #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
        public function edit(int $id, Request $request): Response
        {
            $horaire = $this->repository->find($id);
            
            if($horaire) 
                {
                $horaire = $this->serializer->deserialize(
                    $request->getContent(),
                    Horaire::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $horaire]
                );

                $this->manager->flush();

                return new JsonResponse(null, Response::HTTP_NO_CONTENT);
            }

            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
        public function delete(int $id): Response
        {
            $horaire = $this->repository->find($id);

            if(!$horaire) 
                {
                throw $this->createNotFoundException("Il n'y a pas d'horaire identifié {$id}");
            }

            $this->manager->remove($horaire);
            $this->manager->flush();

            return $this->json(['message' => "Cet horaire a été supprimé"]);
        }
    }

