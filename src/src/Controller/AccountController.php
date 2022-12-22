<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Shows the account page to show and edit the own user account.
 */
class AccountController extends AbstractController
{
    private $passwordErrors = [];
    private $emailErrors = [];
    private $translator = null;

    /**
     * Account page route.
     * 
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  FormFactoryInterface $formFactory
     * @param  TranslatorInterface $translator
     * @return void
     */
    #[Route('/account', name: 'app_account')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator
    ): Response
    {
        $this->translator = $translator;
        
        /** @var User $user */
        $user = $this->getUser();

        // Build and handle Email form.
        $emailSuccess = $request->get('emailSuccess', false);
        
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
            'emailErrors' => $this->emailErrors,
            'emailSuccess' => $emailSuccess,
            'passwordErrors' => $this->passwordErrors,
            'passwordSuccess' => $passwordSuccess,
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
        ->createNamedBuilder('emailForm', FormType::class, $defaultData, [
            // Set action explicitely. We do not want previous get parameters to be set in the next submit.
            // because this would show the "success" message every time.
            'action' => $this->generateUrl('app_account'), 
        ])
        ->add(
            'email', 
            TextType::class, 
            [ 
                'label' => $this->translator->trans(
                    'account.form.emailInputLabel'
                ),
                'constraints' => array(
                    new Assert\Email([
                        'message' => $this->translator->trans(
                            'account.alert.emailInvalid'
                        ),
                    ]),
                    new Assert\NotBlank(),
                )
            ]
        )
        ->add(
            'save',
            SubmitType::class, [
                'label' => $this->translator->trans(
                    'account.form.emailButtonSubmit'
                )
            ])
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
            if ($form->isValid()) {
                $input = $form->getData();
                
                if($this->newMailIsInUse($doctrine, $user, $input['email'])) {
                    $this->emailErrors[] = $this->translator->trans(
                        'account.alert.emailInUse'
                    );
                    return null;
                }
                
                $user->setEmail($input['email']);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('app_account', ['emailSuccess' => 'true']));
            }

            foreach ($form->getErrors(true) as $key => $error) {
                $this->emailErrors[] = $error->getMessage();
            }
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
        ->createNamedBuilder('passwordForm', FormType::class, null, [
            // Set action explicitely. We do not want previous get parameters to be set in the next submit.
            // because this would show the "success" message every time.
            'action' => $this->generateUrl('app_account'), 
        ])
        ->add(
            'previousPassword',
            PasswordType::class,
            [
                'label' => $this->translator->trans(
                    'account.form.currentPasswordInputLabel'
                ),
                'constraints' => array(
                    new Assert\NotBlank([
                        'message' => $this->translator->trans(
                            'account.alert.currentPasswordBlank'
                        ),
                    ]),
                    new SecurityAssert\UserPassword([
                        'message' => $this->translator->trans(
                            'account.alert.currentPasswordWrong'
                        )
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
                    'label' => $this->translator->trans(
                        'account.form.newPasswordLabel'
                    )
                ],
                'second_options' => [
                    'label' => $this->translator->trans(
                        'account.form.repeatNewPasswordLabel'
                    )
                ],
                'constraints' => [
                    // NotBlank is needed, because the PasswordRequirements library does not check for blank.
                    new Assert\NotBlank([
                        'message' => $this->translator->trans(
                            'account.alert.newPasswordBlank'
                        )
                    ]),
                    // Some information on configuration can be taken from the tests:
                    // https://github.com/rollerworks/PasswordStrengthValidator/blob/main/tests/Validator/PasswordRequirementsValidatorTest.php
                    new PasswordRequirements([
                        'minLength' => 6,
                        'requireLetters' => true,
                        'requireCaseDiff' => true,
                        'requireNumbers' => true,
                        'requireSpecialCharacter' => true,
                        'tooShortMessage' => $this->translator->trans(
                            'account.alert.passwordTooShort'
                        ),
                        'missingLettersMessage' => $this->translator->trans(
                            'account.alert.passwordMissingLetters'
                        ),
                        'requireCaseDiffMessage' => $this->translator->trans(
                            'account.alert.passwordRequireCaseDiff'
                        ),
                        'missingNumbersMessage' => $this->translator->trans(
                            'account.alert.passwordMissingNumbers'
                        ),
                        'missingSpecialCharacterMessage' => $this->translator->trans(
                            'account.alert.passwordMissingSpecialCharacter'
                        )
                    ])
                ]
            ])
        ->add(
            'save',
            SubmitType::class,
            [ 
                'label' => $this->translator->trans(
                    'account.form.passwordButtonSubmit'
                )
            ]
        )
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
    
    /**
     * Checks, if an emailAddress is in use by a different account,
     * than the current one.
     *
     * @param  ManagerRegistry $doctrine
     * @param  User $user
     * @param  string $email
     * @return bool
     */
    private function newMailIsInUse(
        ManagerRegistry $doctrine,
        User $user,
        string $email): bool
    {
        // Check the email-address against the users email-address.
        if($user->getEmail() == $email) {
            return false;
        }

        // Try to load a user with this email address from the database.
        $repository = $doctrine->getRepository(User::class);
        $emailUser = $repository->findOneBy(
            [
                'email' => $email
            ]
        );

        if(null === $emailUser) {
            return false;
        }

        return true;
    }
}
