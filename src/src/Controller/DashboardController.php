<?php

namespace App\Controller;

use App\Model\ProjectViewSettingModel;
use App\Service\PaginationService;
use App\Service\ProjectService;
use App\Service\SettingService;
use App\Service\TimeTrackingService;
use App\Service\ViewService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DashboardController
 */
class DashboardController extends AbstractController
{
    /**
     * index
     *
     * Dashboard action, shows the index page of the project.
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param ViewService $viewService
     * @return Response
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        ViewService $viewService
    ): Response {
        $projectViewSetting = $this->processSortOrderRequests($request, $doctrine, $viewService);

        $limit = 10;
        $page = (int)$request->query->get('page', 0);

        if ($page <= 0) {
            $page = 1;
        }

        // Count total pages.
        $projectService = new ProjectService($doctrine);
        $projectCountTotal = $projectService->countAllProjects();

        $pages = 0;

        if ($projectCountTotal != 0) {
            $pages = ceil($projectCountTotal / $limit);
        }

        if ($page > $pages) {
            $page = $pages;
        }

        // Load projects
        $projects = $projectService->getProjects(
            $limit,
            $page,
            $projectViewSetting->getSortBy(),
            $projectViewSetting->getSortOrder()
        );

        $timeTrackingService = new TimeTrackingService($doctrine);
        $notEnded = $timeTrackingService->loadAllNotEndedTimeTrackingEntries();
        $notEnded = $timeTrackingService->indexTimeTrackingResultsByProjectId($notEnded);

        $pagination = new PaginationService($page, $pages, 5);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'notEnded' => $notEnded,
            'projectCountTotal' => $projectCountTotal,
            'projects' => $projects,
            'page' => $page,
            'pages' => $pages,
            'pagination' => $pagination->getPagination(),
            'projectViewSetting' => $projectViewSetting
        ]);
    }

    /**
     * processSortOrderRequests
     *
     * Fetches query parameters, which are used to order the project list.
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param ViewService $viewService
     * @return ProjectViewSettingModel
     */
    private function processSortOrderRequests(
        Request $request,
        ManagerRegistry $doctrine,
        ViewService $viewService
    ): ProjectViewSettingModel {
        // Load current settings for sort order.
        $setting = new SettingService($doctrine);
        $settingJson = $setting->getSettingByTextId('view.project.setting');

        /** @var ProjectViewSettingModel */
        $projectViewSetting = ViewService::loadViewFromJson($settingJson, ProjectViewSettingModel::class);

        // Check, if a new sorting has been requested.
        $settingUpdated = false;
        $sortBy = $request->query->get('sortBy');
        $sortOrder = $request->query->get('sortOrder');

        if (null !== $sortBy) {
            $projectViewSetting->setSortBy($sortBy);
            $settingUpdated = true;
        }

        if (null !== $sortOrder) {
            $projectViewSetting->setSortOrder($sortOrder);
            $settingUpdated = true;
        }

        if ($settingUpdated) {
            $viewService->saveViewData($projectViewSetting, 'view.project.setting');
        }

        return $projectViewSetting;
    }
}
