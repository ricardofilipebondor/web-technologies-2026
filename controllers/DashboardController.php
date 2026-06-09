<?php

class DashboardController
{
    public function index(): void
    {
        requireLogin();
        if (!userCanAccess('dashboard')) {
            setFlash('danger', 'Nu aveti acces la acest modul.');
            redirect('auth/login');
        }

        $data = [
            'memberCount'     => Member::count(),
            'coachCount'      => Coach::count(),
            'competitionCount'=> Competition::count(),
            'activityCount'   => Activity::count(),
            'tripCount'       => Trip::count(),
            'recentCompetitions' => Competition::getRecent(5),
            'recentActivities'   => Activity::getRecent(5),
            'recentPrizes'       => Prize::getRecent(5),
        ];

        render('dashboard/index', $data);
    }
}
