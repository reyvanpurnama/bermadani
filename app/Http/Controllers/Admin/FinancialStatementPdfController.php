<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Domains\Accounting\Services\FinancialStatementService;

class FinancialStatementPdfController extends Controller
{
    public function download(Request $request)
    {
        $year = (int) ($request->query('year') ?? now()->year);
        $service = new FinancialStatementService();

        $data = [
            'year' => $year,
            'neraca' => $service->getBalanceSheet($year),
            'shu' => $service->getIncomeStatement($year),
            'equity' => $service->getEquityChanges($year),
            'cashflow' => $service->getCashFlowStatement($year),
            'kopBase64' => $this->getKopBase64(),
        ];

        $pdf = Pdf::loadView('pdf.financial-statements', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("laporan-keuangan-{$year}.pdf");
    }

    public function downloadCalk(Request $request)
    {
        $year = (int) ($request->query('year') ?? now()->year - 1);

        $pdf = Pdf::loadView('pdf.calk', ['year' => $year])
            ->setPaper('a4', 'portrait');

        return $pdf->download("calk-{$year}.pdf");
    }

    private function getKopBase64(): string
    {
        $kopPath = public_path('images/Kop.png');
        if (file_exists($kopPath)) {
            $imageData = base64_encode(file_get_contents($kopPath));
            return 'data:image/png;base64,' . $imageData;
        }
        return '';
    }
}
