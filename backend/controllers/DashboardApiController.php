<?php

class DashboardApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('dashboard');
        Response::resource(array_merge([
            'memberCount' => Member::count(),
            'coachCount' => Coach::count(),
            'competitionCount' => Competition::count(),
            'activityCount' => Activity::count(),
            'tripCount' => Trip::count(),
            'recentCompetitions' => Competition::getRecent(5),
            'recentActivities' => Activity::getRecent(5),
            'recentPrizes' => Prize::getRecent(5),
        ], ['_links' => Hateoas::links(['self' => '/dashboard'])]));
    }
}
