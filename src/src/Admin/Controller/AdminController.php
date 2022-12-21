<?php

namespace App\Admin\Controller;

use App\Admin\Model\AdminViewSettingModel;
use App\Service\PaginationService;
use App\Service\AdminService;
use App\Service\SettingService;
use App\Service\ViewService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DashboardController
 */
class AdminController extends AbstractController
{
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
            'adminCountTotal' => $adminCountTotal,
            'admins' => $admins,
            'page' => $page,
            'pages' => $pages,
            'pagination' => $pagination->getPagination(),
            'adminViewSetting' => $adminViewSetting
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
}
