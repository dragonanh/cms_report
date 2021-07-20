<?php
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;

class SpoutProcess{
  private $reportWriter;
  private $reader;
  public $filePath;
  private $type;

  public function __construct($filePath, $type = Type::XLSX)
  {
    $this->filePath = $filePath;
    $this->type = $type;
  }

  public function setType($type){
    $this->type = $type;
  }

  public function getFilePath(){
    return $this->filePath;
  }

  public function createWriter(){
    $this->reportWriter = WriterFactory::create($this->type);
    $this->reportWriter->openToFile($this->filePath);
    return $this->reportWriter;
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