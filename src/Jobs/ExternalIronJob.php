<?php namespace Kristianedlund\LaravelExternalQueue\Jobs;

use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\IronJob;

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
        $handler = $this->resolveHandler();
        $data = $this->getJobData();

        $handler->handle($this, $data);
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

        return new $classname;
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
}