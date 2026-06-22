<?php

require_once __DIR__ . '/Router.php';

function registerRoutes(Router $router): void
{
    $sessions = new SessionsApiController();
    $users = new UsersApiController();
    $menu = new MenuApiController();
    $roles = new RolesApiController();
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

    $router->post('/sessions', fn() => $sessions->store());
    $router->delete('/sessions', fn() => $sessions->destroy());

    $router->get('/users/me', fn() => $users->me());
    $router->get('/users', fn() => $users->index());
    $router->post('/users', fn() => $users->store());
    $router->put('/users/{id}', fn($p) => $users->update($p));
    $router->delete('/users/{id}', fn($p) => $users->delete($p));

    $router->get('/roles', fn() => $roles->index());
    $router->get('/menu', fn() => $menu->index());

    $router->get('/dashboard', fn() => $dashboard->index());

    $router->get('/members', fn() => $members->index());
    $router->post('/members/imports', fn() => $members->import());
    $router->post('/members/imports/file', fn() => $members->importFile());
    $router->get('/members/{id}', fn($p) => $members->show($p));
    $router->get('/members/{id}/prizes', fn($p) => $members->prizes($p));
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
    $router->post('/teams/{id}/members', fn($p) => $teams->addMember($p));
    $router->delete('/teams/{team_id}/members/{member_id}', fn($p) => $teams->removeMember($p));
    $router->get('/teams/{id}/results', fn($p) => $teams->results($p));
    $router->post('/teams/{id}/results', fn($p) => $teams->addResult($p));
    $router->delete('/teams/{team_id}/results/{result_id}', fn($p) => $teams->deleteResult($p));

    $router->get('/groups', fn() => $groups->index());
    $router->get('/groups/{id}', fn($p) => $groups->show($p));
    $router->post('/groups', fn() => $groups->store());
    $router->put('/groups/{id}', fn($p) => $groups->update($p));
    $router->delete('/groups/{id}', fn($p) => $groups->delete($p));
    $router->get('/groups/{id}/members', fn($p) => $groups->members($p));
    $router->post('/groups/{id}/members', fn($p) => $groups->addMember($p));
    $router->delete('/groups/{group_id}/members/{member_id}', fn($p) => $groups->removeMember($p));

    $router->get('/halls', fn() => $halls->index());
    $router->get('/halls/{id}', fn($p) => $halls->show($p));
    $router->post('/halls', fn() => $halls->store());
    $router->put('/halls/{id}', fn($p) => $halls->update($p));
    $router->delete('/halls/{id}', fn($p) => $halls->delete($p));
    $router->get('/halls/{id}/slots', fn($p) => $halls->slots($p));
    $router->post('/halls/{id}/slots', fn($p) => $halls->addSlot($p));
    $router->delete('/halls/{hall_id}/slots/{slot_id}', fn($p) => $halls->deleteSlot($p));

    $router->get('/activities', fn() => $activities->index());
    $router->get('/activities/{id}', fn($p) => $activities->show($p));
    $router->post('/activities', fn() => $activities->store());
    $router->put('/activities/{id}', fn($p) => $activities->update($p));
    $router->delete('/activities/{id}', fn($p) => $activities->delete($p));

    $router->get('/competitions', fn() => $competitions->index());
    $router->get('/competitions/{id}', fn($p) => $competitions->show($p));
    $router->post('/competitions', fn() => $competitions->store());
    $router->put('/competitions/{id}', fn($p) => $competitions->update($p));
    $router->delete('/competitions/{id}', fn($p) => $competitions->delete($p));
    $router->get('/competitions/{id}/report', fn($p) => $competitions->report($p));
    $router->get('/competitions/{id}/prizes', fn($p) => $competitions->prizes($p));

    $router->get('/participations', fn() => $participations->index());
    $router->get('/participations/{id}', fn($p) => $participations->show($p));
    $router->post('/participations', fn() => $participations->store());
    $router->put('/participations/{id}', fn($p) => $participations->update($p));
    $router->delete('/participations/{id}', fn($p) => $participations->delete($p));

    $router->get('/rankings', fn() => $rankings->index());

    $router->get('/prizes', fn() => $prizes->index());
    $router->get('/prizes/{id}', fn($p) => $prizes->show($p));
    $router->post('/prizes', fn() => $prizes->store());
    $router->put('/prizes/{id}', fn($p) => $prizes->update($p));
    $router->delete('/prizes/{id}', fn($p) => $prizes->delete($p));

    $router->get('/trips', fn() => $trips->index());
    $router->get('/trips/{id}', fn($p) => $trips->show($p));
    $router->post('/trips', fn() => $trips->store());
    $router->put('/trips/{id}', fn($p) => $trips->update($p));
    $router->delete('/trips/{id}', fn($p) => $trips->delete($p));
    $router->get('/trips/{id}/members', fn($p) => $trips->members($p));
    $router->post('/trips/{id}/members', fn($p) => $trips->addMember($p));
    $router->delete('/trips/{trip_id}/members/{member_id}', fn($p) => $trips->removeMember($p));
    $router->get('/trips/{id}/report', fn($p) => $trips->report($p));

    $router->get('/expenses', fn() => $expenses->index());
    $router->get('/expenses/{id}', fn($p) => $expenses->show($p));
    $router->post('/expenses', fn() => $expenses->store());
    $router->put('/expenses/{id}', fn($p) => $expenses->update($p));
    $router->delete('/expenses/{id}', fn($p) => $expenses->delete($p));

    $router->get('/reimbursements', fn() => $reimbursements->index());
    $router->get('/reimbursements/{id}', fn($p) => $reimbursements->show($p));
    $router->get('/reimbursements/{id}/export', fn($p) => $reimbursements->export($p));
}
