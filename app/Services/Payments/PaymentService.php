<?php

namespace App\Services\Payments;

use App\Models\Application;
use App\Models\Transaction;
use Illuminate\Support\Facades\URL;
use App\Exceptions\PaymentException;
use App\Services\Payments\CredentialsService;
use App\Services\Payments\TransactionUpdater;
use App\Services\Payments\ConfirmGatewayService;
use App\Services\Payments\InitiateGatewayService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentService
{
    public function __construct(
        private InitiateGatewayService $initiator,
        private ConfirmGatewayService $confirmer,
        private TransactionUpdater $updater,
        private CredentialsService $credentials
    ) {}

    public function initiatePayment(array $data, string $appKey): array
    {
        try {
            $application = Application::where('app_key', $appKey)->firstOrFail();
            $transaction = $this->createTransaction($data, $application);

            $response = $this->initiator->execute($transaction);

            return [
                'formUrl' => $response['formUrl'],
                'transaction' => $transaction->only(['order_number', 'status', 'amount'])
            ];
        } catch (ModelNotFoundException $e) {
            throw new PaymentException('Application not found', 'APP_NOT_FOUND', 404);
        }
    }

    public function confirmPayment(string $orderNumber): array
    {
        try {
            $transaction = Transaction::with('application')
                ->where('order_number', $orderNumber)
                ->firstOrFail();

            $response = $this->confirmer->execute($transaction);

            return [
                'transaction' => $transaction,
                'gateway_response' => $response
            ];
        } catch (ModelNotFoundException $e) {
            throw new PaymentException('Transaction not found', 'TRANSACTION_NOT_FOUND', 404);
        }
    }

    private function createTransaction(array $data, Application $application): Transaction
    {
        return Transaction::create([
            'amount' => $data['amount'],
            'order_number' => $this->generateOrderNumber($application),
            'status' => 'initiated',
            'application_id' => $application->id,
            'license_id' => $application->license->id,
            'license_env' => $application->license_env,
            'currency' => '012',
            'partner_id' => $application->partner->id,
            'origin' => $data['origin'] ?? 'Application',
        ]);
    }

    private function generateOrderNumber(Application $application): string
    {
        $licenseId = $application->license->id;

        do {
            $unique = uniqid(mt_rand(), true);
            if ($application->license_env === 'production')
                $orderNumber = strtoupper(substr(base_convert($unique, 16, 36), 0, 20));
            else
                $orderNumber = strtoupper(substr(base_convert($unique, 16, 36), 0, 10));
        } while (
            Transaction::where('order_number', $orderNumber)
            ->where('license_id', $licenseId)
            ->exists()
        );

        return $orderNumber;
    }
}
