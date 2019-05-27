<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        
        
        //handle adminUser
        $adminUser = new User();
        $adminUser->setEmail('birna.gerald@gmail.com')
                  ->setFirstName('ADMIN')
                  ->setLastName('Kenta')
                  ->setPassword($this->encoder->encodePassword($adminUser, 'password'))
                  ->setRoles(['ROLE_ADMIN'])
                  ->setEnable(0);

        $manager->persist($adminUser);

        //handle users
        // $users = [];
        // $genres = ['male', 'female'];

        
        $user = new User();

        // $genre = $faker->randomElement($genres);

        $user->setEmail('prolangamerz@gmail.com')
             ->setFirstName($faker->firstname)
             ->setLastName($faker->lastname)
             ->setPassword($this->encoder->encodePassword($user, 'password'))
             ->setRoles(['ROLE_USER'])
             ->setEnable(0);

        $manager->persist($user);
            
        

        $manager->flush();
    }
}