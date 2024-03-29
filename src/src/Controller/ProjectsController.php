<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ProjectUserService;
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
        $user = $this->getUser();
        $name = $request->query->get('name');

        // Entity erstellen
        $entityManager = $doctrine->getManager();

        $project = new Project();
        $project->setUser($user);
        $project->setName($name);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($project);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * Show an editor to edit a project.
     *
     * @param  ProjectUserService $projectUserService
     * @param  string $id
     * @return Response
     */
    #[Route('/projects/edit/{id}', name: 'app_projects.edit')]
    public function edit(ProjectUserService $projectUserService, string $id): Response
    {
        $id = new Uuid($id);
        $user = $this->getUser();
        $project = $projectUserService->getProject($id, $user);

        if (null === $project) {
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
    public function ajaxSearch(Request $request, ProjectUserService $projectUserService): JsonResponse
    {
        $user = $this->getUser();
        $postJson = $request->getContent();
        $post = json_decode($postJson, true);

        if (!isset($post['searchTerm'])) {
            return new JsonResponse(
                ['searchResult' => [] ]
            );
        }

        $searchTerm = trim($post['searchTerm']);

        if (strlen($searchTerm == 0)) {
            return new JsonResponse(
                [ 'searchResult' => [] ]
            );
        }

        $searchResult = $projectUserService->searchByName($searchTerm, $user, 10);

        if ($searchResult === null) {
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
     * @param  ProjectUserService $ProjectUserService
     * @param  string $id
     * @return RedirectResponse
     */
    #[Route('/projects/update/{id}', name: 'app_projects.update')]
    public function update(
        Request $request,
        ManagerRegistry $doctrine,
        ProjectUserService $ProjectUserService,
        string $id
    ): RedirectResponse
    {
        $user = $this->getUser();
        $id = new Uuid($id);
        $name = $request->request->get('name');
        $entityManager = $doctrine->getManager();
        $project = $ProjectUserService->getProject($id, $user);

        if (null === $project) {
            throw new \Exception('Project with id ' . $id . ' not found.');
        }

        $project->setName($name);
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
