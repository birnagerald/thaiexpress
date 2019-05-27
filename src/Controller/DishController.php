<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Service\Paginator;
use App\Service\ImageHandler;
use App\Repository\DishRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DishController extends AbstractController
{
    /**
     * @Route("/admin/dish/{page<\d+>?1}", name="admin_dish")
     */
    public function index($page, Paginator $paginator)
    {
        $paginator->setEntityClass(Dish::class)
                  ->setPage($page);

        return $this->render('dish/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Create a dish
     * 
     * @Route("/admin/dish/new", name="admin_dish_create")
     * 
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager): Response
    {   
        $dish = new Dish;
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $path = $this->getParameter('kernel.project_dir'). '/public/images/upload/';
            $dish = $form->getData();

            $image = $dish->getImage();

            $image->setPath($path);

            $manager->persist($dish);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le plat <strong>{$dish->getName()}</strong> a bien été enregistré !"
            );
            
            return $this->redirectToRoute('admin_dish');
        }

        return $this->render('dish/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a dish
     * 
     * @Route("/admin/dish/{id}/edit", name="admin_dish_edit")
     *
     * @param Dish $dish
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Dish $dish, Request $request, ObjectManager $manager){
        $form = $this->createForm(DishType::class, $dish);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $path = $this->getParameter('kernel.project_dir'). '/public/images/upload/';
            $image = $form->getData()->getImage();
            $image->setPath($path);
            $manager->flush();
            $this->addFlash(
                'success',
                "Le plat <strong>{$dish->getName()}</strong> a bien été modifié !"
            );

        }

        return $this->render('dish/edit.html.twig', [
            'dish' => $dish,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a dish
     * 
     * @Route("/admin/dish/{id}/delete", name="admin_dish_delete")
     *
     * @param Dish $dish
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Dish $dish, Request $request, ObjectManager $manager): Response
    {   
        $path = $this->getParameter('kernel.project_dir'). '/public/images/upload/';
        $dish->removeImageOnDeleteDish($path);
        $manager->remove($dish);
        $manager->flush();
        

        $this->addFlash(
            'success',
            "Le plat <strong>{$dish->getName()}</strong> a bien été suprimé !"
        );
            
        return $this->redirectToRoute('admin_dish');
        
    }

}
