<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
   public function testApiDocUrlIsSuccessful(): void
   {
      $client = self::createClient();
      $client->followRedirects(false);
      $client->request('GET', '/api/doc');

      self::assertResponseIsSuccessful();
   }

   public function testApiAccountUrlIsSuccessful(): void
   {
      $client = self::createClient();
      $client->followRedirects(false);
      $client->request('PATCH', '/api/account/me');

      self::assertResponseStatusCodeSame(401);
   }
}