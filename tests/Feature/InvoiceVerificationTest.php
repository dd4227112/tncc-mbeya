<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class InvoiceVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_signed_public_url_displays_minimal_invoice_verification_details(): void
    {
        $member = User::factory()->create();
        $creator = User::factory()->create();
        $invoice = Invoice::create([
            'reference_number' => 'INV-VERIFY-001',
            'user_id' => $member->id,
            'total_amount' => 12500,
            'status' => 'paid',
            'created_by' => $creator->id,
            'date' => '2026-10-01',
        ]);

        $url = URL::signedRoute('invoices.verify', ['reference' => $invoice->reference_number]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Invoice verified')
            ->assertSee('INV-VERIFY-001')
            ->assertSee('12,500.00')
            ->assertSee('PAID');
    }

    public function test_an_unsigned_public_verification_url_is_rejected(): void
    {
        $member = User::factory()->create();
        $creator = User::factory()->create();
        Invoice::create([
            'reference_number' => 'INV-VERIFY-002',
            'user_id' => $member->id,
            'total_amount' => 5000,
            'status' => 'pending',
            'created_by' => $creator->id,
            'date' => '2026-10-01',
        ]);

        $this->get(route('invoices.verify', ['reference' => 'INV-VERIFY-002']))
            ->assertNotFound();
    }
}
