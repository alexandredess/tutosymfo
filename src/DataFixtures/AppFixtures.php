<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 50; $i++) { 
            $ingredient = new Ingredient();
            //le nom prendra la chaine de caractère 'ingredient' avec la valeur de $i
            $ingredient ->setName($this->faker->word())
            //on fait un random entre 0 et 100 pour le prix
                        ->setPrice(mt_rand(0,100));
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
