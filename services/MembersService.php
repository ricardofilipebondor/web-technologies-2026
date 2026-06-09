<?php

class MembersService
{
    public function list(): array
    {
        return Member::all();
    }

    public function profile(int $id): ?array
    {
        $member = Member::findWithCoach($id);
        if (!$member) {
            return null;
        }
        return [
            'member' => $member,
            'participations' => Participation::byMember($id),
            'prizes' => Prize::byMember($id),
            'groups' => Member::getGroups($id),
        ];
    }
}
