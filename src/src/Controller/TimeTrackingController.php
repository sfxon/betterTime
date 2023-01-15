<?php

namespace App\Controller;

use App\Service\DateService;
use App\Service\ProjectUserService;
use App\Service\TimeTrackingService;
use App\Service\InternalStatService;
use App\Entity\Project;
use App\Entity\TimeTracking;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * TimeTrackingController
 */
class TimeTrackingController extends AbstractController
{
    /**
     * edit
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/timetracking/edit', name: 'app_time_tracking.edit')]
    public function edit(Request $request, ManagerRegistry $doctrine): Response
    {
        $timeTrackingId = $request->query->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);

        if (0 === $timeTrackingId) {
            die('Keine timeTrackingId übergeben.');
        }

        // Eintrag laden.
        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        return $this->render('time_tracking/edit.html.twig', [
            'controller_name' => 'TimeTrackingController',
            'timeTracking' => $timeTracking
        ]);
    }

    /**
     * end
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/timetracking/end', name: 'app_time_tracking.end')]
    public function end(Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $timeTrackingId = $request->query->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);
        $from = $request->query->get('from');

        if (0 === $timeTrackingId) {
            die('Keine TimeTrackingId übergeben.');
        }

        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if ($timeTracking === null) {
            throw new \Exception('Timetracking entry with id ' . $timeTrackingId . ' not found.');
        }

        $timeTrackingService->endTimeTracking($timeTrackingId);

        // Check, if you should redirect to different page.
        if ($from !== null) {
            $project = $timeTracking->getProject();
            $projectId = $project->getId();

            return $this->redirectToRoute('app_time_tracking.list.project.times', [ 'project_id' => $projectId ]);
        }

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * endDialog
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @param  InternalStatService $internalStatService
     * @param  ProjectUserService $ProjectUserService
     * @return Response
     */
    #[Route('/timetracking/endDialog', name: 'app_time_tracking.end.dialog')]
    public function endDialog(
        Request $request,
        ManagerRegistry $doctrine,
        InternalStatService $internalStatService,
        ProjectUserService $ProjectUserService
    ): Response
    {
        $user = $this->getUser();
        $timeTrackingId = $request->query->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);
        $redirectTo = $request->query->get('redirectTo');

        if (0 === $timeTrackingId) {
            die('Keine TimeTrackingId übergeben.');
        }

        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if ($timeTracking === null) {
            throw new \Exception('Timetracking entry with id ' . $timeTrackingId . ' not found.');
        }

        // Check if start is current date.
        $startAndEndIsSameDate = DateService::isDatetimeToday($timeTracking->getStarttime());

        // Load last used entries.
        $lastUsedProjectIds = $internalStatService->loadLastUsedEntries('project', 10);
        $lastUsedProjects = $ProjectUserService->loadListByIds($lastUsedProjectIds, $user);
        $lastUsedProjects = $timeTrackingService->prependLastUsedProjectsWithCurrentProject($lastUsedProjects, $timeTracking);

