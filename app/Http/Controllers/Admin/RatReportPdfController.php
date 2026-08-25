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
            ->where('shu_amount', '>', 0)
            ->get()
            ->filter(function ($dist) {
                return $dist->member && $dist->member->status === 'ACTIVE' && $dist->member->isMemberKoperasi;
            })
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

    public function downloadBeritaAcaraPdf(Request $request, RatSession $session)
    {
        $totalAnggota = \App\Models\Member::where('status', 'ACTIVE')->count();

        $pdf = Pdf::loadView('pdf.berita-acara-rat', [
            'session' => $session,
            'nomorSurat' => $request->input('nomor_surat'),
            'hariTanggal' => $request->input('hari_tanggal'),
            'jam' => $request->input('jam'),
            'tempat' => $request->input('tempat'),
            'totalAnggota' => $request->input('total_anggota', $totalAnggota),
            'anggotaHadir' => $request->input('anggota_hadir', $totalAnggota),
            'pengurusHadir' => $request->input('pengurus_hadir', 3),
            'pengawasHadir' => $request->input('pengawas_hadir', 1),
            'tamuHadir' => $request->input('tamu_hadir', 0),
            'ketuaSidang' => $request->input('ketua_sidang'),
            'sekretarisSidang' => $request->input('sekretaris_sidang'),
            'ketuaKoperasi' => $request->input('ketua_koperasi'),
            'sekretarisKoperasi' => $request->input('sekretaris_koperasi'),
            'bendaharaKoperasi' => $request->input('bendahara_koperasi'),
            'ketuaPengawas' => $request->input('ketua_pengawas'),
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'portrait');

        $filename = "Berita_Acara_RAT_{$session->year}_Koperasi_Bermadani.pdf";

        return $pdf->download($filename);
    }
}
