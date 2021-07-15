<?php

namespace App\Jobs;

use App\Exports\CarsExport;
use App\Models\Car;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            sleep(10);

            // $this->job->getJobId();

            $limit = 500;
            if (!empty($this->payload['limit'])) {
                $limit = (int)$this->payload['limit'];

                if ($limit > 5000) {
                    $limit = 5000;
                }
            }

            $carRecords = Car::limit($limit)->get()->toArray();

            // Laravel storage path (../DocumentRoot/storage)
            $template = storage_path('app\excel_templates\template.xlsx');

            $spreadsheet = IOFactory::load($template);
            $worksheet = $spreadsheet->getActiveSheet();

            $row = 1;
            foreach ($carRecords as $car) {
                $row++;
                $col = 0;
                foreach ($car as $value) {
                    $worksheet->setCellValueByColumnAndRow(++$col, $row, $value);
                }
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Make directory in storage recursively; if exists ignores
            Storage::makeDirectory('public/roro-sheets');

            // PhpSpreadsheet writes to DocumentRoot instead of laravel default storage path.
            // Hence we supply storage path manually to save in storage path
            // Laravel storage path (../DocumentRoot/storage)
            if(isset($this->payload['file_name'])) {
                $fileName = $this->payload['file_name'];
            } else {
                $d = date('Y-m-d-h-i-s');
                $fileName = "cars_api_static_$d.xlsx";
            }
            $filePath = storage_path("app/public/roro-sheets/$fileName");
            //
            $writer->save($filePath);

            // Excel::store(new CarsExport($this->payload), "public/roro-sheets/cars_$job_id.xlsx");
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
