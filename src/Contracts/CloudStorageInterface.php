<?php
namespace InvoiceService\Contracts;

interface CloudStorageInterface
{
    public function upload(string $sourceFilePath, string $destinationPath): string;
    public function download(string $objectPath): string;
    public function delete(string $objectPath): void;
}