<?php

namespace InvoiceService\EventsSystem;

use InvoiceService\EventsSystem\Contracts\ObserverInterface;

trait SubjectTrait
{
    private $observers = [];

    public function attach(ObserverInterface $observer): void
    {
        $this->observers[spl_object_hash($observer)] = $observer;
    }

    public function detach(ObserverInterface $observer): void
    {
        unset($this->observers[spl_object_hash($observer)]);
    }

    public function notifyObservers(): void
    {
        foreach ($this->observers as $observer) {
            $observer->onEvent($this);
        }
    }
}