<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RatReportPdfController extends Controller
{
    public function downloadPdf(RatSession $session)
    {
        $distributions = MemberShuDistribution::with('member')
            ->where('rat_session_id', $session->id)
            ->get()
            ->sortBy(function ($dist) {
                return $dist->member?->name ?? 'ZZZ';
            })
            ->values();

        $totalSimpananPool = $distributions->sum('total_simpanan_amount');
        $totalJasaSimpananPool = $distributions->sum('jasa_simpanan_amount');
        $totalJasaUsahaPool = $distributions->sum('jasa_usaha_amount');
        $totalShuPool = $distributions->sum('shu_amount');
        $disbursedCount = $distributions->where('is_disbursed', true)->count();

        $pdf = Pdf::loadView('pdf.rat-shu-report', [
            'session' => $session,
            'distributions' => $distributions,
            'totalSimpananPool' => $totalSimpananPool,
            'totalJasaSimpananPool' => $totalJasaSimpananPool,
            'totalJasaUsahaPool' => $totalJasaUsahaPool,
            'totalShuPool' => $totalShuPool,
            'disbursedCount' => $disbursedCount,
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'landscape');

        $filename = "Laporan_Lengkap_SHU_RAT_{$session->year}_Koperasi_Bermadani.pdf";

        return $pdf->download($filename);
    }
}
