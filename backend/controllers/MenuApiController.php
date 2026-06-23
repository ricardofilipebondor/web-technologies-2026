<?php

class MenuApiController
{
    public function index(): void
    {
        AuthMiddleware::requireLogin();
        $items = [];
        foreach (getMenuItems() as $item) {
            if (userCanAccess($item['module'])) {
                $items[] = Hateoas::item($item, '/menu/' . $item['module']);
            }
        }
        Response::resource([
            'items' => $items,
            '_links' => Hateoas::links(['self' => '/menu']),
        ]);
    }
}

class RolesApiController
{
    public function index(): void
    {
        AuthMiddleware::requireModule('admin');
        RestHelper::index('admin', '/roles', User::getRoles());
    }
}
