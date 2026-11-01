<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHascher
    )
    {
    }
    public function load(ObjectManager $manager): void
    {
        for($i=1; $i<=20; $i++) {
        $utilisateur = (new Utilisateur())
            -> setPrenom("Prénom $i")
            -> setTelephone("060102030$i")
            -> setEmail("adresse$i@mail.com")
            -> setVille("Ville $i")
            -> setPays("Pays $i")
            -> setAdressePostale("Adresse $i");

        $utilisateur->setPassword($this->passwordHascher->hashPassword($utilisateur, "p45Sw0Rphpd$i"));
            

        $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