        return $this->render('time_tracking/end-dialog.html.twig', [
            'controller_name' => 'TimeTrackingController',
            'timeTracking' => $timeTracking,
            'redirectTo' => $redirectTo,
            'endDatetimeNow' => time(),
            'showInvoiceNumber' => false,
            'startAndEndIsSameDate' => $startAndEndIsSameDate,
            'lastUsedProjects' => $lastUsedProjects
        ]);
    }

    /**
     * listProjectTimes
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/timetracking/listProjectTimes', name: 'app_time_tracking.list.project.times')]
    public function listProjectTimes(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $projectId = $request->query->get('project_id');
        $projectId = new Uuid($projectId);

        if (null === $projectId) {
            die('Keine projectId übergeben.');
        }

        $ProjectUserService = new ProjectUserService($doctrine);
        $project = $ProjectUserService->getProject($projectId, $user);

        if (null === $project) {
            throw new \Exception('No project with id ' . $projectId . ' found.');
        }

        $timeTrackingService = new TimeTrackingService($doctrine);
        $trackedTimes = $timeTrackingService->loadTimeTrackingsByProject($project);

        return $this->render('time_tracking/list-by-project.html.twig', [
            'controller_name' => 'TimeTrackingController',
            'project' => $project,
            'timeTrackings' => $trackedTimes
        ]);
    }

    /**
     * start
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/timetracking/start', name: 'app_time_tracking.start')]
    public function start(Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $user = $this->getUser();
        $projectId = $request->query->get('project_id');
        $projectId = new Uuid($projectId);
        $from = $request->query->get('from');

        if (0 === $projectId) {
            die('Keine Project ID gefunden');
        }

        // Load project
        $ProjectUserService = new ProjectUserService($doctrine);
        $project = $ProjectUserService->getProject($projectId, $user);

        // Check, if a timetracking is already started for this project.
        $timeTrackingService = new TimeTrackingService($doctrine);
        $openTimeTracking = $timeTrackingService->loadOpenTimeTrackingForProject($project);

        if ($openTimeTracking !== null) {
            die('Es läuft noch Projektzeit für dieses Projekt');
        }

        $timeTrackingService->startTimeTracking($project);

        if ($from == 'timetrackinglist') {
            return $this->redirectToRoute('app_time_tracking.list.project.times', [ 'project_id' => $projectId ]);
        }

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * update
     *
     * @param  Request $request
     * @param  ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/timetracking/update', name: 'app_time_tracking.update')]
    public function update(Request $request, ManagerRegistry $doctrine, InternalStatService $internalStatService): RedirectResponse
    {
        $user = $this->getUser();
        $timeTrackingId = $request->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);
        $projectId = $request->get('projectId');
        $starttime = $request->get('starttime');
        $endtime = $request->get('endtime');
        $useOnInvoice = (int)$request->get('use_on_invoice');
        $invoiceId = (int)$request->get('invoice_id');
        $comment = $request->get('comment');
        $redirectTo = $request->get('redirectTo');

        // Load the timetracking entry from database.
        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if (null === $timeTracking) {
            throw new \Exception('No timetracking entry with id ' . htmlspecialchars($timeTrackingId) . ' has been found');
        }

        // Check datetime
        $starttime = new \DateTime(date('Y-m-d H:i:s', strtotime($starttime)));
        $endtime = new \DateTime(date('Y-m-d H:i:s', strtotime($endtime)));

        if(!DateService::secondDateIsBigger($starttime, $endtime)) {
            throw new \Exception('Endtime date must be bigger than starttime.');
        }

        // Load the project entry from database.
        $ProjectUserService = new ProjectUserService($doctrine);
        $project = $ProjectUserService->loadById(
            Uuid::fromString($projectId), 
            $user
        );

        // Update all the values
        $timeTracking->setProject($project);
        $timeTracking->setStarttime($starttime);
        $timeTracking->setEndtime($endtime);

        if ($useOnInvoice === 1) {
            $timeTracking->setUseOnInvoice(true);
        } else {
            $timeTracking->setUseOnInvoice(false);
        }

        if (0 === $invoiceId) {
            $timeTracking->setInvoiceId(null);
        } else {
            $timeTracking->setInvoiceId($invoiceId);
        }

        $timeTracking->setComment($comment);

        // Update the timeTracking entry in the database.
        $timeTrackingService->update($timeTracking);

        // Update internal statistics.
        if ($projectId !== null) {
            $internalStatService->trackEntityUsage('project', Uuid::fromString($projectId));
        }

        return $this->redirectUpdate($redirectTo, $project);
    }

    /**
     * Redirect to another page by rules.
     *
     * @param  string $redirectTo
     * @param  ?Project $project
     * @return RedirectResponse
     */
    private function redirectUpdate(?string $redirectTo, ?Project $project): RedirectResponse
    {
        // Redirect to list of trackedTimes for a project.
        if ($redirectTo === 'app_time_tracking.list.project.times') {
            if ($project !== null) {
                return $this->redirectToRoute(
                    'app_time_tracking.list.project.times',
                    [ 'project_id' => $project->getId() ]
                );
            }
        }

        // This is the default redirect route.
        return $this->redirectToRoute(
            'app_dashboard',
        );
    }
}
