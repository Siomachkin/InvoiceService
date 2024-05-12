<?php
namespace InvoiceService;

use InvoiceService\Services\MailerService;
use InvoiceService\Services\RedisCacheService;
use InvoiceService\Services\MpdfInvoiceGenerator;
use InvoiceService\Repositories\MongoInvoiceRepository;
use InvoiceService\Controllers\InvoiceController;

class Application
{
    private $mailerService;
    private $invoiceGenerator;
    private $cacheService;
    private $invoiceRepository;
    private $invoiceController;

    public function __construct()
    {
        $this->mailerService = new MailerService();
        $this->invoiceGenerator = new MpdfInvoiceGenerator();
        $this->cacheService = new RedisCacheService();
        $this->invoiceRepository = new MongoInvoiceRepository($this->cacheService);

        $this->invoiceController = new InvoiceController(
            $this->mailerService,
            $this->invoiceGenerator,
            $this->invoiceRepository
        );
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