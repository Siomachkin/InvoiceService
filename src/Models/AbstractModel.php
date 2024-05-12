<?php

namespace InvoiceService\Models;

use Traversable;
use MongoDB\Collection;
use MongoDB\InsertOneResult;
use MongoDB\DeleteResult;
use MongoDB\UpdateResult;
use MongoDB\Model\BSONDocument;

abstract class AbstractModel
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function insertOne(array $document): InsertOneResult
    {
        return $this->collection->insertOne($document);
    }

    public function deleteOne(array $filter): DeleteResult
    {
        return $this->collection->deleteOne($filter);
    }

    public function updateOne(array $filter, array $update): UpdateResult
    {
        return $this->collection->updateOne($filter, ['$set' => $update]);
    }

    public function findOne(array $filter): ?BSONDocument
    {
        return $this->collection->findOne($filter);
    }

    public function findAll(array $filter = []): Traversable
    {
       return $this->collection->find($filter);
    }
}
