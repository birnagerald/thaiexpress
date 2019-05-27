<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\Paginator;
use App\Repository\DishRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/{page<\d+>?1}", name="admin_category")
     */
    public function index($page, Paginator $paginator)
    {
        $paginator->setEntityClass(Category::class)
                  ->setPage($page);

        return $this->render('category/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Create a category
     * 
     * @Route("/admin/category/new", name="admin_category_create")
     * 
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager): Response
    {   
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getName()}</strong> a bien été enregistrée !"
            );
            
            return $this->redirectToRoute('admin_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a category
     * 
     * @Route("/admin/category/{id}/edit", name="admin_category_edit")
     *
     * @param Category $category
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Category $category, Request $request, ObjectManager $manager){
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getName()}</strong> a bien été modifiée !"
            );

        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a category
     * 
     * @Route("/admin/category/{id}/delete", name="admin_category_delete")
     *
     * @param Category $category
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Category $category, Request $request, ObjectManager $manager): Response
    {   

        $manager->remove($category);
        $manager->flush();

        $this->addFlash(
            'success',
            "La catégorie <strong>{$category->getName()}</strong> a bien été suprimée !"
        );
            
        return $this->redirectToRoute('admin_category');
        
    }
}
