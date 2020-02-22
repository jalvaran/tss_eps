<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel.
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

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
            ->setCellValue($Campos[$z++].$i,$DatosVista["ImpuestosCalculados"])
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
    
    public function GenerarAnexoActaLiquidacionXFacturas1($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta) {
        
        require_once('../../librerias/Excel/PHPExcel2.php');
        //$db=$DatosIPS["DataBase"];
        $objPHPExcel = new Spreadsheet();
        $TipoActa=$DatosActa["TipoActaLiquidacion"];
        $DatosActaTipo=$this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
        $Encabezado= utf8_encode($DatosActaTipo["Header"]);
        $Footer= utf8_encode($DatosActaTipo["Footer"]);
        $objPHPExcel->getActiveSheet()->getStyle('E:N')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle("A:N")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddHeader("$Encabezado");
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddFooter("&C".$Footer. '&RPágina &P of &N');
        /*
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
        $drawing->setName('PhpSpreadsheet logo');
        $drawing->setPath('../../LogosEmpresas/logoAsmet.png');
        $drawing->setHeight(36);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);
        */
        $styleName = [
        
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleName);

        $CmbIPS=$DatosIPS["NIT"];
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo= ($this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]));
                
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Titulo]")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        
        $z=0;
        $i=3;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"IPS")
            ->setCellValue($Campos[1].$i++,$DatosIPS["Nombre"])
            ->setCellValue($Campos[0].$i,"NIT")
            ->setCellValue($Campos[1].$i,$DatosIPS["NIT"])    
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[1].$i)->getNumberFormat()->setFormatCode('#');
        $i--;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        
        $sql="SELECT t1.ID,t1.NombreContrato AS Contrato,t1.FechaInicial as FechaInicioContrato,t1.FechaFinal  as FechaFinalContrato,t1.Valor as ValorContrato
                FROM actas_liquidaciones_contratos t1 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion'";
        //print($sql);
        $Consulta= $this->Query($sql);
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i++,"Contrato:")
            ->setCellValue($Campos[0].$i++,"Vigencia (Inicio):")
            ->setCellValue($Campos[0].$i++,"Vigencia (Fin):")
            ->setCellValue($Campos[0].$i++,"Valor Contrato:")    
                ;
        
        $i=$i-4;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $i=$i-4;
        $z=1;
        $flagTipoContrato=0;
        while($DatosContratos= $this->FetchAssoc($Consulta)){
            /*
            if($flagTipoContrato==0){
                $flagTipoContrato=1;
                $TipoContrato=$DatosContratos["TipoContrato"];
            }
            
             * 
             */
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($Campos[$z].$i++,$DatosContratos["Contrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaInicioContrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaFinalContrato"])
                ->setCellValue($Campos[$z].$i,$DatosContratos["ValorContrato"])      
                    ;
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->getNumberFormat()->setFormatCode('#,##0');
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->applyFromArray($styleTitle);
            $z++;
            $i=$i-3;
        }
        $i=$i+4;
        $TipoContrato="";
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"Modalidad:")
            ->setCellValue($Campos[1].$i,$TipoContrato)
            
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[0].$i)->applyFromArray($styleTitle);
        $z=0;
        $i++;
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"DPTO RADICACION")
            ->setCellValue($Campos[$z++].$i,"RADICADO")
            ->setCellValue($Campos[$z++].$i,"MES DE SERVICIOS")
            ->setCellValue($Campos[$z++].$i,"FACTURA")
            ->setCellValue($Campos[$z++].$i,"VALOR FACTURADO")
            ->setCellValue($Campos[$z++].$i,"RETENCION IMPUESTOS")
            ->setCellValue($Campos[$z++].$i,"DEVOLUCION")
            ->setCellValue($Campos[$z++].$i,"GLOSA")
            ->setCellValue($Campos[$z++].$i,"GLOSA A FAVOR ASMET")
            ->setCellValue($Campos[$z++].$i,"NOTA CREDITO / COPAGOS")
            ->setCellValue($Campos[$z++].$i,"RECUPERACION EN IMPUESTOS")
            ->setCellValue($Campos[$z++].$i,"OTROS DESCUENTOS")
            ->setCellValue($Campos[$z++].$i,"VALOR PAGADO")
            ->setCellValue($Campos[$z++].$i,"SALDO")
            
            ;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->getAlignment()->setWrapText(true);
        $z=0;
        $i++;
        if($TipoActa==3){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            
            ;
        }
        if($TipoConsulta==1 and $TipoActa<>3){
            $Tabla="actas_conciliaciones_items";
            $TablaUnion="historial_carteracargada_eps";
            $GroupOrder="ORDER BY MesServicio,NumeroFactura ";
                        
            $Union=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo                  
                    FROM $db.$TablaUnion t1 WHERE 
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                    AND NOT EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t2.NumeroRadicado = t1.NumeroRadicado)  
                        

                       ";
            
            $Union2=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion, t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo                  
                    FROM $db.$TablaUnion t1 WHERE  NOT 
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                      

                       ";
            $Union3=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo,'0' as GlosaXConciliar                        
                    FROM $db.$TablaUnion t1 WHERE NOT
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                                        

                       ";
                 // print($Union2) ; 
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,(TotalPagos + TotalAnticipos) AS TotalPagos,
                    (TotalCopagos) as TotalNotasCopagos,DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                    TotalDevoluciones,ValorSegunEPS as Saldo ,GlosaXConciliar                       
                    FROM $db.$Tabla WHERE                    
                      ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato AND t2.idActaLiquidacion='$idActaLiquidacion')
                    
                      ";
            $sql.=$Union3.$GroupOrder;
            //print($sql);
        }    
        if($TipoConsulta==2 and $TipoActa<>3){
            $Tabla="actas_liquidaciones_items";
            $TablaUnion="historial_carteracargada_eps";
            $GroupOrder="ORDER BY MesServicio,NumeroFactura ";
            $Union=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo                  
                    FROM $db.$TablaUnion t1 WHERE 
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                    AND NOT EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t2.NumeroRadicado = t1.NumeroRadicado)  
                        

                       ";
            $Union2=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo                  
                    FROM $db.$TablaUnion t1 WHERE NOT 
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                      

                       ";
            
            $Union3=" UNION ALL     
                    
                    SELECT t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,
                    t1.NumeroContrato,t1.NumeroFactura,t1.ValorOriginal as ValorDocumento,'0' as Impuestos,'0' AS TotalPagos,
                    '0' as TotalNotasCopagos,'0' as DescuentoPGP,'0' as DescuentoBDUA,'0' as TotalOtrosDescuentos,'0' as TotalGlosaInicial,'0' as TotalGlosaFavor,
                    t1.ValorOriginal as TotalDevoluciones,'0' as Saldo,,'0' as GlosaXConciliar                 
                    FROM $db.$TablaUnion t1 WHERE NOT
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                                        

                       ";
            
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    NumeroFactura,ValorDocumento,Impuestos,(TotalPagos + TotalAnticipos) as TotalPagos,
                    (TotalCopagos) as TotalNotasCopagos,DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                    TotalDevoluciones,ValorSegunEPS as Saldo,GlosaXConciliar                  
                    FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                  
                      ";
            
            $sql.=$Union3.$GroupOrder;
        } 
        //print($sql);
        if($TipoActa<>3){
            $Consulta=$this->Query($sql);
        }
        $Totales["ValorDocumento"]=0;
        $Totales["Impuestos"]=0;
        $Totales["TotalDevoluciones"]=0;
        $Totales["TotalGlosaInicial"]=0;
        $Totales["TotalGlosaFavor"]=0;
        $Totales["TotalNotasCopagos"]=0;
        $Totales["TotalOtrosDescuentos"]=0;
        $Totales["TotalPagos"]=0;
        $Totales["Saldo"]=0;
        if($TipoActa<>3){
            while($DatosVista= $this->FetchAssoc($Consulta)){
                $z=0;
                $i++;

            $Totales["ValorDocumento"]=$Totales["ValorDocumento"]+$DatosVista["ValorDocumento"]; 
            $Totales["Impuestos"]=$Totales["Impuestos"]+$DatosVista["Impuestos"]; 
            $Totales["TotalDevoluciones"]=$Totales["TotalDevoluciones"]+$DatosVista["TotalDevoluciones"]; 
            $Totales["TotalGlosaInicial"]=$Totales["TotalGlosaInicial"]+$DatosVista["TotalGlosaInicial"]; 
            $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]+$DatosVista["GlosaXConciliar"]; 
            $Totales["TotalNotasCopagos"]=$Totales["TotalNotasCopagos"]+$DatosVista["TotalNotasCopagos"]; 
            $Totales["TotalOtrosDescuentos"]=$Totales["TotalOtrosDescuentos"]+$DatosVista["TotalOtrosDescuentos"];
            $Totales["TotalPagos"]=$Totales["TotalPagos"]+$DatosVista["TotalPagos"]; 
            $Totales["Saldo"]=$Totales["Saldo"]+$DatosVista["Saldo"]; 

            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,$DatosVista["DepartamentoRadicacion"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroRadicado"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["MesServicio"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroFactura"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["ValorDocumento"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Impuestos"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalDevoluciones"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaInicial"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"]+$DatosVista["GlosaXConciliar"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalNotasCopagos"])
                ->setCellValue($Campos[$z++].$i,0)
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalOtrosDescuentos"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalPagos"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Saldo"])

                ;
            }
        }    
        $i++;
        $z=0;
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"TOTAL")
            ->setCellValue($Campos[$z++].$i,$Totales["ValorDocumento"])
            ->setCellValue($Campos[$z++].$i,$Totales["Impuestos"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalDevoluciones"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaInicial"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaFavor"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalNotasCopagos"])
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,$Totales["TotalOtrosDescuentos"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalPagos"])
            ->setCellValue($Campos[$z++].$i,$Totales["Saldo"])
                        
            ; 
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
         
        if($DatosActa["PagosPendientesPorLegalizar"]<>0){
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"PAGOS PENDIENTES POR LEGALIZAR")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,$DatosActa["PagosPendientesPorLegalizar"])

                ; 
            $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
            
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"SALDO FINAL")
                ->setCellValue($Campos[$z++].$i,$Totales["Saldo"]-$DatosActa["PagosPendientesPorLegalizar"])

                ; 
            $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
         
        }
         
        $i=$i+5;
        $z=0;
        $Consulta=$this->ConsultarTabla("actas_liquidaciones_firmas", "WHERE idActaLiquidacion='$idActaLiquidacion'");
        while($DatosFirmas=$this->FetchAssoc($Consulta)){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z].$i++,"_________________________________________________")
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Nombre"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Cargo"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Empresa"])
            
            ;
            $i=$i-4;
            $z=$z+4;
        }
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        for($i=1;$i<=14;$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth('18');
        }
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setTop(0.75);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setRight(0.2);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setLeft(1);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setBottom(0.75);
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
    header('Content-Disposition: attachment;filename="'."Anexo_Liquidacion_XFacturas_$idActaLiquidacion".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
   
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    //$idUser=$_SESSION["idUser"];
    //$Ruta='../../exports/acta_liquidacion_'.$idActaLiquidacion.'_tempo'.$idUser.'.xlsx';
    //$objWriter->save($Ruta);
    
    exit; 
   
    }
    
    
    public function GenerarAnexoActaLiquidacionXRadicados1($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta) {
        
        require_once('../../librerias/Excel/PHPExcel2.php');
        //$db=$DatosIPS["DataBase"];
        $objPHPExcel = new Spreadsheet();
        
        $DatosActaTipo=$this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
        $Encabezado= utf8_encode($DatosActaTipo["Header"]);
        $Footer= utf8_encode($DatosActaTipo["Footer"]);
        $objPHPExcel->getActiveSheet()->getStyle('D:N')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle("A:N")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddHeader("$Encabezado");
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddFooter("&C".$Footer. '&RPágina &P of &N');
        /*
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
        $drawing->setName('PhpSpreadsheet logo');
        $drawing->setPath('../../LogosEmpresas/logoAsmet.png');
        $drawing->setHeight(36);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);
        */
        $styleName = [
        
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleName);

        $CmbIPS=$DatosIPS["NIT"];
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo= ($this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]));
                
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Titulo]")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        
        $z=0;
        $i=3;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"IPS")
            ->setCellValue($Campos[1].$i++,$DatosIPS["Nombre"])
            ->setCellValue($Campos[0].$i,"NIT")
            ->setCellValue($Campos[1].$i,$DatosIPS["NIT"])    
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[1].$i)->getNumberFormat()->setFormatCode('#');
        $i--;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        
        $sql="SELECT t1.ID,t2.Contrato,t2.TipoContrato,t2.FechaInicioContrato,t2.FechaFinalContrato,t2.ValorContrato
                             FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente 
                             WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$CmbIPS'";
        //print($sql);
        $Consulta= $this->Query($sql);
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i++,"Contrato:")
            ->setCellValue($Campos[0].$i++,"Vigencia (Inicio):")
            ->setCellValue($Campos[0].$i++,"Vigencia (Fin):")
            ->setCellValue($Campos[0].$i++,"Valor Contrato:")    
                ;
        
        $i=$i-4;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $i=$i-4;
        $z=1;
        $flagTipoContrato=0;
        while($DatosContratos= $this->FetchAssoc($Consulta)){
            if($flagTipoContrato==0){
                $flagTipoContrato=1;
                $TipoContrato=$DatosContratos["TipoContrato"];
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($Campos[$z].$i++,$DatosContratos["Contrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaInicioContrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaFinalContrato"])
                ->setCellValue($Campos[$z].$i,$DatosContratos["ValorContrato"])      
                    ;
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->getNumberFormat()->setFormatCode('#,##0');
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->applyFromArray($styleTitle);
            $z++;
            $i=$i-3;
        }
        $i=$i+4;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"Modalidad:")
            ->setCellValue($Campos[1].$i,$TipoContrato)
            
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[0].$i)->applyFromArray($styleTitle);
        $z=0;
        $i++;
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"DPTO RADICACION")
            ->setCellValue($Campos[$z++].$i,"RADICADO")
            ->setCellValue($Campos[$z++].$i,"MES DE SERVICIOS")
            
            ->setCellValue($Campos[$z++].$i,"VALOR FACTURADO")
            ->setCellValue($Campos[$z++].$i,"RETENCION IMPUESTOS")
            ->setCellValue($Campos[$z++].$i,"DEVOLUCION")
            ->setCellValue($Campos[$z++].$i,"GLOSA")
            ->setCellValue($Campos[$z++].$i,"GLOSA A FAVOR ASMET")
            ->setCellValue($Campos[$z++].$i,"NOTA CREDITO / COPAGOS")
            ->setCellValue($Campos[$z++].$i,"RECUPERACION EN IMPUESTOS")
            ->setCellValue($Campos[$z++].$i,"OTROS DESCUENTOS")
            ->setCellValue($Campos[$z++].$i,"VALOR PAGADO")
            ->setCellValue($Campos[$z++].$i,"SALDO")
            
            ;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->getAlignment()->setWrapText(true);
        
        if($TipoConsulta==1){
            $Tabla="actas_conciliaciones_items";
            $Condicion=" WHERE ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t5 WHERE t5.idContrato=$Tabla.NumeroContrato AND t5.idActaLiquidacion='$idActaLiquidacion') ";
            $GroupOrder=" GROUP BY NumeroRadicado,MesServicio,NumeroContrato ORDER BY MesServicio,NumeroRadicado ";
            $TablaUnion="historial_carteracargada_eps";
            
            $Union3="     
                     SELECT  t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,t1.NumeroContrato,
                        SUM(t1.ValorOriginal) AS ValorDocumento,
                        '0' AS Impuestos,'0' AS TotalPagos,'0' AS TotalNotasCopagos,
                        '0' AS DescuentoPGP,'0' AS DescuentoBDUA,'0' AS TotalOtrosDescuentos,
                        '0' AS TotalGlosaInicial,'0' AS TotalGlosaFavor,
                        SUM(t1.ValorOriginal) AS TotalDevoluciones,'0' AS Saldo,'0' as GlosaXConciliar 

                        FROM $db.$TablaUnion t1 WHERE NOT
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado )
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                                           
                          ";
                        
            $sql=" UNION ALL SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,NumeroContrato,SUM(ValorDocumento) AS ValorDocumento,
                        SUM(Impuestos) AS Impuestos,SUM(TotalPagos + TotalAnticipos) AS TotalPagos,SUM(TotalCopagos) AS TotalNotasCopagos,
                        SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                        SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                        SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo,SUM(GlosaXConciliar) AS GlosaXConciliar 

                        FROM $db.$Tabla $Condicion";
            $sql=$Union3.$sql.$GroupOrder;
            
            //print($sql);
            
        }    
        
        if($TipoConsulta==2){
            $Tabla="actas_liquidaciones_radicados_items";
            $TablaUnion="historial_carteracargada_eps";
            $Condicion=" WHERE idActaLiquidacion='$idActaLiquidacion' ";
            $GroupOrder=" GROUP BY NumeroRadicado,MesServicio,NumeroContrato ORDER BY MesServicio,NumeroRadicado ";
            $sql=" UNION ALL
                
            SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,SUM(ValorDocumento) AS ValorDocumento,
                                SUM(Impuestos) AS Impuestos,SUM(TotalPagos + TotalAnticipos) AS TotalPagos,SUM(TotalCopagos) AS TotalNotasCopagos,
                                SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                                SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                                SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo,sum(GlosaXConciliar) as GlosaXConciliar
                                
                                FROM $db.$Tabla $Condicion ";
                      
            $Union3="     
                     SELECT  t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,t1.NumeroContrato,
                        SUM(t1.ValorOriginal) AS ValorDocumento,
                        '0' AS Impuestos,'0' AS TotalPagos,'0' AS TotalNotasCopagos,
                        '0' AS DescuentoPGP,'0' AS DescuentoBDUA,'0' AS TotalOtrosDescuentos,
                        '0' AS TotalGlosaInicial,'0' AS TotalGlosaFavor,
                        SUM(t1.ValorOriginal) AS TotalDevoluciones,'0' AS Saldo,'0' AS GlosaXConciliar

                        FROM $db.$TablaUnion t1 WHERE NOT
                    EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado )
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                                           
                          ";
            
            $sql=$Union3.$sql.$GroupOrder;
            
        }    
        //print($sql);
        $Consulta=$this->Query($sql);
        $Totales["ValorDocumento"]=0;
        $Totales["Impuestos"]=0;
        $Totales["TotalDevoluciones"]=0;
        $Totales["TotalGlosaInicial"]=0;
        $Totales["TotalGlosaFavor"]=0;
        $Totales["TotalNotasCopagos"]=0;
        $Totales["TotalOtrosDescuentos"]=0;
        $Totales["TotalPagos"]=0;
        $Totales["Saldo"]=0;
        while($DatosVista= $this->FetchAssoc($Consulta)){
            $z=0;
            $i++;
            
        $Totales["ValorDocumento"]=$Totales["ValorDocumento"]+$DatosVista["ValorDocumento"]; 
        $Totales["Impuestos"]=$Totales["Impuestos"]+$DatosVista["Impuestos"]; 
        $Totales["TotalDevoluciones"]=$Totales["TotalDevoluciones"]+$DatosVista["TotalDevoluciones"]; 
        $Totales["TotalGlosaInicial"]=$Totales["TotalGlosaInicial"]+$DatosVista["TotalGlosaInicial"]; 
        $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]+$DatosVista["GlosaXConciliar"]; 
        $Totales["TotalNotasCopagos"]=$Totales["TotalNotasCopagos"]+$DatosVista["TotalNotasCopagos"]; 
        $Totales["TotalOtrosDescuentos"]=$Totales["TotalOtrosDescuentos"]+$DatosVista["TotalOtrosDescuentos"];
        $Totales["TotalPagos"]=$Totales["TotalPagos"]+$DatosVista["TotalPagos"]; 
        $Totales["Saldo"]=$Totales["Saldo"]+$DatosVista["Saldo"]; 
        
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[$z++].$i,$DatosVista["DepartamentoRadicacion"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroRadicado"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["MesServicio"])
            
            ->setCellValue($Campos[$z++].$i,$DatosVista["ValorDocumento"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["Impuestos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalDevoluciones"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaInicial"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"]+$DatosVista["GlosaXConciliar"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalNotasCopagos"])
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalOtrosDescuentos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalPagos"])
            ->setCellValue($Campos[$z++].$i,$DatosVista["Saldo"])
                        
            ;
        }
        $i++;
        $z=0;
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            
            ->setCellValue($Campos[$z++].$i,"TOTAL")
            ->setCellValue($Campos[$z++].$i,$Totales["ValorDocumento"])
            ->setCellValue($Campos[$z++].$i,$Totales["Impuestos"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalDevoluciones"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaInicial"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaFavor"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalNotasCopagos"])
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,$Totales["TotalOtrosDescuentos"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalPagos"])
            ->setCellValue($Campos[$z++].$i,$Totales["Saldo"])
                        
            ; 
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        if($DatosActa["PagosPendientesPorLegalizar"]<>0){
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                
                ->setCellValue($Campos[$z++].$i,"PAGOS PENDIENTES POR LEGALIZAR")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,$DatosActa["PagosPendientesPorLegalizar"])

                ; 
            $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
            
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                ->setCellValue($Campos[$z++].$i,"")
                
                ->setCellValue($Campos[$z++].$i,"SALDO FINAL")
                ->setCellValue($Campos[$z++].$i,$Totales["Saldo"]-$DatosActa["PagosPendientesPorLegalizar"])

                ; 
            $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
         
        } 
        $i=$i+5;
        $z=0;
        $Consulta=$this->ConsultarTabla("actas_liquidaciones_firmas", "WHERE idActaLiquidacion='$idActaLiquidacion'");
        while($DatosFirmas=$this->FetchAssoc($Consulta)){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z].$i++,"_________________________________________________")
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Nombre"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Cargo"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Empresa"])
            
            ;
            $i=$i-4;
            $z=$z+4;
        }
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        for($i=1;$i<=14;$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth('18');
        }
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setTop(0.75);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setRight(0.2);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setLeft(1);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setBottom(0.75);
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
    header('Content-Disposition: attachment;filename="'."Anexo_Liquidacion_XRadicados_$idActaLiquidacion".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    public function GenerarAnexoActaLiquidacionXFacturasCapita($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta) {
        
        require_once('../../librerias/Excel/PHPExcel2.php');
        //$db=$DatosIPS["DataBase"];
        $objPHPExcel = new Spreadsheet();
        
        $DatosActaTipo=$this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
        $TipoActa=$DatosActa["TipoActaLiquidacion"];
        $Encabezado= utf8_encode($DatosActaTipo["Header"]);
        $Footer= utf8_encode($DatosActaTipo["Footer"]);
        $objPHPExcel->getActiveSheet()->getStyle('H:N')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle("A:N")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddHeader("$Encabezado");
        $objPHPExcel->getActiveSheet()->getHeaderFooter()
            ->setOddFooter("&C".$Footer. '&RPágina &P of &N');
        /*
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
        $drawing->setName('PhpSpreadsheet logo');
        $drawing->setPath('../../LogosEmpresas/logoAsmet.png');
        $drawing->setHeight(36);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);
        */
        $styleName = [
        
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleName);

        $CmbIPS=$DatosIPS["NIT"];
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo=$this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
                
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Titulo] CON IPS")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
        
        $z=0;
        $i=3;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"IPS")
            ->setCellValue($Campos[1].$i++,$DatosIPS["Nombre"])
            ->setCellValue($Campos[0].$i,"NIT")
            ->setCellValue($Campos[1].$i,$DatosIPS["NIT"])    
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[1].$i)->getNumberFormat()->setFormatCode('#');
        $i--;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');
        
        $sql="SELECT t1.ID,t2.Contrato,t2.TipoContrato,t2.FechaInicioContrato,t2.FechaFinalContrato,t2.ValorContrato
                             FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente 
                             WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$CmbIPS'";
        //print($sql);
        $Consulta= $this->Query($sql);
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i++,"Contrato:")
            ->setCellValue($Campos[0].$i++,"Vigencia (Inicio):")
            ->setCellValue($Campos[0].$i++,"Vigencia (Fin):")
            ->setCellValue($Campos[0].$i++,"Valor Contrato:")    
                ;
        
        $i=$i-4;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i++)->applyFromArray($styleTitle);
        $i=$i-4;
        $z=1;
        $flagTipoContrato=0;
        while($DatosContratos= $this->FetchAssoc($Consulta)){
            if($flagTipoContrato==0){
                $flagTipoContrato=1;
                $TipoContrato=$DatosContratos["TipoContrato"];
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($Campos[$z].$i++,$DatosContratos["Contrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaInicioContrato"])
                ->setCellValue($Campos[$z].$i++,$DatosContratos["FechaFinalContrato"])
                ->setCellValue($Campos[$z].$i,$DatosContratos["ValorContrato"])      
                    ;
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->getNumberFormat()->setFormatCode('#,##0');
            $objPHPExcel->getActiveSheet()->getStyle($Campos[$z].$i)->applyFromArray($styleTitle);
            $z++;
            $i=$i-3;
        }
        $i=$i+4;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"Modalidad:")
            ->setCellValue($Campos[1].$i,$TipoContrato)
            
                ;
        $objPHPExcel->getActiveSheet()->getStyle($Campos[0].$i)->applyFromArray($styleTitle);
        $i++;
        $sql="SELECT t3.PorcentajePoblacional,t3.ValorPercapitaXDia,
                (SELECT Ciudad FROM municipios_dane t4 WHERE t3.CodigoDane=t4.CodigoDane LIMIT 1) as Municipio 
                             FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente
                             INNER JOIN contrato_percapita t3 ON t2.Contrato=t3.Contrato                             
                             WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$CmbIPS'";
        //print($sql);
        $Consulta= $this->Query($sql);
        while($DatosPercapita= $this->FetchAssoc($Consulta)){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[0].$i,"Municipio:")
            ->setCellValue($Campos[1].$i++,$DatosPercapita["Municipio"])
            ->setCellValue($Campos[0].$i,"Valor percapita día:")
            ->setCellValue($Campos[1].$i++,$DatosPercapita["ValorPercapitaXDia"])      
             
                ;
            $objPHPExcel->getActiveSheet()->getStyle($Campos[1].($i-1))->getNumberFormat()->setFormatCode('#,##0');
            $objPHPExcel->setActiveSheetIndex(0)                 
            ->setCellValue($Campos[0].$i,"% Poblacional:")
            ->setCellValue($Campos[1].$i++,$DatosPercapita["PorcentajePoblacional"]."%")             
           
                ;
            
            
        }
        
        $z=0;
        $i++;
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"DEPARTAMENTO")
            ->setCellValue($Campos[$z++].$i,"MUNICIPIO")
            ->setCellValue($Campos[$z++].$i,"Mes LMA (AAAAMM)")
            ->setCellValue($Campos[$z++].$i,"DIAS RECONOCIDOS LMA")
            ->setCellValue($Campos[$z++].$i,"VR A PAGAR IPS SEGÚN LMA")
            ->setCellValue($Campos[$z++].$i,"No. RADICADO")
            ->setCellValue($Campos[$z++].$i,"No. FACTURA")
            ->setCellValue($Campos[$z++].$i,"VALOR FACTURADO")
            ->setCellValue($Campos[$z++].$i,"VALOR RETENIDO")
            ->setCellValue($Campos[$z++].$i,"Descuento o Reconocimiento por BDUA")
            ->setCellValue($Campos[$z++].$i,"DESCUENTO INICIAL")
            ->setCellValue($Campos[$z++].$i,"DESCUENTOS CONCILIADO A FAVOR ASMET")
            ->setCellValue($Campos[$z++].$i,"VALOR PAGADO")
            ->setCellValue($Campos[$z++].$i,"SALDO")
            
            ;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->getAlignment()->setWrapText(true); 
        if($TipoActa==6){
            $z=0;
        $i++;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            ->setCellValue($Campos[$z++].$i,0)
            
            ;
        }
        
        if($TipoConsulta==1 AND $TipoActa<>6){
            $Tabla="actas_conciliaciones_items";
            $sql="SELECT MesServicio,NumeroContrato, DepartamentoRadicacion,GROUP_CONCAT(NumeroRadicado) AS NumeroRadicado,CodigoSucursal AS CodigoDaneAnexo,
                    (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                    NumeroContrato,GROUP_CONCAT(NumeroFactura) as NumeroFactura,SUM(ValorDocumento) as ValorDocumento,SUM(Impuestos) AS Impuestos,SUM(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                    SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                    SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) as Saldo,SUM(NumeroDiasLMA) AS NumeroDiasLMA,SUM(ValorAPagarLMA) AS  ValorAPagarLMA                  
                    FROM $db.$Tabla WHERE                    
                      ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato AND t2.idActaLiquidacion='$idActaLiquidacion') 
                    GROUP BY  MesServicio,NumeroContrato,CodigoDaneAnexo ORDER BY MesServicio,CodigoDaneAnexo,NumeroFactura";
            
        }    
        if($TipoConsulta==2){
            $Tabla="actas_liquidaciones_items";
            /*
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                    NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                    DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                    TotalDevoluciones,ValorSegunEPS as Saldo,NumeroDiasLMA,ValorAPagarLMA                 
                    FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                  
                      ";
             * 
             */
            $sql="SELECT MesServicio,NumeroContrato,DepartamentoRadicacion,GROUP_CONCAT(NumeroRadicado) AS NumeroRadicado,CodigoSucursal AS CodigoDaneAnexo,
                    (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                    NumeroContrato,GROUP_CONCAT(NumeroFactura) as NumeroFactura,SUM(ValorDocumento) as ValorDocumento,SUM(Impuestos) AS Impuestos,SUM(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                    SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                    SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) as Saldo,SUM(NumeroDiasLMA) AS NumeroDiasLMA,SUM(ValorAPagarLMA) AS  ValorAPagarLMA                  
                    FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                
                      
                    GROUP BY  MesServicio,NumeroContrato,CodigoDaneAnexo ORDER BY MesServicio,CodigoDaneAnexo,NumeroFactura";
        } 
        //print($sql);
        if($TipoActa<>6){
            $Consulta=$this->Query($sql);
        }
        $Totales["ValorDocumento"]=0;
        $Totales["Impuestos"]=0;
        $Totales["TotalDevoluciones"]=0;
        $Totales["TotalGlosaInicial"]=0;
        $Totales["TotalGlosaFavor"]=0;
        //$Totales["TotalNotasCopagos"]=0;
        $Totales["TotalOtrosDescuentos"]=0;
        $Totales["TotalPagos"]=0;
        $Totales["Saldo"]=0;
        $Totales["ValorAPagarLMA"]=0;
        $Totales["DescuentoBDUA"]=0;
        if($TipoActa<>6){
            while($DatosVista= $this->FetchAssoc($Consulta)){
                $z=0;
                $i++;

            $Totales["ValorDocumento"]=$Totales["ValorDocumento"]+$DatosVista["ValorDocumento"]; 
            $Totales["Impuestos"]=$Totales["Impuestos"]+$DatosVista["Impuestos"]; 
            $Totales["TotalDevoluciones"]=$Totales["TotalDevoluciones"]+$DatosVista["TotalDevoluciones"]; 
            $Totales["TotalGlosaInicial"]=$Totales["TotalGlosaInicial"]+$DatosVista["TotalGlosaInicial"]; 
            $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]; 
            //$Totales["TotalNotasCopagos"]=$Totales["TotalNotasCopagos"]+$DatosVista["TotalNotasCopagos"]; 
            $Totales["TotalOtrosDescuentos"]=$Totales["TotalOtrosDescuentos"]+$DatosVista["TotalOtrosDescuentos"];
            $Totales["TotalPagos"]=$Totales["TotalPagos"]+$DatosVista["TotalPagos"]; 
            $Totales["Saldo"]=$Totales["Saldo"]+$DatosVista["Saldo"]; 
            $Totales["ValorAPagarLMA"]=$Totales["ValorAPagarLMA"]+$DatosVista["ValorAPagarLMA"]; 
            $Totales["DescuentoBDUA"]=$Totales["DescuentoBDUA"]+$DatosVista["DescuentoBDUA"]; 

            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,$DatosVista["DepartamentoRadicacion"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Municipio"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["MesServicio"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroDiasLMA"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["ValorAPagarLMA"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroRadicado"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["NumeroFactura"])  
                ->setCellValue($Campos[$z++].$i,$DatosVista["ValorDocumento"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Impuestos"])    
                ->setCellValue($Campos[$z++].$i,$DatosVista["DescuentoBDUA"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaInicial"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"])            
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalPagos"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Saldo"])


                ;
            }
        }    
        $i++;
        $z=0;
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"TOTAL")
            ->setCellValue($Campos[$z++].$i,$Totales["ValorAPagarLMA"]) 
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")    
            ->setCellValue($Campos[$z++].$i,$Totales["ValorDocumento"])
            ->setCellValue($Campos[$z++].$i,$Totales["Impuestos"])
            ->setCellValue($Campos[$z++].$i,$Totales["DescuentoBDUA"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaInicial"])
            ->setCellValue($Campos[$z++].$i,$Totales["TotalGlosaFavor"])
            
            ->setCellValue($Campos[$z++].$i,$Totales["TotalPagos"])
            ->setCellValue($Campos[$z++].$i,$Totales["Saldo"])
                        
            ; 
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
         
        $i++;
        $i++;
        $z=0;
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[11].$i,"VR A PAGAR IPS S/N LMA")
            ->setCellValue($Campos[13].$i++,$Totales["ValorAPagarLMA"])
            ->setCellValue($Campos[11].$i,"VALOR RETENCION DE IMPUESTOS")
            ->setCellValue($Campos[13].$i++,$Totales["Impuestos"])
            ->setCellValue($Campos[11].$i,"DESCUENTOS A FAVOR ASMET")
            ->setCellValue($Campos[13].$i++,$Totales["TotalGlosaFavor"])
            ->setCellValue($Campos[11].$i,"OTROS DESCUENTOS CONCILIADOS")
            ->setCellValue($Campos[13].$i++,$DatosActa["OtrosDescuentosConciliadosAfavor"])
            ->setCellValue($Campos[11].$i,"VALOR PAGADO")
            ->setCellValue($Campos[13].$i++,$Totales["TotalPagos"])
            ->setCellValue($Campos[11].$i,"PAGOS PENDINTES POR LEGALIZAR")
            ->setCellValue($Campos[13].$i++,$DatosActa["PagosPendientesPorLegalizar"])     
            ->setCellValue($Campos[11].$i,"SALDO")
            ->setCellValue($Campos[13].$i++,$Totales["Saldo"]-$DatosActa["OtrosDescuentosConciliadosAfavor"]-$DatosActa["PagosPendientesPorLegalizar"])
                        
            ; 
        $i=$i-7;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        
        
        
        $i=$i+5;
        $z=0;
        $Consulta=$this->ConsultarTabla("actas_liquidaciones_firmas", "WHERE idActaLiquidacion='$idActaLiquidacion'");
        while($DatosFirmas=$this->FetchAssoc($Consulta)){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z].$i++,"_________________________________________________")
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Nombre"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Cargo"])
            ->setCellValue($Campos[$z].$i++,$DatosFirmas["Empresa"])
            
            ;
            
            $i=$i-4;
            $FilaActual=$i;
            $CampoInicial=$Campos[$z].$FilaActual;
            $CampoFinal=$Campos[$z+2].$FilaActual;            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells($CampoInicial.':'.$CampoFinal);
            
            $FilaActual=$i++;
            $CampoInicial=$Campos[$z].$FilaActual;
            $CampoFinal=$Campos[$z+2].$FilaActual;            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells($CampoInicial.':'.$CampoFinal);
            
            $FilaActual=$i++;
            $CampoInicial=$Campos[$z].$FilaActual;
            $CampoFinal=$Campos[$z+2].$FilaActual;            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells($CampoInicial.':'.$CampoFinal);
            $FilaActual=$i++;
            $CampoInicial=$Campos[$z].$FilaActual;
            $CampoFinal=$Campos[$z+2].$FilaActual;            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells($CampoInicial.':'.$CampoFinal);
            
            $FilaActual=$i++;
            $CampoInicial=$Campos[$z].$FilaActual;
            $CampoFinal=$Campos[$z+2].$FilaActual;            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells($CampoInicial.':'.$CampoFinal);
            
            
            $i=$i-4;
            $z=$z+4;
        }
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        $i++;
        $objPHPExcel->getActiveSheet()->getStyle("A$i:N$i")->applyFromArray($styleTitle);
        for($i=1;$i<=20;$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth('18');
        }
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setTop(0.75);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setRight(0.2);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setLeft(1);
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setBottom(0.75);
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
    header('Content-Disposition: attachment;filename="'."Anexo_Liquidacion_XFacturas_$idActaLiquidacion".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    public function GenerarAnexoActaLiquidacionXFacturasSpout($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta) {
        require_once '../../librerias/spout/src/Spout/Autoloader/autoload.php';
        
        //$filePath="../../exports/acta_liquidacion_".$idActaLiquidacion.".xlsx";
        $fileName="acta_liquidacion_".$idActaLiquidacion.".xlsx";
        $DatosActaTipo=$this->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
        $Encabezado= utf8_encode($DatosActaTipo["Header"]);
        $Footer= utf8_encode($DatosActaTipo["Footer"]);
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo=$this->DevuelveValores("contratos_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
        
        
        $defaultStyle = (new StyleBuilder())
            ->setFontSize(8)
            ->build();
        
        $zebraBlackStyle = (new StyleBuilder())
            ->setBackgroundColor(Color::BLACK)
            ->setFontColor(Color::WHITE)
            ->setFontSize(10)
            ->build();

        $zebraWhiteStyle = (new StyleBuilder())
            ->setBackgroundColor(Color::WHITE)
            ->setFontColor(Color::BLACK)
            ->setFontItalic()
            ->build();  

        
        $writer = WriterEntityFactory::createXLSXWriter();
       
        //$writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser

        $cells = [
            WriterEntityFactory::createCell("REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Nombre]",$zebraBlackStyle),
            
        ];

        /** add a row at a time */
        $singleRow = WriterEntityFactory::createRow($cells);
        $writer->addRow($singleRow);
        
        /** Shortcut: add a row from an array of values */
        $sql="SELECT * FROM $db.actas_liquidaciones_items WHERE idActaLiquidacion='14'";
        $Consulta=$this->Query($sql);
        while($DatosItem= $this->FetchAssoc($Consulta)){
            
            $rowFromValues = WriterEntityFactory::createRowFromArray($DatosItem);
            $writer->addRow($rowFromValues);
        }

        $writer->close();
    }
    
   //Fin Clases
}
    