<?php
/**
 * @OA\Info(
 *     title="Invoice Service API",
 *     version="1.0.0",
 *     description="API for creating and sending invoices",
 *     @OA\Contact(
 *         email="support@invoiceservice.com"
 *     )
 * )
 */
namespace InvoiceService;


use InvoiceService\Controllers\InvoiceController;
use InvoiceService\Validation\InputValidator;

class Application
{
    private InvoiceController $invoiceController;


    public function __construct($container)
    {
        $this->invoiceController = $container->get(InvoiceController::class);
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'POST':
                $this->handlePostRequest();
                break;
            default:
                $this->showView();
                break;
        }
    }


    /**
     * @OA\Post(
     *     path="/",
     *     summary="Create and send an invoice",
     *     operationId="createAndSendInvoice",
     *     tags={"Invoices"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Email of the client and list of work items for the invoice",
     *         @OA\JsonContent(
     *             required={"email", "workItems"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="user@email.com"
     *             ),
     *             @OA\Property(
     *                 property="workItems",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="Design services"
     *                     ),
     *                     @OA\Property(
     *                         property="amount",
     *                         type="number",
     *                         format="float",
     *                         example=300.00
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice created and email sent"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create invoice",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to create invoice.")
     *         )
     *     )
     * )
     */
    private function handlePostRequest()
    {
        $response = [];
        $statusCode = 200;

        try {
            $rawData = file_get_contents("php://input");
            $requestData = json_decode($rawData, true);

            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException("Invalid JSON format");
            }

            $email = $requestData['email'] ?? null;
            $workItems = $requestData['workItems'] ?? [];

            InputValidator::validateInvoiceInput($email, $workItems);

            $result = $this->invoiceController->createAndSendInvoice($email, $workItems);

            if ($result['status'] !== 'success') {
                throw new \Exception($result['message']);
            }

            $response = $result;
        } catch (\InvalidArgumentException $e) {
            $statusCode = 400;
            $response = ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $statusCode = 500;
            $response = ['error' => $e->getMessage()];
        }

        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($response);
    }

    private function showView()
    {
        header('Content-Type: text/plain');
        echo 'This service is intended for API use only.';
    }
}