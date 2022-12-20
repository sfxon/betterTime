<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DashboardController
 */
class AdminDashboardController extends AbstractController
{
    /**
     * index
     *
     * Dashboard action, shows the index page of the project.
     * 
     * @return Response
     */
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(): Response {
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }
}
