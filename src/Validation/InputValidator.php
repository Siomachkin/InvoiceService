<?php

namespace InvoiceService\Validation;

class InputValidator
{

    public static function validateInvoiceInput($email, $workItems): void
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email is required.');
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }

        $email = strip_tags($email);

        if (!is_array($workItems) || empty($workItems)) {
            throw new \InvalidArgumentException('Work items are required and must be an array.');
        }

        foreach ($workItems as $item) {

            if (empty($item['description']) || empty($item['amount'])) {
                throw new \InvalidArgumentException('Each work item must include a description and an amount.');
            }

            $item['description'] = strip_tags($item['description']);
            $item['amount'] = strip_tags($item['amount']);
            
            if (strlen($item['description']) > 255) {
                throw new \InvalidArgumentException('The description is too long.');
            }

            $item['amount'] = filter_var($item['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if (!is_numeric($item['amount']) || $item['amount'] <= 0) {
                throw new \InvalidArgumentException('Each work item amount must be a positive number.');
            }
        }
    }
}