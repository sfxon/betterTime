<?php

namespace App\Controller;

use App\Model\ProjectViewSettingModel;
use App\Service\PaginationService;
use App\Service\ProjectService;
use App\Service\SettingService;
use App\Service\TimeTrackingService;
use App\Service\ViewLoaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ManagerRegistry $doctrine, 
        Request $request,
        SettingService $settingService): Response
    {        
        $projectViewSetting = $this->processSortOrderRequests($doctrine, $request);
        
        $limit = 10;
        $page = (int)$request->query->get('page', 0);

        if($page <= 0) {
            $page = 1;
        }

        // Count total pages.
        $projectService = new ProjectService($doctrine);
        $projectCountTotal = $projectService->countAllProjects();
        
        $pages = 0;

        if($projectCountTotal != 0) {
            $pages = ceil($projectCountTotal / $limit);
        }

        if($page > $pages) {
            $page = $pages;
        }

        // Load projects
        $projects = $projectService->getProjects($limit, $page);

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

    private function processSortOrderRequests(ManagerRegistry $doctrine, Request $request) {
        // Load current settings for sort order.
        $setting = new SettingService($doctrine);
        $settingJson = $setting->getSettingByTextId('view.project.setting');
        $projectViewSetting = ViewLoaderService::loadViewFromJson($settingJson, ProjectViewSettingModel::class);
        
        // Check, if a new sorting has been requested.
        $sortBy = $request->query->get('sortBy');
        $sortOrder = $request->query->get('sortOrder');

        if(null !== $sortBy) {
            $projectViewSetting->setSortBy($sortBy);
        }

        if(null !== $sortOrder) {
            $projectViewSetting->setSortOrder($sortOrder);
        }

        return $projectViewSetting;
    }
}
