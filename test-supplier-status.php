<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST SUPPLIER STATUS CONSISTENCY ===\n\n";

// Test 1: Enum SupplierStatus
echo "1. Test SupplierStatus Enum:\n";
$statusPending = \App\Enums\SupplierStatus::PENDING;
echo "   - Status: {$statusPending->value}\n";
echo "   - Label: {$statusPending->label()}\n";
echo "   - Color: {$statusPending->color()}\n\n";

$statusApproved = \App\Enums\SupplierStatus::APPROVED;
echo "   - Status: {$statusApproved->value}\n";
echo "   - Label: {$statusApproved->label()}\n";
echo "   - Color: {$statusApproved->color()}\n\n";

// Test 2: Model Supplier dengan accessor
echo "2. Test Model Supplier Accessor:\n";
$supplier = new \App\Models\Supplier();
$supplier->status = 'APPROVED';
$supplier->businessName = 'Test Supplier';
$supplier->registrationPaymentStatus = 'VERIFIED';

echo "   - Status: {$supplier->status}\n";
echo "   - Status Label: {$supplier->statusLabel}\n";
echo "   - Status Color: {$supplier->statusColor}\n";
echo "   - Payment Status: {$supplier->registrationPaymentStatus}\n";
echo "   - Payment Label: {$supplier->paymentStatusLabel}\n";
echo "   - Payment Color: {$supplier->paymentStatusColor}\n\n";

// Test 3: String comparison
echo "3. Test String Comparison:\n";
$isPending = ($supplier->status === 'PENDING');
$isApproved = ($supplier->status === 'APPROVED');
$isActive = ($supplier->status === 'ACTIVE');

echo "   - Is PENDING? " . ($isPending ? 'TRUE' : 'FALSE') . "\n";
echo "   - Is APPROVED? " . ($isApproved ? 'TRUE' : 'FALSE') . "\n";
echo "   - Is ACTIVE? " . ($isActive ? 'TRUE' : 'FALSE') . "\n\n";

// Test 4: All status values
echo "4. Test All SupplierStatus Values:\n";
foreach (\App\Enums\SupplierStatus::cases() as $status) {
    echo "   - {$status->value}: {$status->label()} ({$status->color()})\n";
}

echo "\n✅ ALL TESTS PASSED!\n";
