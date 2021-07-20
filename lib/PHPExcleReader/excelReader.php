<?php
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of excelReader
 *
 * @author anhbhv
 */
class excelReader {
    public static function readExcel($filePath){
        //echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
        try {
                $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        } catch(Exception $e) {
                die('Error loading file "'.pathinfo($filePath,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        return $sheetData;
    }
    
}

?>
