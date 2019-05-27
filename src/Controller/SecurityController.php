<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Token;
use App\Service\TokenSendler;
use App\Form\RegistrationType;
use App\Repository\TokenRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Registration
     *
     * @Route("/registration", name="registration")
     * 
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder, TokenSendler $tokenSendler){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $passwordEncoded = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncoded);

            $token = new Token($user);

            $manager->persist($token);
            $manager->flush();

            $tokenSendler->sendToken($user, $token);

            $this->addFlash(
                'success',
                "Votre compte a bien été créé ! Veuillez valider votre inscription en cliquant sur le lien que nous vous avons envoyé par mail !"
            );

            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView() 
            ]);
    }

    /**
     * Check if a token is valid
     *
     * @Route("/confirmation/{token}", name="token_validation")
     * 
     * @param Request $request
     * @param TokenRepository $tokenRepository
     * @param ObjectManager $manager
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     */
    public function validateToken($token, TokenRepository $tokenRepository, ObjectManager $manager, GuardAuthenticatorHandler $guardAuthenticatorHandler, LoginFormAuthenticator $loginFormAuthenticator, Request $request){
        $token = $tokenRepository->findOneBy(['value' => $token]);
        if(null === $token){
            throw new NotFoundHttpException();
        }
        
        $user = $token->getUser();

        if($token->isValid()){

            $user->setEnable(true);
            $manager->flush($user);

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthenticator,
                'main'
            );
        }
        $manager->remove($user);
        $manager->remove($token);
        $this->addFlash(
                'notice',
                "Le token est expiré, Inscrivez-vous à nouveau !"
            );

        return $this->redirectToRoute('registration');
    }


}
