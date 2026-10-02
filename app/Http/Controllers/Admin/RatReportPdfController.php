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
            ->sortBy(function ($dist) {
                return strtolower($dist->member?->name ?? 'zzz');
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

        $filename = "Laporan_Lengkap_SHU_RAT_{$session->year}_" . config('cooperative.short_name') . ".pdf";

        return $pdf->download($filename);
    }

    public function downloadBeritaAcaraPdf(Request $request, RatSession $session)
    {
        $totalAnggota = \App\Models\Member::where('status', 'ACTIVE')->count();

        // Load Kop.png logo image as base64 for DomPDF compatibility
        $kopPath = public_path(config('cooperative.kop_surat_path'));

        $kopBase64 = null;
        if (file_exists($kopPath)) {
            $kopBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($kopPath));
        }

        $pdf = Pdf::loadView('pdf.berita-acara-rat', [
            'session' => $session,
            'kopBase64' => $kopBase64,
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
            'catatanRekomendasi' => $request->input('catatan_rekomendasi'),
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'portrait');

        $filename = "Berita_Acara_RAT_{$session->year}_" . config('cooperative.short_name') . ".pdf";

        return $pdf->download($filename);
    }

    public function downloadSlipPdf(Request $request, MemberShuDistribution $distribution)
    {
        $distribution->loadMissing(['member', 'ratSession']);
        $member = $distribution->member;
        $session = $distribution->ratSession;

        // Load Kop.png logo image as base64 for DomPDF compatibility
        $kopPath = public_path(config('cooperative.kop_surat_path', 'images/Kop.png'));
        $kopBase64 = null;
        if (file_exists($kopPath)) {
            $kopBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($kopPath));
        }

        // Load Bendahara digital signature if configured
        $sigSetting = coop_setting('bendahara_signature', '');
        $sigBase64 = null;
        if (!empty($sigSetting) && file_exists(public_path($sigSetting))) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($sigSetting)));
        }

        $rcptPokok = (float) ($member?->simpananPokok ?? $distribution->simpanan_pokok_snapshot ?? 0);
        $rcptWajib = (float) ($member?->simpananWajib ?? $distribution->simpanan_wajib_snapshot ?? 0);
        $rcptSukarela = (float) ($member?->simpananSukarela ?? 0);
        $rcptTotalSimpanan = $rcptPokok + $rcptWajib;
        $rcptShu = (float) $distribution->shu_amount;
        $rcptTotalPencairan = $rcptTotalSimpanan + $rcptShu;

        $type = $request->query('type', 'member'); // 'member' | 'coop' | 'dual'

        $viewData = [
            'distribution' => $distribution,
            'member' => $member,
            'session' => $session,
            'kopBase64' => $kopBase64,
            'sigBase64' => $sigBase64,
            'rcptPokok' => $rcptPokok,
            'rcptWajib' => $rcptWajib,
            'rcptSukarela' => $rcptSukarela,
            'rcptTotalSimpanan' => $rcptTotalSimpanan,
            'rcptShu' => $rcptShu,
            'rcptTotalPencairan' => $rcptTotalPencairan,
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
            'slipType' => $type,
        ];

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $member?->name ?? 'Anggota');
        $nomorAnggota = $member?->nomorAnggota ?? $distribution->id;
        $year = $session?->year ?? date('Y');

        if ($type === 'dual') {
            $pdf = Pdf::loadView('pdf.slip-shu-dual', $viewData)->setPaper('a4', 'portrait');
            $filename = "Slip_Pencairan_2Rangkap_A4_{$year}_{$nomorAnggota}_{$safeName}.pdf";
        } elseif ($type === 'coop') {
            $pdf = Pdf::loadView('pdf.slip-shu', $viewData)->setPaper('a5', 'landscape');
            $filename = "Slip_Pencairan_Arsip_Koperasi_{$year}_{$nomorAnggota}_{$safeName}.pdf";
        } else {
            $pdf = Pdf::loadView('pdf.slip-shu', $viewData)->setPaper('a5', 'landscape');
            $filename = "Slip_Pencairan_SHU_dan_Simpanan_{$year}_{$nomorAnggota}_{$safeName}.pdf";
        }

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
