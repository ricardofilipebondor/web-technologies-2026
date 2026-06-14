<?php

require_once __DIR__ . '/Router.php';

function registerRoutes(Router $router): void
{
    $auth = new AuthApiController();
    $dashboard = new DashboardApiController();
    $members = new MembersApiController();
    $coaches = new CoachesApiController();
    $teams = new TeamsApiController();
    $groups = new GroupsApiController();
    $halls = new HallsApiController();
    $activities = new ActivitiesApiController();
    $competitions = new CompetitionsApiController();
    $participations = new ParticipationsApiController();
    $rankings = new RankingsApiController();
    $prizes = new PrizesApiController();
    $trips = new TripsApiController();
    $expenses = new ExpensesApiController();
    $reimbursements = new ReimbursementsApiController();
    $admin = new AdminApiController();

    $router->post('/auth/login', fn() => $auth->login());
    $router->post('/auth/register', fn() => $auth->register());
    $router->post('/auth/logout', fn() => $auth->logout());
    $router->get('/auth/me', fn() => $auth->me());
    $router->get('/auth/menu', fn() => $auth->menu());

    $router->get('/dashboard', fn() => $dashboard->index());

    $router->get('/members', fn() => $members->index());
    $router->get('/members/coaches', fn() => $members->coaches());
    $router->get('/members/export', fn() => $members->export());
    $router->post('/members/import', fn() => $members->import());
    $router->post('/members/import-file', fn() => $members->importFile());
    $router->get('/members/{id}', fn($p) => $members->show($p));
    $router->post('/members', fn() => $members->store());
    $router->put('/members/{id}', fn($p) => $members->update($p));
    $router->delete('/members/{id}', fn($p) => $members->delete($p));

    $router->get('/coaches', fn() => $coaches->index());
    $router->get('/coaches/{id}', fn($p) => $coaches->show($p));
    $router->post('/coaches', fn() => $coaches->store());
    $router->put('/coaches/{id}', fn($p) => $coaches->update($p));
    $router->delete('/coaches/{id}', fn($p) => $coaches->delete($p));

    $router->get('/teams', fn() => $teams->index());
    $router->get('/teams/{id}', fn($p) => $teams->show($p));
    $router->post('/teams', fn() => $teams->store());
    $router->put('/teams/{id}', fn($p) => $teams->update($p));
    $router->delete('/teams/{id}', fn($p) => $teams->delete($p));
    $router->get('/teams/{id}/members', fn($p) => $teams->members($p));
    $router->post('/teams/members', fn() => $teams->addMember());
    $router->delete('/teams/{team_id}/members/{member_id}', fn($p) => $teams->removeMember($p));
    $router->get('/teams/{id}/results', fn($p) => $teams->results($p));
    $router->post('/teams/results', fn() => $teams->addResult());
    $router->delete('/teams/results/{result_id}', fn($p) => $teams->deleteResult($p));

    $router->get('/groups', fn() => $groups->index());
    $router->get('/groups/coaches', fn() => $groups->coaches());
    $router->post('/groups', fn() => $groups->store());
    $router->put('/groups/{id}', fn($p) => $groups->update($p));
    $router->delete('/groups/{id}', fn($p) => $groups->delete($p));
    $router->get('/groups/{id}/members', fn($p) => $groups->members($p));
    $router->post('/groups/members', fn() => $groups->addMember());
    $router->delete('/groups/{group_id}/members/{member_id}', fn($p) => $groups->removeMember($p));

    $router->get('/halls', fn() => $halls->index());
    $router->post('/halls', fn() => $halls->store());
    $router->put('/halls/{id}', fn($p) => $halls->update($p));
    $router->delete('/halls/{id}', fn($p) => $halls->delete($p));
    $router->get('/halls/{id}/slots', fn($p) => $halls->slots($p));
    $router->post('/halls/slots', fn() => $halls->addSlot());
    $router->delete('/halls/slots/{slot_id}', fn($p) => $halls->deleteSlot($p));

    $router->get('/activities', fn() => $activities->index());
    $router->get('/activities/meta', fn() => $activities->meta());
    $router->post('/activities', fn() => $activities->store());
    $router->put('/activities/{id}', fn($p) => $activities->update($p));
    $router->delete('/activities/{id}', fn($p) => $activities->delete($p));

    $router->get('/competitions', fn() => $competitions->index());
    $router->post('/competitions', fn() => $competitions->store());
    $router->put('/competitions/{id}', fn($p) => $competitions->update($p));
    $router->delete('/competitions/{id}', fn($p) => $competitions->delete($p));
    $router->get('/competitions/{id}/report', fn($p) => $competitions->report($p));

    $router->get('/participations', fn() => $participations->index());
    $router->get('/participations/meta', fn() => $participations->meta());
    $router->post('/participations', fn() => $participations->store());
    $router->put('/participations/{id}', fn($p) => $participations->update($p));
    $router->delete('/participations/{id}', fn($p) => $participations->delete($p));
    $router->get('/participations/report', fn() => $participations->report());
    $router->get('/participations/export', fn() => $participations->exportReport());

    $router->get('/rankings', fn() => $rankings->index());
    $router->get('/rankings/export', fn() => $rankings->export());

    $router->get('/prizes', fn() => $prizes->index());
    $router->get('/prizes/meta', fn() => $prizes->meta());
    $router->post('/prizes', fn() => $prizes->store());
    $router->put('/prizes/{id}', fn($p) => $prizes->update($p));
    $router->delete('/prizes/{id}', fn($p) => $prizes->delete($p));
    $router->get('/prizes/member/{member_id}', fn($p) => $prizes->byMember($p));
    $router->get('/prizes/competition/{competition_id}', fn($p) => $prizes->byCompetition($p));

    $router->get('/trips', fn() => $trips->index());
    $router->get('/trips/meta', fn() => $trips->meta());
    $router->post('/trips', fn() => $trips->store());
    $router->put('/trips/{id}', fn($p) => $trips->update($p));
    $router->delete('/trips/{id}', fn($p) => $trips->delete($p));
    $router->get('/trips/{id}/members', fn($p) => $trips->members($p));
    $router->post('/trips/members', fn() => $trips->addMember());
    $router->delete('/trips/{trip_id}/members/{member_id}', fn($p) => $trips->removeMember($p));
    $router->get('/trips/{id}/report', fn($p) => $trips->report($p));

    $router->get('/expenses', fn() => $expenses->index());
    $router->get('/expenses/meta', fn() => $expenses->meta());
    $router->post('/expenses', fn() => $expenses->store());
    $router->put('/expenses/{id}', fn($p) => $expenses->update($p));
    $router->delete('/expenses/{id}', fn($p) => $expenses->delete($p));

    $router->get('/reimbursements', fn() => $reimbursements->index());
    $router->get('/reimbursements/{id}', fn($p) => $reimbursements->show($p));
    $router->get('/reimbursements/{id}/export', fn($p) => $reimbursements->export($p));

    $router->get('/admin', fn() => $admin->index());
    $router->post('/admin/users', fn() => $admin->store());
    $router->put('/admin/users/{id}/role', fn($p) => $admin->updateRole($p));
    $router->delete('/admin/users/{id}', fn($p) => $admin->delete($p));
}
