<?php

namespace App\Controller;

use App\Service\PaginationService;
use App\Service\ProjectService;
use App\Service\SettingService;
use App\Service\TimeTrackingService;
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
            'pagination' => $pagination->getPagination()
        ]);
    }
}
