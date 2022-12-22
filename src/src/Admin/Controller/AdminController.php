<?php

namespace App\Admin\Controller;

use App\Admin\Model\AdminViewSettingModel;
use App\Entity\Admin;
use App\Service\PaginationService;
use App\Service\AdminService;
use App\Service\SettingService;
use App\Service\ViewService;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DashboardController
 */
class AdminController extends AbstractController
{
    private array $newAdminFormErrors = [];

    /**
     * index
     *
     * Dashboard action, shows the index page of the admin.
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param ViewService $viewService
     * @return Response
     */
    #[Route('/admin/admins', name: 'admin_list')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        ViewService $viewService
    ): Response {
        $adminViewSetting = $this->processSortOrderRequests($request, $doctrine, $viewService);
        $accountCreatedSuccess = $request->get('accountCreatedSuccess', false);

        $limit = 10;
        $page = (int)$request->query->get('page', 0);

        if ($page <= 0) {
            $page = 1;
        }

        // Count total pages.
        $adminService = new AdminService($doctrine);
        $adminCountTotal = $adminService->countAllAdmins();

        $pages = 0;

        if ($adminCountTotal != 0) {
            $pages = ceil($adminCountTotal / $limit);
        }

        if ($page > $pages) {
            $page = $pages;
        }

        // Load admins.
        $admins = $adminService->getAdmins(
            $limit,
            $page,
            $adminViewSetting->getSortBy(),
            $adminViewSetting->getSortOrder()
        );

        $pagination = new PaginationService($page, $pages, 5);

        return $this->render('admin/admin/index.html.twig', [
            'controller_name' => 'DashboardController',
            'accountCreatedSuccess' => $accountCreatedSuccess,
            'adminCountTotal' => $adminCountTotal,
            'admins' => $admins,
            'page' => $page,
            'pages' => $pages,
            'pagination' => $pagination->getPagination(),
            'adminViewSetting' => $adminViewSetting
        ]);
    }

    /**
     * Route to create a new admin account.
     * 
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  FormFactoryInterface $formFactory
     * @param  TranslatorInterface $translator
     * @param  AdminService $adminService
     * @return void
     */
    #[Route('/admin/admins/new', name: 'admin_admins_new')]
    public function new(
        Request $request, 
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator,
        AdminService $adminService
    ): Response
    {
        $this->translator = $translator;
        
        // Handle emails
        $newAdminForm = $this->createNewAdminForm($formFactory);
        $result = $this->handleNewAdminForm($request, $doctrine, $newAdminForm, $adminService, $passwordHasher);

        if($result !== null) {
            return $result;
        }

        return $this->render('admin/admin/new.html.twig', [
            'controller_name' => 'AccountController',
            'newAdminForm' => $newAdminForm->createView(),
            'newAdminFormErrors' => $this->newAdminFormErrors
        ]);
    }

    /**
     * processSortOrderRequests
     *
     * Fetches query parameters, which are used to order the admin list.
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param ViewService $viewService
     * @return AdminViewSettingModel
     */
    private function processSortOrderRequests(
        Request $request,
        ManagerRegistry $doctrine,
        ViewService $viewService
    ): AdminViewSettingModel {
        // Load current settings for sort order.
        $setting = new SettingService($doctrine);
        $settingJson = $setting->getSettingByTextId('admin.admin.setting');

        /** @var AdminViewSettingModel */
        $adminViewSetting = ViewService::loadViewFromJson($settingJson, AdminViewSettingModel::class);

        // Check, if a new sorting has been requested.
        $settingUpdated = false;
        $sortBy = $request->query->get('sortBy');
        $sortOrder = $request->query->get('sortOrder');

        if (null !== $sortBy) {
            $adminViewSetting->setSortBy($sortBy);
            $settingUpdated = true;
        }

        if (null !== $sortOrder) {
            $adminViewSetting->setSortOrder($sortOrder);
            $settingUpdated = true;
        }

        if ($settingUpdated) {
            $viewService->saveViewData($adminViewSetting, 'admin.admin.setting');
        }

        return $adminViewSetting;
    }

    /**
     * Creates the form for changing the accounts mail address.
     *
     * @param  FormFactoryInterface $formFactory
     * @param  array $defaultData
     * @return FormInterface
     */
    private function createNewAdminForm(FormFactoryInterface $formFactory,): FormInterface
    {
        $form = $formFactory
        ->createNamedBuilder('emailForm', FormType::class, null, [
            // Set action explicitely. We do not want previous get parameters to be set in the next submit.
            // because this would show the "success" message every time.
            'action' => $this->generateUrl('admin_admins_new'), 
        ])
        ->add(
            'email', 
            TextType::class, 
            [ 
                'label' => $this->translator->trans(
                    'admin.adminNew.emailInputLabel'
                ),
                'constraints' => array(
                    new Assert\Email([
                        'message' => $this->translator->trans(
                            'admin.adminNew.emailInvalid'
                        ),
                    ]),
                    new Assert\NotBlank(),
                )
            ]
        )
        ->add(
            'password',
            PasswordType::class,
            [
                'label' => $this->translator->trans(
                    'admin.adminNew.passwordInputLabel'
                ),
                'constraints' => array(
                    // NotBlank is needed, because the PasswordRequirements library does not check for blank.
                    new Assert\NotBlank([
                        'message' => $this->translator->trans(
                            'admin.adminNew.alertNewPasswordBlank'
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
                            'admin.adminNew.alertPasswordTooShort'
                        ),
                        'missingLettersMessage' => $this->translator->trans(
                            'admin.adminNew.alertPasswordMissingLetters'
                        ),
                        'requireCaseDiffMessage' => $this->translator->trans(
                            'admin.adminNew.alertPasswordRequireCaseDiff'
                        ),
                        'missingNumbersMessage' => $this->translator->trans(
                            'admin.adminNew.alertPasswordMissingNumbers'
                        ),
                        'missingSpecialCharacterMessage' => $this->translator->trans(
                            'admin.adminNew.alertPasswordMissingSpecialCharacter'
                        )
                    ])
                )
            ]
        )
        ->add(
            'save',
            SubmitType::class, [
                'label' => $this->translator->trans(
                    'admin.adminNew.emailButtonSubmit'
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
     * @param  AdminService $adminService
     * @param  UserPasswordHasherInterface $passwordHasher
     * @return Response|null
     */
    private function handleNewAdminForm(
        Request $request,
        ManagerRegistry $doctrine,
        FormInterface $form,
        AdminService $adminService,
        UserPasswordHasherInterface $passwordHasher): ?Response
    {
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if ($form->isValid()) {
                $admin = new Admin();
                
                $input = $form->getData();
                
                if($adminService->mailIsInUse($doctrine, $input['email'])) {
                    $this->newAdminFormErrors[] = $this->translator->trans(
                        'admin.adminNew.alertEmailInUse'
                    );
                    return null;
                }

                $admin->setPassword(
                    $passwordHasher->hashPassword($admin, $input['password'])
                );
                
                $admin->setEmail($input['email']);
                $em = $doctrine->getManager();
                $em->persist($admin);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_list', ['accountCreatedSuccess' => 'true']));
            }

            foreach ($form->getErrors(true) as $key => $error) {
                $this->newAdminFormErrors[] = $error->getMessage();
            }
        }

        return null;
    }
}
