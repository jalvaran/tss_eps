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
    
    public function GenerarAnexoActaLiquidacionXFacturas1($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta) {
        
        require_once('../../librerias/Excel/PHPExcel2.php');
        //$db=$DatosIPS["DataBase"];
        $objPHPExcel = new Spreadsheet();
        
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
                'size' => 14
            ]
        ];

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleName);

        $CmbIPS=$DatosIPS["NIT"];
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo=$this->DevuelveValores("contratos_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
                
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Nombre] CON IPS")
             
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
        
        
        if($TipoConsulta==1){
            $Tabla="actas_conciliaciones_items";
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,TotalPagos,
                    (TotalCopagos+TotalAnticipos) as TotalNotasCopagos,DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                    TotalDevoluciones,ValorSegunEPS as Saldo                  
                    FROM $db.$Tabla WHERE                    
                      ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato) ";
        }    
        if($TipoConsulta==2){
            $Tabla="actas_liquidaciones_items";
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    NumeroFactura,ValorDocumento,Impuestos,TotalPagos,
                    (TotalCopagos+TotalAnticipos) as TotalNotasCopagos,DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                    TotalDevoluciones,ValorSegunEPS as Saldo                  
                    FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                  
                      ";
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
        $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]; 
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
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"])
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
            $z=$z+2;
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
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
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
    header('Content-Disposition: attachment;filename="'."Anexo_Liquidacion_XFacturas_$idActaLiquidacion".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
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
                'size' => 14
            ]
        ];

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleName);

        $CmbIPS=$DatosIPS["NIT"];
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $DatosContratoTipo=$this->DevuelveValores("contratos_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
                
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","REPORTE DE LIQUIDACIÓN DE CONTRATOS POR $DatosContratoTipo[Nombre] CON IPS")
             
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
        
        
        if($TipoConsulta==1){
            $Tabla="actas_conciliaciones_items";
            $Condicion=" WHERE ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato) GROUP BY NumeroRadicado,MesServicio,NumeroContrato";
            
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,SUM(ValorDocumento) AS ValorDocumento,
                                SUM(Impuestos) AS Impuestos,SUM(TotalPagos) AS TotalPagos,SUM(TotalCopagos+TotalAnticipos) AS TotalNotasCopagos,
                                SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                                SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                                SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo
                                
                                FROM $db.$Tabla $Condicion";
            
            
        }    
        
        if($TipoConsulta==2){
            $Tabla="actas_liquidaciones_radicados_items";
            
            $Condicion=" WHERE idActaLiquidacion='$idActaLiquidacion' GROUP BY NumeroRadicado,MesServicio,NumeroContrato";
            $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,SUM(ValorDocumento) AS ValorDocumento,
                                SUM(Impuestos) AS Impuestos,SUM(TotalPagos) AS TotalPagos,SUM(TotalCopagos+TotalAnticipos) AS TotalNotasCopagos,
                                SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                                SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                                SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo
                                
                                FROM $db.$Tabla $Condicion";
            
            
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
        $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]; 
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
            ->setCellValue($Campos[$z++].$i,$DatosVista["TotalGlosaFavor"])
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
            $z=$z+2;
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
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
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
    header('Content-Disposition: attachment;filename="'."Anexo_Liquidacion_XFacturas_$idActaLiquidacion".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    
    
   //Fin Clases
}
    