<?php

class TripsService
{
    public function list(): array
    {
        return Trip::all();
    }

    public function report(int $tripId): ?array
    {
        $trip = Trip::find($tripId);
        if (!$trip) {
            return null;
        }
        return [
            'trip' => $trip,
            'members' => Trip::getMembers($tripId),
            'expenses' => Expense::byTrip($tripId),
            'total' => Trip::getTotalExpenses($tripId),
        ];
    }
}
