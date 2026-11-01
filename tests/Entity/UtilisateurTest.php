<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
   public function testTheAutomaticApiTokenSettingWhenAnUserIsCreated(): void
   {
      $utilisateur = new Utilisateur();
      $this->assertNotNull($utilisateur->getApiToken());
   }
}