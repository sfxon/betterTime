<?php

namespace App\Controller;

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


class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $defaultData = [
            'email' => $user->getEmail()
        ];
        
        $form = $this
        ->createFormBuilder($defaultData)
        ->add('email', TextType::class, [ 'label' => 'E-Mail'])
        ->add(
            'password',
            RepeatedType::class, 
            [ 
                'type' => PasswordType::class, 
                'required' => true, 
                'first_options' => [ 
                    'label' => 'Passwort'
                ],
                'second_options' => [
                    'label' => 'Passwort wiederholen'
                ]
            ])
        ->add('save', SubmitType::class, [ 'label' => 'Änderungen speichern'])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $input = $form->getData();

            $user->setEmail($input['email']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $input['password'])
            );

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_account'));
        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'accountForm' => $form->createView()
        ]);
    }
}
