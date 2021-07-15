<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Jobs\CarsExportJob;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends Controller
{
    /**
     * @param Request $request
     */
    public function test_job(Request $request)
    {
        TestJob::dispatch($request->all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roro_sheets(Request $request)
    {
        $d = date('Y-m-d-h-i-s');
        $fileName = "cars_api_$d.xlsx";
        $request->request->add(['file_name' => $fileName]);

        CarsExportJob::dispatch($request->all());

        $link = asset("storage/roro-sheets/$fileName");

        return $this->sendResponse("Spreadsheet generation in process. You can access it $link");
    }

    /**
     * @param Request $request
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excel(Request $request)
    {
        $limit = (int)$request->input('limit', 500);
        if ($limit > 5000) {
            $limit = 5000;
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
        $d = date('Y-m-d-h-i-s');
        $fileName = "cars_web_$d.xlsx";
        $filePath = storage_path("app/public/roro-sheets/$fileName");
        //
        $writer->save($filePath);

        $href = asset("storage/roro-sheets/$fileName");
        $link = "<a href='$href'>here</a>";

        echo "Spreadsheet generated with limit max $limit. You can access file $link";
    }
}
