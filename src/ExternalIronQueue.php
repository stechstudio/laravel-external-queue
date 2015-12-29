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

    /**
     * Delete a message from the Iron queue, with reservation ID.
     * Hopefully we can remove this method once Laravel updates the core IronQueue
     * to the new v3 API.
     *
     * @param  string  $queue
     * @param  string  $id
     * @return void
     */
    public function deleteMessageWithReservation($queue, $id, $reservation_id)
    {
        $this->iron->deleteMessage($queue, $id, $reservation_id);
    }
}