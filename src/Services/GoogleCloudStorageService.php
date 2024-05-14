<?php

namespace InvoiceService\Services;

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Bucket;
use InvoiceService\Contracts\CloudStorageInterface;

class GoogleCloudStorageService implements CloudStorageInterface
{
    private Bucket $bucket;

    public function __construct()
    {
        $bucketName = getenv('GOOGLE_BUCKET_NAME');
        $keyFilePath = getenv('GOOGLE_APPLICATION_CREDENTIALS');

        $storage = new StorageClient([
            'keyFilePath' => $keyFilePath,
        ]);
        $this->bucket = $storage->bucket($bucketName);

        try {
            $this->bucket->info();
        } catch (\Google\Cloud\Core\Exception\NotFoundException $e) {
            throw new \RuntimeException(sprintf('The bucket "%s" does not exist or is not accessible.', $bucketName), 0, $e);
        }
    }

    public function upload(string $sourceFilePath, string $destinationPath): string
    {
        $file = fopen($sourceFilePath, 'r');
        if (!$file) {
            throw new \RuntimeException("Unable to open file for reading: {$sourceFilePath}");
        }

        try {
            $object = $this->bucket->upload($file, [
                'name' => $destinationPath
            ]);

            if (is_resource($file)) {
                fclose($file);
            }

            $expiresAt = new \DateTime('+1 week');
            $options = [
                'version' => 'v4'
            ];
            $signedUrl = $object->signedUrl($expiresAt, $options);

            return $signedUrl;
            
        } catch (\Exception $e) {
            if (is_resource($file)) {
                fclose($file);
            }
            throw new \RuntimeException(sprintf('Error uploading file to Google Cloud Storage: %s', $e->getMessage()), 0, $e);
        }
    }

    public function download(string $objectPath): string
    {
        try {
            $object = $this->bucket->object($objectPath);
            $tempFilePath = tempnam(sys_get_temp_dir(), 'gcs') . '.pdf';
            $object->downloadToFile($tempFilePath);
            return $tempFilePath;
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error downloading file from Google Cloud Storage: %s', $e->getMessage()), 0, $e);
        }
    }

    public function delete(string $objectPath): void
    {
        try {
            $object = $this->bucket->object($objectPath);
            $object->delete();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error deleting file from Google Cloud Storage: %s', $e->getMessage()), 0, $e);
        }
    }
}