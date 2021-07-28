<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Jobs\CarsExportJob;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        echo 'Last Row Number: ' . $highestRow . '<br>';
        echo 'Last Col Name: ' . $highestCol . '<br>';
        echo 'Last Col Index: ' . $highestColIndex . '<br>';

        foreach ($inputFileNames as $inputFileName) {
            $filePath = storage_path("app/public/roro-sheets/$inputFileName");
            echo $filePath . '<br>';
            $spreadsheet = $reader->load($filePath);

            $sheetName = 'Sheet1';
            $clonedWorksheet = clone $spreadsheet->getSheetByName($sheetName);

            if ($duplicateSheet = $spreadsheetMain->getSheetByName($sheetName)) {
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
        $time_start = microtime(true);

        $inputFileType = 'Xlsx';
        $sheetnames = [
            'Sheet1'
        ];

        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetnames);

        $mainFileName = 'a.xlsx';
        $mainFilePath = storage_path("app/public/roro-sheets/$mainFileName");
        $mainSpreadsheet = $reader->load($mainFilePath);

        $mainWorksheet = $mainSpreadsheet->getActiveSheet();
        $mainHighestRow = $mainWorksheet->getHighestRow();

        $inputFileName = 'b.xlsx';
        $inputFilePath = storage_path("app/public/roro-sheets/$inputFileName");
        $inputSpreadsheet = $reader->load($inputFilePath);

        $inputWorksheet = $inputSpreadsheet->getActiveSheet();
        $inputHighestRow = $inputWorksheet->getHighestRow();

        $inputCellValues = $inputWorksheet->rangeToArray(
            'A2:A' . $inputHighestRow, // The worksheet range that we want to retrieve
            null,         // Value that should be returned for empty cells
            false,  // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            false,      // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            false      // Should the array be indexed by cell row and cell column
        );

        $mainWorksheet->fromArray($inputCellValues, null, 'A' . ($mainHighestRow + 1));

        $writer = new Xlsx($mainSpreadsheet);
        $writer->save($mainFilePath);

        echo 'total memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB <br>';
        echo 'peak memory usage: ' . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB <br>';

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = (microtime(true) - $time_start) * 1000;
        echo 'time usage: ' . round($execution_time, 2) . ' ms<br>';
    }

    public function mergeExternalInOne_test()
    {
        $time_start = microtime(true);

        $headerRows = 13;
        $footerRows = 13;
        $dataRowsStart = $headerRows + 1;

        $inputFileType = 'Xlsx';
        $sheetnames = [
            'Sheet1'
        ];

        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetnames);

        $mainFileName = 'a.xlsx';
        $mainFilePath = storage_path("app/public/roro-sheets/$mainFileName");
        $mainSpreadsheet = $reader->load($mainFilePath);

        $mainWorksheet = $mainSpreadsheet->getActiveSheet();
        $mainHighestRow = $mainWorksheet->getHighestRow();

        $inputFileName = 'Book_Yes.xlsx';
        $inputFilePath = storage_path("app/public/roro-sheets/$inputFileName");
        $inputSpreadsheet = $reader->load($inputFilePath);

        $inputWorksheet = $inputSpreadsheet->getActiveSheet();
        $inputHighestRow = $inputWorksheet->getHighestRow();
        $dataRowsEnd = $inputHighestRow - $footerRows - 1;

        $inputCellValues = $inputWorksheet->rangeToArray(
            "A$dataRowsStart:G$dataRowsEnd", // The worksheet range that we want to retrieve
            null,         // Value that should be returned for empty cells
            true,  // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            true,      // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            false      // Should the array be indexed by cell row and cell column
        );

        $mainWorksheet->fromArray($inputCellValues, null, 'A' . ($mainHighestRow + 1));

        $writer = new Xlsx($mainSpreadsheet);
        $writer->save($mainFilePath);

        echo 'total memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB <br>';
        echo 'peak memory usage: ' . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB <br>';

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = (microtime(true) - $time_start) * 1000;
        echo 'time usage: ' . round($execution_time, 2) . ' ms<br>';
    }

    public function mergeExternalInOne_styled()
    {
        $time_start = microtime(true);

        $headerRows = 13;
        $footerRows = 13;
        $dataRowsStart = $headerRows + 1;

        $inputFileType = 'Xlsx';
        $sheetNames = [
            'Sheet1'
        ];

        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetNames);

        $mainFileName = 'b.xlsx';
        $mainFilePath = storage_path("app/public/roro-sheets/$mainFileName");
        $mainSpreadsheet = $reader->load($mainFilePath);

        $mainWorksheet = $mainSpreadsheet->getActiveSheet();
        $mainHighestRow = $mainWorksheet->getHighestRow();

        $inputFileName = 'Book_Yes.xlsx';
        $inputFilePath = storage_path("app/public/roro-sheets/$inputFileName");
        $inputSpreadsheet = $reader->load($inputFilePath);

        $inputWorksheet = $inputSpreadsheet->getActiveSheet();
        $inputHighestRow = $inputWorksheet->getHighestRow();
        $dataRowsEnd = $inputHighestRow - $footerRows - 1;

        $this->copyRowsWithStyle($inputWorksheet, "A$dataRowsStart:G$dataRowsEnd", "A$mainHighestRow", $mainWorksheet);

        $writer = new Xlsx($mainSpreadsheet);
        $writer->save($mainFilePath);

        echo 'total memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB <br>';
        echo 'peak memory usage: ' . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB <br>';

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = (microtime(true) - $time_start) * 1000;
        echo 'time usage: ' . round($execution_time, 2) . ' ms<br>';
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function copyRowsWithStyle(Worksheet $sheet, $srcRange, $dstCell, Worksheet $destSheet = null): void
    {
        if (!isset($destSheet)) {
            $destSheet = $sheet;
        }

        if (!preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch)) {
            // Invalid src range
            return;
        }

        if (!preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch)) {
            // Invalid dest cell
            return;
        }

        [1 => $srcColumnStart, 2 => $srcRowStart, 3 => $srcColumnEnd, 4 => $srcRowEnd] = $srcRangeMatch;
        [1 => $destColumnStart, 2 => $destRowStart] = $destCellMatch;

        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd);
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);

        $wsExt = new WorksheetDrawingExt($sheet);

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $destSheet->setCellValue($dstCell, $cell->getValue());

                // $style = $sheet->getStyleByColumnAndRow($col, $row);
                // $destSheet->duplicateStyle($style, $dstCell);

                $styleArray = $sheet->getStyle($cell->getCoordinate())->exportArray();
                $destSheet->getStyle($dstCell)->applyFromArray($styleArray);

                // Image insert code start
                $drawings = $wsExt->drawingsForCoordinate($cell->getCoordinate());
                foreach ($drawings as $d) {
                    $zipReader = fopen($d->getPath(),'r');
                    $imageContents = '';
                    while (!feof($zipReader)) {
                        $imageContents .= fread($zipReader,1024);
                    }
                    fclose($zipReader);

                    // Add a drawing to the worksheet
                    $drawing = new MemoryDrawing();
                    $drawing->setName('Sample image');
                    $drawing->setDescription('Sample image');
                    $drawing->setImageResource(imagecreatefromstring($imageContents));
                    $drawing->setRenderingFunction(MemoryDrawing::RENDERING_DEFAULT);
                    $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
                    $drawing->setCoordinates($dstCell);
                    $drawing->setWorksheet($destSheet);
                }
                // Image insert code end

                // Set width of column, but only once per column
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $destSheet->getColumnDimensionByColumn($destColumnStart + $colCount)->setAutoSize(false);
                    $destSheet->getColumnDimensionByColumn($destColumnStart + $colCount)->setWidth($w);
                }

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $destSheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                $targetRowStart = $destRowStart + $relativeRowStart;
                $targetRowEnd = $destRowStart + $relativeRowEnd;

                $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                //Merge target cells
                $destSheet->mergeCells($merge);
            }
        }
    }
}

