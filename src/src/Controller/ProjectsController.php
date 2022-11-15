<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ProjectService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * ProjectsController
 */
class ProjectsController extends AbstractController
{
    /**
     * Create a project entry in the database.
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/projects/create', name: 'app_projects.create')]    
    public function create(Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $name = $request->query->get('name');

        // Entity erstellen
        $entityManager = $doctrine->getManager();

        $product = new Project();
        $product->setName($name);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * Show an editor to edit a project.
     *
     * @param  ProjectService $projectService
     * @param  string $id
     * @return Response
     */
    #[Route('/projects/edit/{id}', name: 'app_projects.edit')]
    public function edit(ProjectService $projectService, string $id): Response
    {
        $id = new Uuid($id);
        $project = $projectService->getProject($id);

        if(null === $project) {
            throw new \Exception('Project with id ' . $id . ' not found.');
        }

        return $this->render('projects/edit-project.html.twig', [
            'controller_name' => 'ProjectsController',
            'project' => $project
        ]);
    }
    
    /**
     * Show an editor to create a new project.
     *
     * @return Response
     */
    #[Route('/projects/new', name: 'app_projects.new')]
    public function new(): Response
    {
        return $this->render('projects/new-project.html.twig', [
            'controller_name' => 'ProjectsController',
        ]);
    }

    /**
     * Answer an Ajax Search.
     *
     * @return Response
     */
    #[Route('/projects/ajaxSearch', name: 'app_projects.search')]
    public function ajaxSearch(Request $request, ProjectService $projectService): JsonResponse
    {
        $postJson = $request->getContent();
        $post = json_decode($postJson, true);
        
        if(!isset($post['searchTerm'])) {
            return new JsonResponse(
                ['searchResult' => [] ]
            );
        }

        $searchTerm = trim($post['searchTerm']);

        if(strlen($searchTerm == 0)) {
            return new JsonResponse(
                [ 'searchResult' => [] ]
            );
        }

        $searchResult = $projectService->searchByName($searchTerm, 10);

        if($searchResult === null) {
            return new JsonResponse(
                [ 'searchResult' => [] ]
            );
        }

        return new JsonResponse(
            [ 'searchResult' => $searchResult ]
        );
    }

    /**
     * Update an entry in the database.
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  ProjectService $projectService
     * @param  string $id
     * @return RedirectResponse
     */
    #[Route('/projects/update/{id}', name: 'app_projects.update')]
    public function update(
        Request $request,
        ManagerRegistry $doctrine,
        ProjectService $projectService,
        string $id): RedirectResponse
    {
        $id = new Uuid($id);
        $name = $request->request->get('name');
        $entityManager = $doctrine->getManager();
        $project = $projectService->getProject($id);

        if(null === $project) {
            throw new \Exception('Project with id ' . $id . ' not found.');
        }

        $project->setName($name);
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
