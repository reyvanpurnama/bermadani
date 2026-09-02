<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Koperasi\Models\Loan;
use App\Domains\Koperasi\Models\Member;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MemberSettlementPdfController extends Controller
{
    public function downloadPdf($memberId)
    {
        $member = Member::with(['settlement', 'settlement.settledBy'])->findOrFail($memberId);
        $settlement = $member->settlement;

        $kopPath = public_path(coop_config('kop_surat_path'));
        $kopBase64 = '';
        if (file_exists($kopPath)) {
            $kopData = file_get_contents($kopPath);
            $type = pathinfo($kopPath, PATHINFO_EXTENSION);
            $kopBase64 = 'data:image/' . $type . ';base64,' . base64_encode($kopData);
        }

        $activeLoans = Loan::where('member_id', $member->id)
            ->whereIn('status', ['ACTIVE', 'APPROVED', 'OVERDUE'])
            ->get();

        $pdf = Pdf::loadView('admin.reports.member-settlement-pdf', [
            'member' => $member,
            'settlement' => $settlement,
            'activeLoans' => $activeLoans,
            'kopBase64' => $kopBase64,
        ])->setPaper('a4', 'portrait');

        $shortName = str_replace(' ', '_', coop_config('short_name'));
        $filename = "Berita_Acara_Pengembalian_Simpanan_{$shortName}_{$member->nomorAnggota}.pdf";

        return $pdf->stream($filename);
    }
}
