<?php

namespace App\DataFixtures;

use App\Entity\TransportEnCommun;
use App\Entity\TypeTransport;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);

        //Création des types de transport

        $typeTransport = ["Bus", "Tramway", "Metro", "Funiculaire"];

        $listTypeTransport = [];

        for ($ii = 0; $ii < count($typeTransport); $ii++) {
            $transport = new TypeTransport();
            $transport->setLibelle($typeTransport[$ii]);
            $manager->persist($transport);

            $listTypeTransport[$ii] = $transport;
        }
/*
        $bus = new TypeTransport();
        $bus->setLibelle("Bus");
        $manager->persist($bus);

        $listTypeTransport[0] = $bus;

        $tramway = new TypeTransport();
        $tramway->setLibelle("Tramway");
        $manager->persist($tramway);

        $listTypeTransport[1] = $tramway;

        $metro = new TypeTransport();
        $metro->setLibelle("Metro");
        $manager->persist($metro);

        $listTypeTransport[2] = $metro;

        $funiculaire = new TypeTransport();
        $funiculaire->setLibelle("Funiculaire");
        $manager->persist($funiculaire);

        $listTypeTransport[3] = $funiculaire;
*/

        $listTerminus = ["Charpenne", "Gare d'Oullin", "Ecully grand Ouest", "Dardilly", "Monplaisir"];

        // Création d'une vingtaine de moyen de transport
        for ($ii = 1; $ii < 20; $ii++) {
            $transport = new TransportEnCommun;
            $transport->setNomTransport('C' . $ii);
            //$transport->setTypeTransport($ii);
            $transport->setTerminusDepart($listTerminus[array_rand($listTerminus)]);
            // On ajoute au hasard les type de transports pour chaque transport
            $transport->setTypeTransport($listTypeTransport[array_rand($listTypeTransport)]);
            $manager->persist($transport);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
