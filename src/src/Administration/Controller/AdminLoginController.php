<?php

namespace App\Administration\Controller;

use App\Entity\Administrator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminLoginController extends AbstractController {
    /**
     * index
     * 
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  TranslatorInterface $translator
     * @return Response
     */
    #[Route('/administration/login', name: 'administration_login')]
    public function index(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('admin/login/index.html.twig', [
            'controller_name' => 'AdminLoginController',
            'last_username' => $lastUsername,
            'error'  => $error
        ]);
    }

    #[Route(path: '/administration/logout', name: 'app_admin_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
