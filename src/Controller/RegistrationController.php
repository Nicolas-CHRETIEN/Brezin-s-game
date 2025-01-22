<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    // /**
    //  * Create new user's object
    //  * If form is submitted and valid.
    //  *      set new user's properties.
    //  *      Save new user.
    //  *      Log user in.
    //  * Render registration page with registration form.
    //  * 
    //  * @Route("/register", name="app_register")
    //  * 
    //  * @param Request $request
    //  * @param UserPasswordHasherInterface $userPasswordHasher
    //  * @param UserAuthenticatorInterface $userAuthenticator
    //  * @param AppCustomAuthenticator $authenticator
    //  * @param EntityManagerInterface $entityManager
    //  * @return Response
    // */
    // public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    // {
    //     $user = new Users();
    //     $form = $this->createForm(RegistrationFormType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // encode the plain password
    //         $user->setPassword(
    //         $userPasswordHasher->hashPassword(
    //                 $user,
    //                 $form->get('plainPassword')->getData()
    //             )
    //         );

    //         $entityManager->persist($user);
    //         $entityManager->flush();
            
    //         return $userAuthenticator->authenticateUser(
    //             $user,
    //             $authenticator,
    //             $request 
    //         );
    //     }

    //     return $this->render('registration/register.html.twig', [
    //         'registrationForm' => $form->createView(),
    //     ]);
    // }
}
