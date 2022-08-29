<?php

namespace App\DataFixtures;

use App\Entity\SeasData;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    // // symfony console doctrine:fixtures:load 
    public function load(ObjectManager $manager): void
    {
        /*****************************************************************
         * themes
         */
        $themes = array(
            'Fleuve & Hydrologie',
            'Littoral & Maritime',
            'Forêt',
            'Géologie',
            'Santé',
            'Urbanisation',
            'Agriculture',
            'Energie',
            'Biodiversité',
            'Occupation des sols',
            'Risques'
        );
        
        foreach($themes as $value){
            
            $src = __DIR__.'/images/themes/'.$value.'.png';
            
            $file = new UploadedFile(
                $src,
                $value.'.png',
                filesize($src),
                null,
                true //  Set test mode true !!! " Local files are used in test mode hence the code should not enforce HTTP uploads."
            );

            $theme = new Theme();
            $theme->setTheme($value);
            $theme->setImageFile($file);
            
            $manager->persist($theme);
        }

        /*****************************************************************
         * seasdata
         */
        for ($i=1; $i < 11; $i++) {
            
            $src = __DIR__.'/images/sat/sat'.$i.'.png';
            
            $file = new UploadedFile(
                $src,
                $i.'.png',
                filesize($src),
                null,
                true //  Set test mode true !!! " Local files are used in test mode hence the code should not enforce HTTP uploads."
            );
            
            $seasdata = new SeasData();
            $seasdata->setImageFile($file);
            $seasdata->setDate($this->faker->dateTimeThisYear());
            $seasdata->setFournisseur($this->faker->words(3, true));
            $seasdata->setEchelle('echelle-'.$i % 2);
            $seasdata->setCapteur('capteur-'.$i % 3);
            $seasdata->setVehicule('vehicule-'.$i % 2);
            $seasdata->setResolution('Resolution'.$i % 4);
            $seasdata->setTypeDeProduit('type-de-produit'.$i % 6);
            
            for ($i=1; $i < rand(1,11); $i++) {
                $seasdata->addTheme($manager->find('Theme' ,rand(1,11)));
                $manager->persist($seasdata);
            }
            
        }
        
        $manager->flush();
    }
}