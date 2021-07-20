<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhpExcelProcess{
  private $reportWriter;
  private $reader;
  public $filePath;
  private $type;

  public function __construct()
  {

  }

  public function createWriter(){
    $writer = new Xlsx($spreadsheet);
    $writer->save('hello world.xlsx');
  }

  public function createReader(){
    $this->reader = ReaderFactory::create($this->type);
    return $this->reader;
  }

  public function writeRow($row)
  {
    $this->reportWriter->addRow($row);
  }

  public function writeHeaderRow($headerRow)
  {
    $headerStyle = (new StyleBuilder())->setFontBold()->build();
    $this->reportWriter->addRowWithStyle($headerRow, $headerStyle);
  }

  public function close()
  {
    $this->reportWriter->close();
  }
}