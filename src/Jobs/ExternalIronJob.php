<?php namespace Kristianedlund\LaravelExternalQueue\Jobs;

use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\IronJob;
use Illuminate\Support\Arr;

class ExternalIronJob extends IronJob implements JobContract
{

    /**
     * Resolve and fire the job handler method.
     *
     * @param  array  $payload
     * @return void
     */
    protected function resolveAndFire(array $payload)
    {
        $handlerClass = $this->resolveHandler();

        $this->instance = $this->resolve($handlerClass);

        $data = $this->getJobData();

        $this->instance->handle($this, $data);
    }

    /**
     * Spawns a new handler for the specific job
     * @return Kristianedlund\ExternalQueue\Contracts\ExternalQueueJobHandler The handler for this this job
     */
    protected function resolveHandler()
    {

        $job = $this->getJobName();
        //Get the handler class name
        $classname = config('externalqueue.handlers.' . $job, '');

        if (!class_exists($classname) ||
            !in_array('Kristianedlund\LaravelExternalQueue\Contracts\ExternalQueueJobHandler', class_implements($classname))
        ) {
            throw new \UnexpectedValueException('The handler class for ' . $job . ' was not found');
        }
        return $classname;
    }

    /**
     * Extract the payload data from the queue message
     * @return Array The payload data
     */
    protected function getJobData()
    {
        $rawdata = $this->decodePayload();

        return $rawdata['data'];
    }

    /**
     * Get the job name
     * @return string The job name
     */
    protected function getJobName()
    {
        $rawdata = $this->decodePayload();
        return array_key_exists('job', $rawdata) ? $rawdata['job'] : 'default';
    }

    /**
     * Decode the payload data in the message
     * @return Array The decoded data
     */
    protected function decodePayload()
    {
        return json_decode($this->getRawBody(), true);
    }

    /**
    * Our payloads typically will not have the queue name, we
    * need to fallback to the IronQueue default
    * @return String
    */
    public function getQueue()
    {
        return $this->iron->getQueue(parent::getQueue());
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;

        if (isset($this->job->pushed)) {
            return;
        }

        $this->iron->deleteMessageWithReservation($this->getQueue(), $this->job->id, $this->job->reservation_id);
    }
}