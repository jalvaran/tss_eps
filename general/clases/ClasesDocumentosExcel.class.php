<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel.
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}

class TS_Excel extends conexion{
    
    // Clase para generar excel de un balance de comprobacion
    
    public function GenerarFormatoConciliacionesMasivas($db,$CmbIPS) {
        require_once('../../librerias/Excel/PHPExcel2.php');
        
        $objPHPExcel = new Spreadsheet();
        
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $z=0;
        $i=1;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"IPS")
            ->setCellValue($Campos[$z++].$i,"NumeroFactura")
            ->setCellValue($Campos[$z++].$i,"FechaFactura")
            ->setCellValue($Campos[$z++].$i,"NumeroRadicado")
            ->setCellValue($Campos[$z++].$i,"FechaRadicado")
            ->setCellValue($Campos[$z++].$i,"NumeroContrato")
            ->setCellValue($Campos[$z++].$i,"ValorDocumento")
            ->setCellValue($Campos[$z++].$i,"ValorMenosImpuestos")
            ->setCellValue($Campos[$z++].$i,"MesServicio")
            ->setCellValue($Campos[$z++].$i,"Impuestos")
            ->setCellValue($Campos[$z++].$i,"OtrosDescuentos")
            ->setCellValue($Campos[$z++].$i,"ImpuestosSegunASMET")
            ->setCellValue($Campos[$z++].$i,"TotalPagos")
            ->setCellValue($Campos[$z++].$i,"TotalAnticipos")
            ->setCellValue($Campos[$z++].$i,"TotalGlosaInicial")
            ->setCellValue($Campos[$z++].$i,"TotalGlosaFavor")
            ->setCellValue($Campos[$z++].$i,"TotalGlosaContra")
            ->setCellValue($Campos[$z++].$i,"TotalCopagos")
            ->setCellValue($Campos[$z++].$i,"TotalDevoluciones")
            ->setCellValue($Campos[$z++].$i,"GlosaXConciliar")
            ->setCellValue($Campos[$z++].$i,"ValorSegunEPS")
                
            ->setCellValue($Campos[$z++].$i,"ValorSegunIPS")
            ->setCellValue($Campos[$z++].$i,"Diferencia")
            ->setCellValue($Campos[$z++].$i,"CarteraXEdades")
            
            ->setCellValue($Campos[$z++].$i,"ConciliacionAFavorDe")
            ->setCellValue($Campos[$z++].$i,"Observacion")
            ->setCellValue($Campos[$z++].$i,"ValorConciliacion")
            ->setCellValue($Campos[$z++].$i,"TotalConciliaciones")
            ;
            
        $sql="SELECT * FROM $db.hoja_de_trabajo WHERE Estado=0 AND Diferencia<>0";
        $Consulta=$this->Query($sql);
        $i=1;
        while($DatosVista= $this->FetchAssoc($Consulta)){
            $z=0;
            $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,$CmbIPS)
            ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroFactura"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["FechaFactura"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroRadicado"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["FechaRadicado"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroContrato"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["ValorDocumento"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["ValorMenosImpuestos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["MesServicio"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["Impuestos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["OtrosDescuentos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["ImpuestosSegunASMET"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalPagos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalAnticipos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaInicial"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaContra"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalCopagos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalDevoluciones"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["GlosaXConciliar"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["ValorSegunEPS"])
                
            ->setCellValue($Campos[$z++].$i,$DatosVista["ValorSegunIPS"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["Diferencia"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["CarteraXEdades"])
            
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalConciliaciones"])
            
            ;
        }
        
   
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Formato conciliacion masiva")
        ->setSubject("Formato")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("Formato conciliacion masiva");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."Base_Conciliacion_$CmbIPS".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    
    
   //Fin Clases
}
    