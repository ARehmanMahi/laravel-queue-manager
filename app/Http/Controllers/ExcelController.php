<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Jobs\CarsExportJob;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $row = 2;
        foreach ($carRecords as $car) {
            $col = 1;
            foreach ($car as $value) {
                $worksheet->setCellValueByColumnAndRow($col++, $row, $value);
            }
            $row++;
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

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function mergeExternalSheet()
    {
        $inputFileType = 'Xlsx';
        $inputFileNames = [
            'a.xlsx',
            'b.xlsx',
        ];
        $sheetnames = [
            'Sheet1'
        ];

        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetnames);

        $inputFileName = array_shift($inputFileNames);
        $filePath = storage_path("app/public/roro-sheets/$inputFileName");
        $spreadsheetMain = $reader->load($filePath);

        $main = $spreadsheetMain->getActiveSheet();
        $highestRow = $main->getHighestRow();
        $highestCol = $main->getHighestColumn();

        echo 'Last Row Number: '. $highestRow . '<br>';
        echo 'Last Col Name: ' . $highestCol . '<br>';
        echo 'Last Col Index: ' . Coordinate::columnIndexFromString($highestCol) . '<br>';

        foreach ($inputFileNames as $inputFileName) {
            $filePath = storage_path("app/public/roro-sheets/$inputFileName");
            echo $filePath . '<br>';
            $spreadsheet = $reader->load($filePath);

            $sheetName = 'Sheet1';
            $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetName);

            if($duplicateSheet = $spreadsheetMain->getSheetByName($sheetName)) {
                $duplicateSheet->setTitle($sheetName . date('_YmdHis'));
            }

            $spreadsheetMain->addExternalSheet($clonedWorksheet);
        }

        $writer = new Xlsx($spreadsheetMain);

        $fileName = "merged.xlsx";
        $filePath = storage_path("app/public/roro-sheets/$fileName");
        $writer->save($filePath);
    }

    public function mergeExternalInOne()
    {
        $inputFileType = 'Xlsx';
        $inputFileNames = [
            'a.xlsx',
            'b.xlsx',
        ];
        $sheetnames = [
            'Sheet1'
        ];

        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetnames);

        $inputFileName = array_shift($inputFileNames);
        $filePath = storage_path("app/public/roro-sheets/$inputFileName");
        echo $filePath . '<br>';
        $spreadsheetMain = $reader->load($filePath);

        foreach ($inputFileNames as $inputFileName) {
            $filePath = storage_path("app/public/roro-sheets/$inputFileName");
            $spreadsheet = $reader->load($filePath);

            $sheetName = 'Sheet1';
            $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetName);

            if($duplicateSheet = $spreadsheetMain->getSheetByName($sheetName)) {
                $duplicateSheet->setTitle($sheetName . date('_YmdHis'));
            }

            $spreadsheetMain->addExternalSheet($clonedWorksheet);
        }

        $writer = new Xlsx($spreadsheetMain);

        $fileName = "merged2.xlsx";
        $filePath = storage_path("app/public/roro-sheets/$fileName");
        $writer->save($filePath);
    }

}
