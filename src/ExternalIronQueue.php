<?php namespace Kristianedlund\LaravelExternalQueue;

use Illuminate\Queue\IronQueue;
use Kristianedlund\LaravelExternalQueue\Jobs\ExternalIronJob;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class ExternalIronQueue extends IronQueue implements QueueContract
{
    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $job = $this->iron->getMessage($queue);

        if (! is_null($job)) {
            $job->body = $this->parseJobBody($job->body);

            return new ExternalIronJob($this->container, $this, $job);
        }
    }
}