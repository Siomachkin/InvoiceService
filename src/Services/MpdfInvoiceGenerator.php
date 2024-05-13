<?php
namespace InvoiceService\Services;

use InvoiceService\Contracts\CloudStorageInterface;
use InvoiceService\Contracts\InvoiceGeneratorInterface;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfInvoiceGenerator implements InvoiceGeneratorInterface
{
    private $cloudStorage;

    public function __construct(CloudStorageInterface $cloudStorage)
    {
        $this->cloudStorage = $cloudStorage;
    }


    public function generate(array $invoiceData): string
    {
        $mpdf = new Mpdf();

        $tempPdfFilePath = tempnam(sys_get_temp_dir(), 'invoice') . '.pdf';
        $mpdf->Output($tempPdfFilePath, Destination::FILE);

        if (!file_exists($tempPdfFilePath)) {
            throw new \Exception('Unable to save the invoice PDF to temporary file.');
        }

        $cloudPdfFilePath = "invoices/{$invoiceData['invoice_number']}.pdf";
        $publicUrl = $this->uploadFileToCloud($tempPdfFilePath, $cloudPdfFilePath);

        unlink($tempPdfFilePath);

        return $publicUrl;
    }

    private function uploadFileToCloud(string $localFilePath, string $cloudFilePath): string
    {
        try {
            $publicUrl = $this->cloudStorage->upload($localFilePath, $cloudFilePath);
            return $publicUrl;
        } catch (\Exception $e) {
            throw new \Exception('Failed to upload the invoice PDF to cloud storage: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function generateHtmlContent(array $invoiceData, string $formattedDate, float $total): string
    {
        $htmlContent = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Invoice</title>
            <style>
                body { font-family: sans-serif; margin: 0; padding: 0; }
                .invoice-header { background-color: #ADD8E6; color: white; text-align: center; padding: 20px 0; }
                .invoice-header img { max-width: 100px; }
                .invoice-header h2 { margin: 0; }
                .invoice-title { background-color: #fff; text-align: center; padding: 20px 0; }
                .invoice-title h1 { margin: 0; }
                .invoice-info { padding: 20px; background-color: #fff; }
                .invoice-info th { text-align: left; padding-right: 15px; }
                .invoice-info .amount { text-align: right; }
                .separator { width: 100%; height: 1px; background-color: #ccc; margin: 20px auto; }
                .invoice-body { padding: 20px; background-color: #fff; }
                .invoice-footer { background-color: #f2f2f2; color: #000; text-align: center; padding: 20px 0 0 0; font-size: 0.8em; }
                .appreciation { text-align: center; font-style: italic; margin-top: 20px; }
                .total-due {font-weight: bold; }
                .table-items { width: 100%; border-collapse: collapse; }
                .table-items th, .table-items td { padding: 10px;}
                .table-items th { text-align: left; }
                .table-items td { text-align: left; }
            </style>
        </head>
        <body>
            <div class="invoice-header">
                
                <h2>Brick and Willow Design</h2>
            </div>
            <div class="invoice-title">
                <h1>New Invoice</h1>
                <div style="text-align: center;">
                    <p>$' . number_format($total, 2) . ' due on ' . $formattedDate . '</p>
                </div>
            </div>
           
            <div class="separator"></div>
            <div class="invoice-body">
                <table class="invoice-info">
                    <tr>
                        <th>Invoice #</th>
                        <td>' . $invoiceData['invoice_number'] . '</td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>' . $formattedDate . '</td>
                    </tr>
                </table>
                <table class="invoice-info">
                    <tr>
                        <th>Customer:</th>
                        <td>' . $invoiceData['client']['first_name'] . ' ' . $invoiceData['client']['last_name'] . '</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>' . $invoiceData['client']['email'] . '</td>
                    </tr>
                    <tr>
                        <th>Company:</th>
                        <td>' . $invoiceData['company']['company_name'] . '</td>
                    </tr>
                </table>
                <div class="separator"></div>
                <div class="appreciation">
                    <p>We appreciate your business</p>
                </div>
                <div class="separator"></div>


                <table class="table-items">
                    <tbody>';

        foreach ($invoiceData['items'] as $item) {
            $htmlContent .= '<tr><td>' . $item['description'] . '</td><td>$' . number_format($item['amount'], 2) . '</td></tr>';
        }

        $htmlContent .= '
                    <tr>
                        <td class="total-due">Total Due:</td>
                        <td class="total-due">$' . number_format($total, 2) . '</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-footer">
                <p>Brick and Willow Design</p>
                <p>123 Design St, Creativity City, Artstate 12345</p>
                <p>+1 234 567 8900</p>
            </div>
        </body>
        </html>';

        return $htmlContent;
    }
}