<?php

namespace App\Controller;

use App\Service\ProjectService;
use App\Service\TimeTrackingService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $projectService = new ProjectService($doctrine);
        $projects = $projectService->getAllProjects();

        $timeTrackingService = new TimeTrackingService($doctrine);
        $notEnded = $timeTrackingService->loadAllNotEndedTimeTrackingEntries();
        $notEnded = $timeTrackingService->indexTimeTrackingResultsByProjectId($notEnded);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'projects' => $projects,
            'notEnded' => $notEnded
        ]);
    }
}
