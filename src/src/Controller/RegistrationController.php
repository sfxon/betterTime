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

/**
 * RegistrationController
 */
class RegistrationController extends AbstractController
{

    /**
     * index
     *
     * @return Response
     */
    #[Route('/registration', name: 'app_registration')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        
        $form = $this->createFormBuilder()
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
        ->add('registrieren', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $input = $form->getData();

            $user = new User();
            $user->setEmail($input['email']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $input['password'])
            );

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'registrationForm' => $form->createView()
        ]);
    }
}
