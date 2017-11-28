<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 2017/9/14
 * Time: 21:06
 */

namespace app\common\controller;
use think\Controller;

class Excel extends Controller
{
    static public function export_excel1($sheet_title,$col_title,$col_width,$data)
     {
        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Writer.Excel2007.php');
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($sheet_title); //给当前活动sheet设置名称

         foreach ($col_title as $key=>$value){
             $PHPSheet->setCellValue($key,$value);
         }
         foreach ($col_width as $key=>$value){
             $PHPSheet->getColumnDimension($key)->setWidth($value);
         }

         $row = 2;
         foreach ($data as $key=>$value){


             $PHPSheet->setCellValue("A".$row,$value['id']);
             $PHPSheet->setCellValue("B".$row,$value['snap_time']);
             $PHPSheet->setCellValue("C".$row,$value['vhost_name']);
             $PHPSheet->setCellValue("D".$row,$value['is_active']);
             $PHPSheet->setCellValue("E".$row,$value['is_normal']);
             $row++;
         }


        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$sheet_title. '.xlsx"');
        header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

   static function export_excel($sheet_title,$col_title,$col_width,$data){
        ini_set('max_execution_time', '0');
        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Writer.Excel2007.php');
        $filename=str_replace('.xls', '', $sheet_title).'.xls';
        $phpexcel = new \PHPExcel();
        $phpexcel->getProperties()
            ->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
//        $phpexcel->getActiveSheet()->fromArray($data);

       $PHPSheet = $phpexcel->getActiveSheet();
       foreach ($col_title as $key=>$value){
           $PHPSheet->setCellValue($key,$value);
       }
       foreach ($col_width as $key=>$value){
           $PHPSheet->getColumnDimension($key)->setWidth($value);
       }
       $row = 2;
       foreach ($data as $key=>$value){

           $PHPSheet->setCellValue("A".$row,$value['id']);
           $PHPSheet->setCellValue("B".$row,$value['snap_time']);
           $PHPSheet->setCellValue("C".$row,$value['is_normal']);
           $row++;
       }
        $phpexcel->getActiveSheet()->setTitle('Sheet1');
        $phpexcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$sheet_title");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objwriter = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
        $objwriter->save('php://output');
        exit;
    }


}