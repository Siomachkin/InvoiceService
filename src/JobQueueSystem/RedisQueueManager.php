<?php

namespace InvoiceService\JobQueueSystem;

use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\Traits\RedisConnectionTrait;

class RedisQueueManager implements QueueManagerInterface
{
    use RedisConnectionTrait;

    private bool $shouldStop = false;
    private JobFactory $jobFactory;
    private float $timeout;

    public function __construct(JobFactory $factory)
    {
        $this->connectToRedis();
        $this->jobFactory = $factory;
        $this->timeout = getenv('REDIS_BRPOP_TIMEOUT') !== false ? getenv('REDIS_BRPOP_TIMEOUT') : 3;
    }

    public function enqueueJob(string $queue, array $jobData): void
    {
        try {
            $serializedJob = serialize($jobData);
            $this->redis->lpush($queue, [$serializedJob]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function listen(string $queue): void
    {
        $this->shouldStop = false;

        while (!$this->shouldStop) {
            try {
                $serializedJob = $this->redis->brpop($queue, $this->timeout);

                if ($serializedJob) {
                    $jobPayload = unserialize($serializedJob[1]);
                    if ($jobPayload === false) {
                        throw new \Exception('Unable to unserialize job data.');
                    }
                    $job = $this->jobFactory->createJob($jobPayload['type'], $jobPayload['data']);
                    $job->handle();
                }
            } catch (\Exception $e) {
                error_log('An error occurred: ' . $e->getMessage());
                throw new \Exception('An error occurred: ' . $e->getMessage());
            }
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }
}