<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string $module
 * @property string $description
 * @property string $loggable_type
 * @property int $loggable_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $action_color
 * @property-read mixed $module_icon
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $loggable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byAction($action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byModule($module)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereLoggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereLoggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property numeric $opening_cash
 * @property \Illuminate\Support\Carbon $check_in_at
 * @property \Illuminate\Support\Carbon|null $check_out_at
 * @property numeric|null $closing_cash
 * @property numeric|null $expected_cash
 * @property numeric|null $difference
 * @property int $total_transactions
 * @property numeric $total_sales
 * @property numeric $total_cash_sales
 * @property numeric $total_non_cash_sales
 * @property string|null $note
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $duration
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereCheckInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereCheckOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereClosingCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereExpectedCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereOpeningCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereTotalCashSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereTotalNonCashSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereTotalSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereTotalTransactions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashierShift whereUserId($value)
 */
	class CashierShift extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $icon
 * @property int $order
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $batchCode
 * @property int $supplierId
 * @property string $status
 * @property numeric $totalValue
 * @property numeric $totalSold
 * @property numeric $payableAmount
 * @property \Illuminate\Support\Carbon|null $receivedAt
 * @property \Illuminate\Support\Carbon|null $settledAt
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $margin
 * @property-read mixed $sold_percent
 * @property-read mixed $total_initial_qty
 * @property-read mixed $total_sold_qty
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConsignmentItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Supplier $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereBatchCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch wherePayableAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereSettledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereTotalSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentBatch whereUpdatedAt($value)
 */
	class ConsignmentBatch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $batchId
 * @property int $productId
 * @property int $initialQty
 * @property int $damagedQty Jumlah rusak/hilang/tidak layak jual
 * @property int $returnedQty Jumlah barang yang diretur ke supplier
 * @property int $soldQty
 * @property int $remainingQty
 * @property numeric $sellPrice
 * @property numeric $supplierPrice Harga dari supplier (buyPrice)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ConsignmentBatch $batch
 * @property-read mixed $margin
 * @property-read mixed $payable_amount
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereDamagedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereInitialQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereRemainingQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereReturnedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereSellPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereSoldQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereSupplierPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsignmentItem whereUpdatedAt($value)
 */
	class ConsignmentItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $category
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $transactionDate
 * @property string|null $description
 * @property string|null $proofFile
 * @property int $userId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction byDateRange($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction expense()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction income()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction thisYear()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereProofFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction whereUserId($value)
 */
	class FinancialTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $member_id
 * @property numeric $amount
 * @property numeric $interestRate
 * @property int $tenor
 * @property numeric $monthlyPayment
 * @property numeric $remainingAmount
 * @property string $status
 * @property string $loanSource
 * @property string|null $purpose
 * @property \Illuminate\Support\Carbon|null $approvedAt
 * @property string|null $approvedBy
 * @property \Illuminate\Support\Carbon $startDate
 * @property \Illuminate\Support\Carbon $endDate
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanPayment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereMonthlyPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereRemainingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereTenor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUpdatedAt($value)
 */
	class Loan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $loanId
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $paymentDate
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPayment whereUpdatedAt($value)
 */
	class LoanPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $userId
 * @property string $nomorAnggota
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string $gender
 * @property string $unitKerja
 * @property \Illuminate\Support\Carbon $joinDate
 * @property string $status
 * @property bool $isMemberKoperasi
 * @property numeric $simpananPokok
 * @property numeric $simpananWajib
 * @property numeric $monthly_simpanan_wajib
 * @property string $simwa_payment_method
 * @property string $sukarela_payment_method
 * @property numeric $monthly_sukarela_amount
 * @property \Illuminate\Support\Carbon|null $salary_deduction_consent_date
 * @property numeric $simpananSukarela
 * @property numeric $monthly_wajib_amount
 * @property string|null $last_wajib_debit_date
 * @property int $points
 * @property string $tier
 * @property numeric $totalSpent
 * @property \Illuminate\Support\Carbon|null $lastPurchase
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $next_tier_progress
 * @property-read mixed $points_to_next_tier
 * @property-read mixed $status_badge
 * @property-read mixed $tier_badge
 * @property-read float $total_salary_deduction
 * @property-read mixed $total_simpanan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemberPointsHistory> $pointsHistory
 * @property-read int|null $points_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Saving> $savings
 * @property-read int|null $savings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SimpananTransaction> $simpananTransactions
 * @property-read int|null $simpanan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member byTier($tier)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member byUnit($unit)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member koperasiMembers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereIsMemberKoperasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereJoinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereLastPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereLastWajibDebitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereMonthlySimpananWajib($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereMonthlySukarelaAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereMonthlyWajibAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereNomorAnggota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSalaryDeductionConsentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSimpananPokok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSimpananSukarela($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSimpananWajib($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSimwaPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereSukarelaPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereTotalSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUnitKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUserId($value)
 */
	class Member extends \Eloquent {}
}

namespace App\Models{
/**
 * MemberKoperasi Model
 * 
 * Alias untuk Member model dengan nama yang lebih jelas.
 * Table: members (untuk Member Koperasi dengan simpanan)
 * 
 * Auto-create Member Minimarket ketika Member Koperasi dibuat.
 *
 * @property int $id
 * @property int $userId
 * @property string $nomorAnggota
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string $gender
 * @property string $unitKerja
 * @property \Illuminate\Support\Carbon $joinDate
 * @property string $status
 * @property bool $isMemberKoperasi
 * @property numeric $simpananPokok
 * @property numeric $simpananWajib
 * @property numeric $monthly_simpanan_wajib
 * @property string $simwa_payment_method
 * @property string $sukarela_payment_method
 * @property numeric $monthly_sukarela_amount
 * @property \Illuminate\Support\Carbon|null $salary_deduction_consent_date
 * @property numeric $simpananSukarela
 * @property numeric $monthly_wajib_amount
 * @property string|null $last_wajib_debit_date
 * @property int $points
 * @property string $tier
 * @property numeric $totalSpent
 * @property \Illuminate\Support\Carbon|null $lastPurchase
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $next_tier_progress
 * @property-read mixed $points_to_next_tier
 * @property-read mixed $status_badge
 * @property-read mixed $tier_badge
 * @property-read float $total_salary_deduction
 * @property-read mixed $total_simpanan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \App\Models\MemberMinimarket|null $memberMinimarket
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemberPointsHistory> $pointsHistory
 * @property-read int|null $points_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Saving> $savings
 * @property-read int|null $savings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SimpananTransaction> $simpananTransactions
 * @property-read int|null $simpanan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi byTier($tier)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi byUnit($unit)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi koperasiMembers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereIsMemberKoperasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereJoinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereLastPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereLastWajibDebitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereMonthlySimpananWajib($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereMonthlySukarelaAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereMonthlyWajibAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereNomorAnggota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSalaryDeductionConsentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSimpananPokok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSimpananSukarela($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSimpananWajib($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSimwaPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereSukarelaPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereTotalSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereUnitKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberKoperasi whereUserId($value)
 */
	class MemberKoperasi extends \Eloquent {}
}

namespace App\Models{
/**
 * MemberMinimarket Model
 * 
 * Table: member_minimarket
 * Untuk loyalty program minimarket (customer biasa)
 *
 * @property int $id
 * @property int $userId
 * @property string $memberNumber
 * @property string|null $cardNumber
 * @property int $points
 * @property numeric $totalSpent
 * @property \Illuminate\Support\Carbon|null $lastVisit
 * @property string $status
 * @property int|null $registeredBy
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $tier
 * @property-read \App\Models\Member|null $memberKoperasi
 * @property-read \App\Models\User|null $registrar
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereLastVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereMemberNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereRegisteredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereTotalSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberMinimarket whereUserId($value)
 */
	class MemberMinimarket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $memberId
 * @property int|null $transactionId
 * @property string $type
 * @property int $points
 * @property int $balance
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $expiresAt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @property-read \App\Models\Transaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory earned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory redeemed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberPointsHistory whereUpdatedAt($value)
 */
	class MemberPointsHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property int $categoryId
 * @property string|null $sku
 * @property numeric|null $buyPrice
 * @property numeric $sellPrice
 * @property int $stock
 * @property int $threshold
 * @property string $unit
 * @property string $ownershipType
 * @property string $status
 * @property string $approvalStatus
 * @property string|null $rejectionReason
 * @property \Illuminate\Support\Carbon|null $approvedAt
 * @property int|null $approvedBy
 * @property bool $isConsignment
 * @property bool $isActive
 * @property int $isDraft
 * @property int|null $supplierId
 * @property string|null $supplierContact
 * @property numeric $profitShareRate
 * @property string $stockCycle
 * @property numeric|null $avgCost
 * @property string|null $expiryPolicy
 * @property \Illuminate\Support\Carbon|null $lastRestockAt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConsignmentItem> $consignmentItems
 * @property-read int|null $consignment_items_count
 * @property-read mixed $gross_margin
 * @property-read mixed $gross_profit
 * @property-read mixed $gross_profit_margin
 * @property-read mixed $margin
 * @property-read mixed $margin_percentage
 * @property-read mixed $markup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RestockRequest> $restockRequests
 * @property-read int|null $restock_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionItem> $transactionItems
 * @property-read int|null $transaction_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product availableForSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product byCategory($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product consignment()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product lowStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAvgCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBuyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereExpiryPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsConsignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLastRestockAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereOwnershipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProfitShareRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStockCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSupplierContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $productId
 * @property int $supplierId
 * @property int $requestedBy
 * @property int $requestedQty
 * @property string|null $note
 * @property string $status
 * @property int|null $confirmedQty
 * @property string|null $supplierNote
 * @property \Illuminate\Support\Carbon|null $respondedAt
 * @property \Illuminate\Support\Carbon|null $completedAt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $requestedByUser
 * @property-read \App\Models\Supplier $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereConfirmedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereRequestedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereSupplierNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestockRequest whereUpdatedAt($value)
 */
	class RestockRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $member_id
 * @property string $type
 * @property numeric $amount
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member|null $member
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving deposits()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saving withdrawals()
 */
	class Saving extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $billId
 * @property int $memberId
 * @property numeric $amount
 * @property string $paymentMethod
 * @property \Illuminate\Support\Carbon $paymentDate
 * @property string|null $referenceNumber
 * @property string $receiptNumber
 * @property string|null $notes
 * @property string|null $proofAttachment
 * @property int $processedBy
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SimpananTransaction $bill
 * @property-read \App\Models\Member $member
 * @property-read \App\Models\User $processor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereProofAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananPayment whereUpdatedAt($value)
 */
	class SimpananPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $memberId
 * @property int|null $relatedMemberId
 * @property string $type
 * @property string|null $billingMonth
 * @property string|null $transactionType
 * @property numeric $amount
 * @property numeric $paidAmount
 * @property numeric $balanceAfter
 * @property string|null $notes
 * @property string|null $buktiPath
 * @property int $processedBy
 * @property string $status
 * @property string|null $transferReference
 * @property int $isRead
 * @property string $billStatus
 * @property int|null $approvedBy
 * @property \Illuminate\Support\Carbon|null $approvedAt
 * @property string|null $rejectionReason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read mixed $formatted_amount
 * @property-read mixed $formatted_balance
 * @property-read mixed $payment_status
 * @property-read mixed $remaining_amount
 * @property-read mixed $status_badge
 * @property-read mixed $transaction_type_label
 * @property-read mixed $type_label
 * @property-read \App\Models\Member|null $member
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SimpananPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User|null $processor
 * @property-read \App\Models\Member|null $relatedMember
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction byMember($memberId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction deposits()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereBillStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereBillingMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereBuktiPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereRelatedMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereTransferReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SimpananTransaction withdrawals()
 */
	class SimpananTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $productId
 * @property string $movementType
 * @property int $quantity
 * @property string|null $referenceType
 * @property string|null $referenceId
 * @property numeric|null $unitCost
 * @property string|null $note
 * @property \Illuminate\Support\Carbon $occurredAt
 * @property bool $isProduction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement stockIn()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement stockOut()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereIsProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereMovementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereUnitCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereUpdatedAt($value)
 */
	class StockMovement extends \Eloquent {}
}

namespace App\Models{
/**
 * @method void updateLastLogin()
 * @property int $id
 * @property string $code
 * @property string $ownerName
 * @property string $businessName
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string|null $description
 * @property string|null $productCategory
 * @property string $password
 * @property string|null $bankName
 * @property string|null $bankAccountNumber
 * @property string|null $bankAccountHolderName
 * @property numeric $registrationFee
 * @property string|null $registrationPaymentProof
 * @property string $registrationPaymentStatus
 * @property \Illuminate\Support\Carbon|null $registrationPaymentVerifiedAt
 * @property int|null $registrationPaymentVerifiedBy
 * @property numeric $monthlyFee
 * @property string $preferredPaymentMethod
 * @property string|null $paymentTerms
 * @property bool $isPaymentActive
 * @property string $paymentStatus
 * @property \Illuminate\Support\Carbon|null $lastPaymentDate
 * @property \Illuminate\Support\Carbon|null $nextPaymentDue
 * @property int $paymentGraceDays
 * @property bool $isSuspendedForPayment
 * @property \Illuminate\Support\Carbon|null $suspendedAt
 * @property string|null $suspensionReason
 * @property int $maxActiveProducts
 * @property int $currentActiveProducts
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $approvedAt
 * @property int|null $approvedById
 * @property string|null $rejectedReason
 * @property int|null $productQualityScore
 * @property int|null $productPriceScore
 * @property int|null $productPackagingScore
 * @property numeric|null $productAverageScore
 * @property string|null $evaluationNotes
 * @property int|null $evaluatedBy
 * @property \Illuminate\Support\Carbon|null $evaluatedAt
 * @property bool $isActive
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $payment_status_color
 * @property-read mixed $payment_status_enum
 * @property-read string $payment_status_label
 * @property-read string $status_color
 * @property-read mixed $status_enum
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereApprovedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereBankAccountHolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCurrentActiveProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEvaluatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEvaluatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEvaluationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereIsPaymentActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereIsSuspendedForPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereLastPaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereMaxActiveProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereMonthlyFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNextPaymentDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePaymentGraceDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePreferredPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProductAverageScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProductCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProductPackagingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProductPriceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereProductQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRegistrationFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRegistrationPaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRegistrationPaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRegistrationPaymentVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRegistrationPaymentVerifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSuspensionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supplierId
 * @property string $type
 * @property string $title
 * @property string $message
 * @property string|null $icon
 * @property string|null $actionUrl
 * @property bool $isRead
 * @property \Illuminate\Support\Carbon|null $readAt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Supplier $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereActionUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplierNotification whereUpdatedAt($value)
 */
	class SupplierNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $invoiceNumber
 * @property int|null $memberId
 * @property int|null $userId
 * @property string $type
 * @property numeric $totalAmount
 * @property string $paymentMethod
 * @property string $status
 * @property string|null $note
 * @property \Illuminate\Support\Carbon $date
 * @property bool $isProduction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $total_gross_profit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Member|null $member
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction sales()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereIsProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUserId($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $transactionId
 * @property int $productId
 * @property int $quantity
 * @property numeric $unitPrice
 * @property numeric $totalPrice
 * @property numeric|null $cogsPerUnit
 * @property numeric|null $totalCogs
 * @property numeric|null $grossProfit
 * @property bool $isProduction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereCogsPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereGrossProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereIsProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereTotalCogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereUpdatedAt($value)
 */
	class TransactionItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon|null $lastLoginAt
 * @property bool $mustChangePassword
 * @property \Illuminate\Support\Carbon|null $passwordChangedAt
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashierShift> $cashierShifts
 * @property-read int|null $cashier_shifts_count
 * @property-read \App\Models\Member|null $member
 * @property-read \App\Models\Member|null $memberKoperasi
 * @property-read \App\Models\MemberMinimarket|null $memberMinimarket
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole($role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMustChangePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $userId
 * @property string $developerName
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $startTime
 * @property string|null $endTime
 * @property numeric $hoursWorked
 * @property string $description
 * @property numeric $hourlyRate
 * @property numeric $totalAmount
 * @property string $status
 * @property int|null $approvedBy
 * @property \Illuminate\Support\Carbon|null $approvedAt
 * @property \Illuminate\Support\Carbon|null $paidAt
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read mixed $formatted_amount
 * @property-read mixed $formatted_hours
 * @property-read mixed $status_color
 * @property-read mixed $status_label
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog inMonth($year, $month)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereDeveloperName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereHoursWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkLog whereUserId($value)
 */
	class WorkLog extends \Eloquent {}
}

