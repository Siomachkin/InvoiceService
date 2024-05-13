<?php

namespace InvoiceService\EventsSystem\Contracts;

interface ObserverInterface
{
    public function onEvent(SubjectInterface $subject): void;
}