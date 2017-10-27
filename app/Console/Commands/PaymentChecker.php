<?php

namespace App\Console\Commands;

use App\Events\PaymentCompleted;
use App\Payment;
use App\Util\Iota;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PaymentChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iota:payments:check {user=0} {paymentId=0} {all=0} {duration=48}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if pending payments confirmed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $paymentId = $this->argument('paymentId');
        $checkAll = $this->argument('all');
        $duration = $this->argument('duration');

        // Get all uncompleted payments
        $payments = Payment::whereStatus(0);

        if ($userId) {
            $payments = Payment::whereStatus(0)->whereUserId($userId);
        }

        if ($paymentId) {
            $payments = $payments->whereId($paymentId);
        }

        if ($duration) {
            $payments->whereDate("updated_at", '>=', Carbon::now()->subHours($duration));
        }

        $payments = $payments->get();

        $totalUnverifiedPayments = count($payments);
        $totalChecked = 0;
        $totalVerifiedPayments = 0;

        $this->info("Total unverified payments: " . $totalUnverifiedPayments);

        if ($totalUnverifiedPayments) {

            $bar = $this->output->createProgressBar($totalUnverifiedPayments);

            foreach ($payments as $payment) {

                $metadata = $payment->metadata;

                // Check if address exists
                if ($payment->address && (isset($metadata['transaction']['hash']) || $checkAll == 1)) {

                    // Get balance for address
                    $iotaBalance = (new Iota())->getBalanceByAddress($payment->address->address, 'I');

                    // If balance is greater or equals to what it was supposed to
                    if ($iotaBalance >= $payment->price_iota) {

                        // Update paymetn status as done
                        $payment->update(['status' => 1]);

                        // If IPN URL exists, call it
                        if ($payment->ipn) {
                            try{
                                (new Iota())->call([
                                    'URL'    => $payment->ipn,
                                    'METHOD' => 'POST',
                                    'DATA'   => [
                                        'address'         => $payment->address->address,
                                        'address_balance' => $iotaBalance,
                                        'payment_id'      => base64_encode($payment->id),
                                        'invoice_id'      => $payment->invoice_id,
                                        'status'          => $payment->status,
                                        'price_usd'       => $payment->price_usd,
                                        'price_iota'      => $payment->price_iota,
                                        'ipn'             => $payment->ipn,
                                        'ipn_verify_code' => $payment->ipn_verify_code,
                                        'custom'          => $payment->metadata,
                                        'created_at'      => $payment->created_at,
                                        'updated_at'      => $payment->updated_at
                                    ]
                                ]);
                            }catch (\Exception $e){

                            }
                        }

                        // [Event]
                        event(new PaymentCompleted($payment, []));

                        $totalVerifiedPayments++;
                    }

                    $totalChecked++;
                }

                $bar->advance();
            }

            $bar->finish();
        }

        $this->info("Checked for verification: " . $totalChecked);
        $this->info("Verified now: " . $totalVerifiedPayments);
        die;
    }
}
