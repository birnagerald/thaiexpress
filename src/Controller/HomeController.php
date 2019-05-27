<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(DishRepository $dish)
    {

        return $this->render('home/index.html.twig', [
            'dishes' => $dish->findAll()
        ]);
    }
}
