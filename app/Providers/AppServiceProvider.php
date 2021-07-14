<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            // $job_id = $event->job->getJobId();
            // $uuid = $event->job->uuid();
            // $connection = $event->job->getConnectionName();
            // $queue = $event->job->getQueue();
            // $payload = $event->job->payload();

            echo 'Processing...';
        });


        Queue::after(function (JobProcessed $event) {
            $job_id = $event->job->getJobId();
            $uuid = $event->job->uuid();
            $connection = $event->job->getConnectionName();
            $queue = $event->job->getQueue();
            $payload = $event->job->payload();
            $payload = json_encode($payload);

            echo 'Processed...';

            $query = "insert into jobs_log (job_id, uuid, connection, queue, payload) values ($job_id, '$uuid', '$connection', '$queue', '$payload')";
            db::insert($query);
        });

        Queue::failing(function (JobFailed $event) {
            // $job_id = $event->job->getJobId();
            // $uuid = $event->job->uuid();
            // $connection = $event->job->getConnectionName();
            // $queue = $event->job->getQueue();
            // $payload = $event->job->payload();
            // $exception = $event->exception;

            echo 'Failed...';
        });
    }
}
