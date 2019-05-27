<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;


class Stats {
    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
    }

    public function getStats(){
        $users      = $this->getUsersCount();
        $dish       = $this->getDishCount();
        $category   = $this->getCategoryCount();

        return compact('users', 'dish', 'category');
    }
    
    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getDishCount(){
        return $this->manager->createQuery('SELECT COUNT(d) FROM App\Entity\Dish d')->getSingleScalarResult();
    }

    public function getCategoryCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Category c')->getSingleScalarResult();
    }

}