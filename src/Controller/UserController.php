<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user")
     */
    public function index($page, Paginator $paginator)
    {
        $paginator->setEntityClass(User::class)
                  ->setPage($page);

        return $this->render('user/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Edit a user
     * 
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     *
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(User $user, Request $request, ObjectManager $manager){
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFirstName()}</strong> <strong>{$user->getLastName()}</strong> a bien été modifié !"
            );

        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a user
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(User $user, Request $request, ObjectManager $manager): Response
    {   

        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFirstName()}</strong> <strong>{$user->getLastName()}</strong> a bien été suprimé !"
        );
            
        return $this->redirectToRoute('admin_user');
        
    }
}
