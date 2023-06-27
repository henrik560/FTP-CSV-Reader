<?php

namespace App\Jobs;

use App\Models\Debtor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;

class createDebtorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $debtors;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $debtors)
    {
        $this->debtors = $debtors;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LazyCollection::make($this->debtors)->each(function ($debtor) {
            Debtor::updateOrCreate(
                ['debtor_number' => $debtor["Debiteurnummer"]],
                $this->mapDebtorData($debtor)
            );
        });
    }

    private function mapDebtorData(array $debtor): array
    {
        return [
            'name_1' => $debtor['Naam1'],
            'name_2' => $debtor['Naam2'],
            'search_name' => $debtor['Zoeknaam'],
            'address' => $debtor['Adres'],
            'postalcode' => $debtor['Postcode'],
            'city' => $debtor['Plaats'],
            'country' => $debtor['Land'],
            'contact' => $debtor['Contact'],
            'phonenumber' => $debtor['Telefoon'],
            'mobile' => $debtor['Mobiel'],
            'email' => $debtor['Email'],
            'email_cc' => $debtor['Email cc'],
            'email_invoice' => $debtor['Email factuur'],
            'email_invoice_cc' => $debtor['Email factuur cc'],
            'tax_number' => $debtor['BTW nummer'],
        ];
    }
}
