<?php

class ReimbursementController
{
    public function index(): void
    {
        $this->checkAccess();
        render('reimbursements/index', ['trips' => Trip::all()]);
    }

    public function show(): void
    {
        $this->checkAccess();
        $tripId = (int) get('id');
        $trip = Trip::find($tripId);
        if (!$trip) {
            redirect('reimbursements/index');
        }
        $expenses = Expense::byTrip($tripId);
        $total = Trip::getTotalExpenses($tripId);
        render('reimbursements/show', [
            'trip' => $trip,
            'members' => Trip::getMembers($tripId),
            'expenses' => $expenses,
            'total' => $total,
        ]);
    }

    public function export(): void
    {
        $this->checkAccess();
        $tripId = (int) get('id');
        $format = get('format', 'csv');
        $trip = Trip::find($tripId);
        $expenses = Expense::byTrip($tripId);
        $total = Trip::getTotalExpenses($tripId);

        $tripMembers = Trip::getMembers($tripId);

        if ($format === 'pdf') {
            $lines = [
                'Destinatie: ' . $trip['destinatie'],
                'Echipa: ' . ($trip['team_nume'] ?? '-'),
                'Plecare: ' . $trip['data_plecare'],
                'Intoarcere: ' . $trip['data_intoarcere'],
                'Scop: ' . ($trip['scop'] ?? '-'),
                '',
                'MEMBRI ECHIPA:',
            ];
            foreach ($tripMembers as $m) {
                $lines[] = '- ' . $m['nume'] . ' ' . $m['prenume'];
            }
            $lines[] = '';
            $lines[] = 'CHELTUIELI:';
            foreach ($expenses as $e) {
                $lines[] = sprintf('%s - %.2f RON - %s', $e['tip'], $e['suma'], $e['observatii'] ?? '');
            }
            $lines[] = '';
            $lines[] = sprintf('TOTAL GENERAL: %.2f RON', $total);
            PdfExporter::generateReport('Raport Decont - ' . $trip['destinatie'], $lines);
        }

        $rows = array_map(fn($e) => [$e['tip'], $e['suma'], $e['observatii'] ?? ''], $expenses);
        $rows[] = ['TOTAL', $total, ''];
        DataExporter::toCsv($rows, ['tip', 'suma', 'observatii'], 'decont_' . $tripId . '.csv');
    }

    private function checkAccess(): void
    {
        requireLogin();
        if (!userCanAccess('reimbursements')) {
            setFlash('danger', 'Nu aveti acces.');
            redirect('dashboard/index');
        }
    }
}
