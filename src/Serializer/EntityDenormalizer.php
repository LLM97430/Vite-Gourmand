<?php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityDenormalizer implements DenormalizerInterface
{
    // 1. On injecte UNIQUEMENT l'EntityManager ici
   public function __construct(
      private EntityManagerInterface $entityManager
   ) {}

   // 2. Le type de retour est : mixed (l'objet entité trouvé)
   public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
   {
      return $this->entityManager->find($type, $data);
   }

   // 3. Le type de retour ici est : bool
   public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
   {
      return str_starts_with($type, 'App\Entity\\') 
         && (is_int($data) || is_string($data))
         && !is_numeric($type);
   }

   public function getSupportedTypes(?string $format): array
   {
      return [
         'App\Entity\*' => true,
      ];
   }
}