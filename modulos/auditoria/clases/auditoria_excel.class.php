<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel general
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class ExcelReportes extends conexion{
    
    // Clase para generar excel de un balance de comprobacion
    
    public function auditoria_hoja_trabajo_evento($CmbIPS,$hoja_trabajo_id) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $nombre_hoja1="Hoja de Trabajo";        
        $datos_ips=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $db=$datos_ips["DataBase"];
        
        $objPHPExcel = new Spreadsheet();
        
        $objPHPExcel->getActiveSheet()->setTitle($nombre_hoja1);
            
        
        //Resumen de actas de liquidacion en la hoja 1
        
        $objPHPExcel->getActiveSheet()->getStyle('F:AO')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 10
            ]
            
        ];
                
        $Campos=[ 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $datos_columnas=$this->ShowColums("$db.auditoria_hoja_de_trabajo_evento");
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","GESTIÓN LIQUIDACIÓN - CARGUE DE ACTAS")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A3","HOJA DE TRABAJO")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:N3');
        $objPHPExcel->getActiveSheet()->getStyle('A1:AO3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A5:AO5')->applyFromArray($styleTitle);
        
        $z=0;
        $i=5;
        
        foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
            if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync')
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($Campos[$z++].$i, strtoupper(str_replace("_"," ",$NombreColumna))) 
            ;
        }
                
        $sql="SELECT * FROM  $db.auditoria_hoja_de_trabajo_evento WHERE hoja_trabajo_id='$hoja_trabajo_id' ";
        $Consulta=$this->Query($sql);
        $sumatorias=[];
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $z=0;
            foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
                if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync'){
                    if(!isset($sumatorias[$NombreColumna])){
                        $sumatorias[$NombreColumna]=0;
                    }
                    if(is_numeric($datos_consulta[$NombreColumna])){
                        $sumatorias[$NombreColumna]=$sumatorias[$NombreColumna]+$datos_consulta[$NombreColumna];
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($Campos[$z++].$i, $datos_consulta[$NombreColumna]) 
                    ;
                }     
            }
        }
        
        $i++;
        $z=-1;
        
        foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
            if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync'){
                $z=$z+1;
                if(isset($sumatorias[$NombreColumna]) and $NombreColumna<>'contrato' and $NombreColumna<>'departamento_radicacion' and $NombreColumna<>'radicado'  and $NombreColumna<>'mes_servicio' and $NombreColumna<>'factura'){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($Campos[$z].$i, $sumatorias[$NombreColumna]) 
                    ;
                }
            }
            
        }
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, "TOTALES"); 
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);               
                   
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AO'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A5:AO5")->getAlignment()->setWrapText(true);
       
        for($i=1;$i<=41;$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth('18');
        }
       //fin detalles conciliacion
        
  // $objPHPExcel->setActiveSheetIndexByName($nombre_hoja1);     
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Hoja de Trabajo Auditoria")
        ->setSubject("TAGS")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("TAGS");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."HojaTrabajoAuditoria_$hoja_trabajo_id".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
     public function auditoria_hoja_trabajo_pgp($CmbIPS,$hoja_trabajo_id) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $nombre_hoja1="Hoja de Trabajo";        
        $datos_ips=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $db=$datos_ips["DataBase"];
        
        $objPHPExcel = new Spreadsheet();
        
        $objPHPExcel->getActiveSheet()->setTitle($nombre_hoja1);
            
        
        //Resumen de actas de liquidacion en la hoja 1
        
        $objPHPExcel->getActiveSheet()->getStyle('F:AO')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 10
            ]
            
        ];
                
        $Campos=[ 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $datos_columnas=$this->ShowColums("$db.auditoria_hoja_de_trabajo_pgp");
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","GESTIÓN LIQUIDACIÓN - CARGUE DE ACTAS")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A3","HOJA DE TRABAJO")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:N3');
        $objPHPExcel->getActiveSheet()->getStyle('A1:AO3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A5:AO5')->applyFromArray($styleTitle);
        
        $z=0;
        $i=5;
        
        foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
            if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync')
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($Campos[$z++].$i, strtoupper(str_replace("_"," ",$NombreColumna))) 
            ;
        }
                
        $sql="SELECT * FROM  $db.auditoria_hoja_de_trabajo_pgp WHERE hoja_trabajo_id='$hoja_trabajo_id' ";
        $Consulta=$this->Query($sql);
        $sumatorias=[];
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $z=0;
            foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
                if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync'){
                    if(!isset($sumatorias[$NombreColumna])){
                        $sumatorias[$NombreColumna]=0;
                    }
                    if(is_numeric($datos_consulta[$NombreColumna])){
                        $sumatorias[$NombreColumna]=$sumatorias[$NombreColumna]+$datos_consulta[$NombreColumna];
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($Campos[$z++].$i, $datos_consulta[$NombreColumna]) 
                    ;
                }     
            }
        }
        
        $i++;
        $z=-1;
        
        foreach ($datos_columnas["Field"] as $key => $NombreColumna) {
            if($NombreColumna<>'hoja_trabajo_id' and $NombreColumna<>'updated_ts' and $NombreColumna<>'Sync'){
                $z=$z+1;
                if(isset($sumatorias[$NombreColumna]) and $NombreColumna<>'contrato' and $NombreColumna<>'departamento_radicacion' and $NombreColumna<>'radicado'  and $NombreColumna<>'mes_servicio' and $NombreColumna<>'factura'){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($Campos[$z].$i, $sumatorias[$NombreColumna]) 
                    ;
                }
            }
            
        }
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, "TOTALES"); 
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);               
                   
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AO'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A5:AO5")->getAlignment()->setWrapText(true);
       
        for($i=1;$i<=41;$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth('18');
        }
       //fin detalles conciliacion
        
  // $objPHPExcel->setActiveSheetIndexByName($nombre_hoja1);     
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Hoja de Trabajo Auditoria")
        ->setSubject("TAGS")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("TAGS");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."HojaTrabajoAuditoria_$hoja_trabajo_id".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
   //Fin Clases
}
    