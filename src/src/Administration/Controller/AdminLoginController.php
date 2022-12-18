<?php

namespace App\Administration\Controller;

use App\Entity\User;
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
    #[Route('/admin/login', name: 'app_admin_login')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator
    ): Response
    {
        /*
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        */

        $this->translator = $translator;
        
        $form = $this->createFormBuilder()
        ->add(
            'email',
            TextType::class,
            [ 
                'label' => $this->translator->trans(
                    'admin.login.emailInputLabel'
                ),
            ]
        )
        ->add(
            'password', 
            PasswordType::class,
            [
                'required' => true, 
                'label' => $this->translator->trans(
                    'admin.login.passwordInputLabel'
                )
            ]
        )
        ->add('submit', SubmitType::class, [
            'label' => $this->translator->trans(
                'admin.login.submit'
            )
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $input = $form->getData();

            var_dump($input);

            /*

            $user = new User();
            $user->setEmail($input['email']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $input['password'])
            );

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('index'));
            */
        }
        
        return $this->render('admin/login/index.html.twig', [
            'controller_name' => 'AdminLoginController',
            'adminLoginForm' => $form->createView()
        ]);
    }
}
