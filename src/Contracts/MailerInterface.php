<?php
namespace InvoiceService\Contracts;
   
interface MailerInterface {
    public function send($to, $subject, $body, $attachment = null): bool;
}