<?php

namespace App\Controller;

use App\Service\ProjectService;
use App\Service\TimeTrackingService;
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

        if(0 === $timeTrackingId) {
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

        if(0 === $timeTrackingId) {
            die('Keine TimeTrackingId übergeben.');
        }

        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if($timeTracking === null) {
            throw new \Exception('Timetracking entry with id ' . $timeTrackingId . ' not found.');
        }
        
        $timeTrackingService->endTimeTracking($timeTrackingId);

        // Check, if you should redirect to different page.
        if($from !== null) {
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
     * @return Response
     */
    #[Route('/timetracking/endDialog', name: 'app_time_tracking.end.dialog')]
    public function endDialog(Request $request, ManagerRegistry $doctrine): Response
    {
        $timeTrackingId = $request->query->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);
        $forwardTo = $request->query->get('forwardTo');

        if(0 === $timeTrackingId) {
            die('Keine TimeTrackingId übergeben.');
        }

        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if($timeTracking === null) {
            throw new \Exception('Timetracking entry with id ' . $timeTrackingId . ' not found.');
        }

        return $this->render('time_tracking/end-dialog.html.twig', [
            'controller_name' => 'TimeTrackingController',
            'timeTracking' => $timeTracking,
            'forwardTo' => $forwardTo,
            'endDatetimeNow' => time(),
            'showInvoiceNumber' => false
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
        $projectId = $request->query->get('project_id');
        $projectId = new Uuid($projectId);

        if(null === $projectId) {
            die('Keine projectId übergeben.');
        }

        $projectService = new ProjectService($doctrine);
        $project = $projectService->getProject($projectId);

        if(null === $project) {
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
        $projectId = $request->query->get('project_id');
        $projectId = new Uuid($projectId);
        $from = $request->query->get('from');

        if(0 === $projectId) {
            die('Keine Project ID gefunden');
        }

        // Load project
        $projectService = new ProjectService($doctrine);
        $project = $projectService->getProject($projectId);

        // Check, if a timetracking is already started for this project.
        $timeTrackingService = new TimeTrackingService($doctrine);
        $openTimeTracking = $timeTrackingService->loadOpenTimeTrackingForProject($project);

        if($openTimeTracking !== null) {
            die('Es läuft noch Projektzeit für dieses Projekt');
        }

        $timeTrackingService->startTimeTracking($project);

        if($from == 'timetrackinglist') {
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
    public function update(Request $request, ManagerRegistry $doctrine): RedirectResponse
    {
        $timeTrackingId = $request->get('time_tracking_id');
        $timeTrackingId = new Uuid($timeTrackingId);

        $starttime = $request->get('starttime');
        $endtime = $request->get('endtime');
        $useOnInvoice = (int)$request->get('use_on_invoice');
        $invoiceId = (int)$request->get('invoice_id');
        $comment = $request->get('comment');

        // Eintrag laden.
        $timeTrackingService = new TimeTrackingService($doctrine);
        $timeTracking = $timeTrackingService->loadById($timeTrackingId);

        if(null === $timeTracking) {
            die('Der Eintrag wurde nicht gefunden.');
        }

        $timeTracking->setStarttime(new \DateTime(date('Y-m-d H:i:s', strtotime($starttime))));
        
        if(strlen(trim($endtime)) > 0) {
            $timeTracking->setEndtime(new \DateTime(date('Y-m-d H:i:s', strtotime($endtime))));
        } else {
            $timeTracking->setEndtime(null);
        }
        
        if($useOnInvoice === 1) {
            $timeTracking->setUseOnInvoice(true);
        } else {
            $timeTracking->setUseOnInvoice(false);
        }

        if(0 === $invoiceId) {
            $timeTracking->setInvoiceId(null);
        } else {
            $timeTracking->setInvoiceId($invoiceId);
        }
        
        $timeTracking->setComment($comment);

        // Eintrag aktualisieren
        $timeTrackingService->update($timeTracking);
        
        return $this->redirectToRoute(
            'app_time_tracking.list.project.times',
            [ 'project_id' => $timeTracking->getProject()->getId() ]
        );
    }
}
