<?php
namespace InvoiceService\docker\php;

require __DIR__ . '/../../vendor/autoload.php';

use InvoiceService\Models\ClientModel;
use InvoiceService\Models\CompanyModel;
use InvoiceService\Traits\MongoConnectionTrait;

class DatabaseSeeder
{

    use MongoConnectionTrait;

    private ClientModel $clientModel;
    private CompanyModel $companyModel;

    public function __construct()
    {
        $this->connectToMongo();

        $clientsCollection = $this->client->selectCollection($this->databaseName, 'clients');
        $companiesCollection = $this->client->selectCollection($this->databaseName, 'companies');
        $this->clientModel = new ClientModel($clientsCollection);
        $this->companyModel = new CompanyModel($companiesCollection);

    }

    public function seedDatabase(): void
    {
        $this->addRandomClients();
        $this->addRandomCompanies();
    }

    public function addRandomClients(): void
    {

        for ($i = 0; $i < 10; $i++) {
            $firstName = 'Client' . rand(1, 100);
            $lastName = 'LastName' . rand(1, 100);
            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@example.com';

            $clientData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
            ];

            $this->clientModel->insertOne($clientData);
        }
    }

    public function addRandomCompanies(): void
    {

        $clients = $this->clientModel->findAll();

        foreach ($clients as $client) {
            $companyName = 'Company' . rand(1, 100);
            $address = rand(1, 100) . ' Main St, Some City, Some State, ' . rand(10000, 99999);

            $companyData = [
                'client_email' => $client['email'],
                'company_name' => $companyName,
                'address' => $address,
            ];

            $this->companyModel->insertOne($companyData);
        }
    }

}

$seeder = new DatabaseSeeder();
$seeder->seedDatabase();