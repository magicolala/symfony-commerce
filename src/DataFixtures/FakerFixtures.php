<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Marque;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class FakerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $marques = [];
        $categories = [];
        for ($j=0; $j < 5; $j++) {
            $marque = new Marque();
            $marque->setLogo($faker->imageUrl());
            $marque->setLien($faker->url);
            $marque->setName($faker->company);
            $manager->persist($marque);
            $marques[] = $marque;

            $categorie = new Categorie();
            $categorie->setTitre($faker->company);
            $categorie->setImg($faker->imageUrl());
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        for ($i=0; $i < 4; $i++) {


            for ($b = 0; $b < 12; $b++) {
                $produit = new Produit();
                $produit->setCategorie($categories[$faker->numberBetween(0,4)]);
                $produit->setTitre($faker->company);
                $produit->setPrix($faker->numberBetween(5, 5000));
                $produit->setImg($faker->imageUrl());
                $produit->setNote($faker->numberBetween(0,5));
                $produit->setMarque($marques[$faker->numberBetween(0,4)]);
                $produit->setDescription($faker->realText());
                $manager->persist($produit);

                $blog = new Blog();
                $blog->setCategorie($categories[$faker->numberBetween(0,4)]);
                $blog->setTitre($faker->company);
                $blog->setContent($faker->realText());
                $blog->setCreatedAt($faker->dateTime);
                $blog->setIsPublished($faker->boolean);
                $manager->persist($blog);

                for ($c = 0; $c < 5; $c++) {
                    $commentaire = new Commentaire();
                    $commentaire->setProduit($produit);
                    $commentaire->setUtilisateur($faker->name);
                    $commentaire->setCommentaire($faker->realText());
                    $manager->persist($commentaire);
                }
            }
        }

        $manager->flush();
    }
}