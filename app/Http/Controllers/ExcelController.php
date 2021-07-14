<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Jobs\CarsExportJob;
use Illuminate\Http\Request;
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
        CarsExportJob::dispatch($request->all());

        return $this->sendResponse('Roro sheet generation in process');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excel(Request $request)
    {
        // Laravel storage path (../DocumentRoot/storage)
        $template = storage_path('app/excel-templates/template.xlsx');

        $spreadsheet = IOFactory::load($template);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('A2')->setValue('John');
        $worksheet->getCell('B2')->setValue('Smith');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Make directory in storage recursively; if exists ignores
        Storage::makeDirectory('public/roro-sheets');

        // PhpSpreadsheet writes to DocumentRoot instead of laravel default storage path.
        // Hence we supply storage path manually to save in storage path
        // Laravel storage path (../DocumentRoot/storage)
        $fileName = 'cars.xlsx';
        $filePath = storage_path("app/public/roro-sheets/$fileName");
        //
        $writer->save($filePath);

        return $this->sendResponse(asset('storage/roro-sheets/' . $fileName) . ' generated');
    }
}