class WorksheetDrawingExt
{
    private $idx = [];

    /**
     * Upon construction will build an index drawings and their locations
     */
    public function __construct(Worksheet $ws)
    {
        $this->createDrawingIndex($ws);
    }

    private function addDrawingToCell($coord, $drawing)
    {
        if (array_key_exists($coord, $this->idx)) {
            //There's already one image here - append a new
            $this->idx[$coord][] = $drawing;
        } else {
            //No images so far, setup the base array
            $this->idx[$coord] = [$drawing];
        }
    }

    private function createDrawingIndex($ws)
    {
        $drawings = $ws->getDrawingCollection();
        foreach ($drawings as $drawing) {
            $coord = $drawing->getCoordinates();//Inconsistent plural!
            $this->addDrawingToCell($coord, $drawing);
        }
    }

    /**
     * Get all drawings for a cell (always returns an array even if there's 1 or less images)
     */
    public function drawingsForCell(Cell $cell): array
    {
        return $this->drawingsForCoordinate($cell->getCoordinate());
    }

    /**
     * Get all drawings at the given coordinate (always returns an array even if there's 1 or less images)
     */
    public function drawingsForCoordinate(string $coordinate): array
    {
        if (array_key_exists($coordinate, $this->idx)) {
            return $this->idx[$coordinate];
        }

        return [];
    }
}
