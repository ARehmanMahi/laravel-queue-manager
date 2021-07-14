<?php

namespace App\Jobs;

use App\Exports\CarsExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class CarsExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var []
     */
    protected $payload = [];

    /**
     * Create a new job instance.
     *
     * CarsExportJob constructor.
     * @param array $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $job_id = $this->job->getJobId();
            echo $job_id . PHP_EOL;

            Excel::store(new CarsExport($this->payload), "public/roro-sheets/cars_$job_id.xlsx");
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
