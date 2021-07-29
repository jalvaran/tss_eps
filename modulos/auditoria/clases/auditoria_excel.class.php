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
    
    public function auditoria_hoja_trabajo($CmbIPS,$hoja_trabajo_id) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $nombre_hoja1="Hoja de Trabajo";        
        
        
        $objPHPExcel = new Spreadsheet();
        
        $objPHPExcel->getActiveSheet()->setTitle($nombre_hoja1);
            
        
        //Resumen de actas de liquidacion en la hoja 1
        
        $objPHPExcel->getActiveSheet()->getStyle('F:Z')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            
        ];
                
        $Campos=[ 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","GESTIÓN LIQUIDACIÓN - CARGUE DE ACTAS")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:D1');
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A3","HOJA DE TRABAJO")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:B3');
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleTitle);
        $z=0;
        $i=7;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"DPTO RADICACION")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS") 
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")    
            ;
        /*
        $objPHPExcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($styleTitle);
        $sql="SELECT nombre_liquidador,COUNT(ID) as total_actas  
                FROM vista_informe_liquidaciones_tags 
                WHERE FechaRegistro>='$FechaInicial' and FechaRegistro<='$FechaFinal' and Estado=0 
                GROUP BY nombre_liquidador ORDER BY total_actas DESC";
        $Consulta=$this->Query($sql);
        $z=0;
        $total_actas=0;
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $total_actas=$total_actas+$datos_consulta["total_actas"];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,$datos_consulta["nombre_liquidador"])
            ->setCellValue($Campos[1].$i,$datos_consulta["total_actas"])
                              
            ;
        }
        
         
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"TOTAL")
            ->setCellValue($Campos[1].$i,$total_actas)
                              
            ;
        
        $objPHPExcel->getActiveSheet()->getStyle("A3:M3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth('45');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth('30');
        
        
        
        
        $i=3;
        $z=0;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja2)
            ->setCellValue($Campos[$z++].$i,"LIQUIDADOR")    
            ->setCellValue($Campos[$z++].$i,"ID DEL ACTA")
            ->setCellValue($Campos[$z++].$i,"IDENTIFICADOR ASMET")
            ->setCellValue($Campos[$z++].$i,"NIT IPS")
            ->setCellValue($Campos[$z++].$i,"RAZON SOCIAL IPS")
            ->setCellValue($Campos[$z++].$i,"TIPO DE ACTA")
            ->setCellValue($Campos[$z++].$i,"MES DE SERVICIO INICIAL")
            ->setCellValue($Campos[$z++].$i,"MES DE SERVICIO FINAL")
            ->setCellValue($Campos[$z++].$i,"FECHA DE FIRMA")
            ->setCellValue($Campos[$z++].$i,"FECHA DE REGISTRO")
            ->setCellValue($Campos[$z++].$i,"ESTADO")
            ->setCellValue($Campos[$z++].$i,"CONTRATOS")
            ;
        
        $sql="SELECT * FROM vista_informe_liquidaciones_tags 
               WHERE FechaRegistro>='$FechaInicial' and FechaRegistro<='$FechaFinal' 
               ORDER BY nombre_liquidador ASC
               ";
        $Consulta=$this->Query($sql);
        $z=0;
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $nombre_estado="";
            if($datos_consulta["Estado"]==0){
                $nombre_estado="ABIERTA";
            }
            if($datos_consulta["Estado"]==1){
                $nombre_estado="CERRADA";
            }
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja2)
            ->setCellValue($Campos[$z++].$i,$datos_consulta["nombre_liquidador"])        
            ->setCellValue($Campos[$z++].$i,$datos_consulta["ID"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["IdentificadorActaEPS"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["NIT_IPS"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["RazonSocialIPS"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["tipo_acta"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["MesServicioInicial"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["MesServicioFinal"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["FechaFirma"])
            ->setCellValue($Campos[$z++].$i,$datos_consulta["FechaRegistro"])
            
            ->setCellValue($Campos[$z++].$i,$nombre_estado)
            ->setCellValue($Campos[$z++].$i,$datos_consulta["contratos"])
                              
            ;
            
            $z=0;
        }
        
        $i=1;
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A3:L3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('30');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('40');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('27');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('16');
         * 
         */
        
     
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
    header('Content-Disposition: attachment;filename="'."HojaTrabajoAuditoria".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    
   //Fin Clases
}
    