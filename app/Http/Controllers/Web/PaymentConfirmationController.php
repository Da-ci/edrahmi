<?php

namespace App\Http\Controllers\Web;

use App\Models\Application;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Traits\HandlesApiExceptions;
use App\Traits\HandlesWebExceptions;
use App\Services\Payments\PaymentService;
use App\Services\Payments\ReceiptService;

class PaymentConfirmationController extends Controller
{
    use HandlesApiExceptions;
    use HandlesWebExceptions;

    public function __construct(private PaymentService $paymentService, private ReceiptService $receiptService) {}

    public function confirm(string $orderNumber)
    {
        $result = $this->paymentService->confirmPayment($orderNumber);

        $transaction = $result['transaction'];
        $gatewayResponse = $result['gateway_response'];

        $redirectUrl = $transaction->application->redirect_url;

        $queryParams = http_build_query([
            'order_number' => $orderNumber,
            // 'status' => $transaction->status,
            // 'confirmation_status' => $transaction->confirmation_status,
            // 'gateway_code' => $this->getGatewayErrorCode($gatewayResponse)
        ]);

        if ($transaction->origin === 'System')
            return redirect()->route('pay', [
                'slug' => $transaction->application->slug,
                'order_number' => $transaction->order_number
            ]);

        return redirect()->to("$redirectUrl?$queryParams");

        try {
        } catch (\Throwable $e) {
            return $this->handleApiException($e);
        }
    }

    private function getGatewayErrorCode(array $response): string
    {
        return (string)($response['ErrorCode'] ?? $response['errorCode'] ?? 'UNKNOWN');
    }
}
