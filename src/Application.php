<?php
namespace InvoiceService;


use InvoiceService\Controllers\InvoiceController;

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

    private function handlePostRequest()
    {

        $email = $_POST['email'] ?? null;
        $workItems = $_POST['workItems'] ?? [];

        if ($email && $workItems) {
            $result = $this->invoiceController->createAndSendInvoice($email, $workItems);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
        }
    }

    private function showView()
    {
        echo 'This is the default view.';
    }
}