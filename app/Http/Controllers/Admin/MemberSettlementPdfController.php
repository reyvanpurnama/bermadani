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

    public function downloadMySettlementPdf()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $member = Member::where('userId', $user->id)->firstOrFail();
        return $this->downloadPdf($member->id);
    }

    public function exportSummaryPdf(Request $request)
    {
        $statusFilter = $request->query('status', 'ALL');
        $search = $request->query('search', '');

        $query = Member::query()
            ->whereIn('status', ['INACTIVE', 'SUSPENDED'])
            ->with(['settlement', 'settlement.settledBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nomorAnggota', 'like', "%{$search}%")
                  ->orWhere('unitKerja', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'PENDING') {
            $query->where(function ($q) {
                $q->whereDoesntHave('settlement')
                  ->orWhereHas('settlement', fn($sub) => $sub->where('status', 'PENDING'));
            });
        } elseif ($statusFilter === 'SETTLED') {
            $query->whereHas('settlement', fn($sub) => $sub->where('status', 'SETTLED'));
        }

        $members = $query->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->get();

        $kopPath = public_path(coop_config('kop_surat_path'));
        $kopBase64 = '';
        if (file_exists($kopPath)) {
            $kopData = file_get_contents($kopPath);
            $type = pathinfo($kopPath, PATHINFO_EXTENSION);
            $kopBase64 = 'data:image/' . $type . ';base64,' . base64_encode($kopData);
        }

        $pdf = Pdf::loadView('admin.reports.resigned-members-summary-pdf', [
            'members' => $members,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'kopBase64' => $kopBase64,
        ])->setPaper('a4', 'landscape');

        $shortName = str_replace(' ', '_', coop_config('short_name'));
        $dateStr = date('Ymd_His');
        $filename = "Rekap_Pengembalian_Simpanan_Anggota_Keluar_{$shortName}_{$dateStr}.pdf";

        return $pdf->stream($filename);
    }
}
