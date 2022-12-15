<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


/**
 * Shows the account page to show and edit the own user account.
 */
class AccountController extends AbstractController
{
    private $passwordErrors = [];

    /**
     * Account page route.
     * 
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  FormFactoryInterface $formFactory
     * @return void
     */
    #[Route('/account', name: 'app_account')]    
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Build and handle Email form.
        $defaultData = [
            'email' => $user->getEmail()
        ];

        $emailForm = $this->createEmailForm($formFactory, $defaultData);
        $result = $this->handleEmailForm($request, $doctrine, $emailForm, $user);

        if($result !== null) {
            return $result;
        }

        // Build and handle password form.
        $passwordForm = $this->createPasswordForm($formFactory, $doctrine, $user, $passwordHasher);
        $result = $this->handlePasswordForm($request, $doctrine, $passwordForm, $passwordHasher, $user);

        if($result !== null) {
            return $result;
        }

        $passwordSuccess = $request->get('passwordSuccess', false);

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'emailForm' => $emailForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'passwordErrors' => $this->passwordErrors,
            'passwordSuccess' => $passwordSuccess
        ]);
    }
    
    /**
     * Creates the form for changing the accounts mail address.
     *
     * @param  FormFactoryInterface $formFactory
     * @param  array $defaultData
     * @return FormInterface
     */
    private function createEmailForm(
        FormFactoryInterface $formFactory,
        array $defaultData): FormInterface
    {
        $form = $formFactory
        ->createNamedBuilder('emailForm', FormType::class, $defaultData)
        ->add('email', TextType::class, [ 'label' => 'E-Mail'])
        ->add('save', SubmitType::class, [ 'label' => 'Änderungen speichern'])
        ->getForm();

        return $form;
    }

    /**
     * Handles actions for the email form.
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  FormInterface $form
     * @param  User $user
     * @return Response|null
     */
    private function handleEmailForm(
        Request $request,
        ManagerRegistry $doctrine,
        FormInterface $form,
        User $user): ?Response
    {
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $input = $form->getData();
            $user->setEmail($input['email']);
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_account'));
        }

        return null;
    }
    
    /**
     * Creates the form for changing the accounts password.
     * 
     * @param  FormFactoryInterface $formFactory
     * 
     * @return FormInterface
     */
    private function createPasswordForm(
        FormFactoryInterface $formFactory): FormInterface
    {
        $form = $formFactory
        ->createNamedBuilder('passwordForm')
        ->add(
            'previousPassword',
            PasswordType::class,
            [
                'label' => 'Aktuelles Passwort',
                'constraints' => array(
                    new SecurityAssert\UserPassword([
                        'message' => 'Wrong value for your current password',
                    ])
                )
            ]
        )
        ->add(
            'password',
            RepeatedType::class, 
            [ 
                'type' => PasswordType::class, 
                'required' => true, 
                'first_options' => [ 
                    'label' => 'Neues Passwort'
                ],
                'second_options' => [
                    'label' => 'Neues Passwort wiederholen'
                ],
                'constraints' => [
                    // NotBlank is needed, because the PasswordRequirements library does not check for blank.
                    new Assert\NotBlank([
                        'message' => 'Bitte geben Sie ein Passwort ein.'
                    ]),
                    // Some information on configuration can be taken from the tests:
                    // https://github.com/rollerworks/PasswordStrengthValidator/blob/main/tests/Validator/PasswordRequirementsValidatorTest.php
                    new PasswordRequirements([
                        'minLength' => 6,
                        'requireLetters' => true,
                        'requireCaseDiff' => true,
                        'requireNumbers' => true,
                        'requireSpecialCharacter' => true,
                    ])
                ]
            ])
        ->add('save', SubmitType::class, [ 'label' => 'Änderungen speichern'])
        ->getForm();

        return $form;
    }
    
    /**
     * Handles actions for the password form.
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  FormInterface $form
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  User $user
     * @return Response|null
     */
    private function handlePasswordForm(
        Request $request,
        ManagerRegistry $doctrine,
        FormInterface $form,
        UserPasswordHasherInterface $passwordHasher,
        User $user): ?Response
    {
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->isValid()) {
                $input = $form->getData();
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $input['password'])
                );

                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('app_account', ['passwordSuccess' => 'true']));
            }

            foreach ($form->getErrors(true) as $key => $error) {
                $this->passwordErrors[] = $error->getMessage();
            }
        }

        return null;
    }
}
