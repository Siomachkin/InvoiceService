<?php

namespace InvoiceService\EventsSystem\Contracts;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;
    public function notifyObservers(): void;
}