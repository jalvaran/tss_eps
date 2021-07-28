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
    
    public function informe_gestion($FechaInicial,$FechaFinal,$CmbEstado,$CmbProyectosTicketsListado,$CmbModulosTicketsListado,$CmbTiposTicketsListado,$usuario_id) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $nombre_hoja1="Resumen Liquidaciones";        
        $nombre_hoja2="Detalles Actas liq.";
        $nombre_hoja5="Resumen Conciliaciones";
        $nombre_hoja6="Detalles Actas conc.";
        $nombre_hoja3="Resumen Tickets";
        $nombre_hoja4="Detalles Tickets";
        
        $objPHPExcel = new Spreadsheet();
        
        $objPHPExcel->getActiveSheet()->setTitle($nombre_hoja1);
        $hoja2 = $objPHPExcel->createSheet();
        $hoja2->setTitle($nombre_hoja2);
        
        $hoja5 = $objPHPExcel->createSheet();
        $hoja5->setTitle($nombre_hoja5);
        
        $hoja6 = $objPHPExcel->createSheet();
        $hoja6->setTitle($nombre_hoja6);
        
        $hoja3 = $objPHPExcel->createSheet();
        $hoja3->setTitle($nombre_hoja3);
        
        $hoja4 = $objPHPExcel->createSheet();
        $hoja4->setTitle($nombre_hoja4);
        
        
        
        //Resumen de actas de liquidacion en la hoja 1
        
        $objPHPExcel->getActiveSheet()->getStyle('H:K')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            
        ];
                
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","INFORME DE GESTION DE SOPORTE Y USO DEL TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:D1');
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A3","ACTAS DE LIQUIDACIÓN ABIERTAS ENTRE EL $FechaInicial Y $FechaFinal")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:B3');
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleTitle);
        $z=0;
        $i=4;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"LIQUIDADOR")
            ->setCellValue($Campos[$z++].$i,"TOTAL ACTAS")
                              
            ;
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
        
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E3","ACTAS DE LIQUIDACIÓN CERRADAS ENTRE EL $FechaInicial Y $FechaFinal")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E3:F3');
        
        $objPHPExcel->getActiveSheet()->getStyle('E3:F3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('E1:F1')->applyFromArray($styleTitle);
        
        $i=4;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[4].$i,"LIQUIDADOR")
            ->setCellValue($Campos[5].$i,"TOTAL ACTAS")
                              
            ;
        $objPHPExcel->getActiveSheet()->getStyle('E4:F4')->applyFromArray($styleTitle);
        $sql="SELECT nombre_liquidador,COUNT(ID) as total_actas  
                FROM vista_informe_liquidaciones_tags 
                WHERE FechaFirma>='$FechaInicial' and FechaFirma<='$FechaFinal' and Estado=1 
                GROUP BY nombre_liquidador ORDER BY total_actas DESC";
        $Consulta=$this->Query($sql);
        $total_actas=0;
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $total_actas=$total_actas+$datos_consulta["total_actas"];
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[4].$i,$datos_consulta["nombre_liquidador"])
            ->setCellValue($Campos[5].$i,$datos_consulta["total_actas"])
                              
            ;
        }
        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[4].$i,"TOTAL")
            ->setCellValue($Campos[5].$i,$total_actas)
                              
            ;
        $objPHPExcel->getActiveSheet()->getStyle("E3:F3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(5)->setWidth('45');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6)->setWidth('30');
        
        
        //Detalles Actas de liquidacion
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja2)
            ->setCellValue("A1","DETALLE DE ACTAS REALIZADAS POR EL TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        
        
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
        
        
     //Resumen de Tickets
        
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja3)
            ->setCellValue("A1","RESUMEN DE TICKETS EN TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        $Condicion="";
        if($CmbEstado>0){
            $Condicion.=" AND Estado='$CmbEstado' ";
        }
        if($CmbProyectosTicketsListado>0){
            $Condicion.=" AND idProyecto='$CmbProyectosTicketsListado' ";
        }
        if($CmbModulosTicketsListado>0){
            $Condicion.=" AND idModuloProyecto='$CmbModulosTicketsListado' ";
        }
        if($CmbTiposTicketsListado>0){
            $Condicion.=" AND TipoTicket='$CmbTiposTicketsListado' ";
        }
        if($usuario_id>0){
            $Condicion.=" AND idUsuarioAsignado='$usuario_id' ";
        }
        
        $i=3;
        $z=0;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja3)
            ->setCellValue($Campos[$z++].$i,"FECHA DE SOLICITUD")    
            ->setCellValue($Campos[$z++].$i,"FECHA DE RESPUESTA")
            ->setCellValue($Campos[$z++].$i,"USUARIO QUE SOLICITA")
            ->setCellValue($Campos[$z++].$i,"MODULO")
            ->setCellValue($Campos[$z++].$i,"TIPO DE TICKET")
            ->setCellValue($Campos[$z++].$i,"ESTADO")
            ->setCellValue($Campos[$z++].$i,"TOTAL")
            ->setCellValue($Campos[$z++].$i,"PROMEDIO RESPUESTA (Hrs)")    
            ->setCellValue($Campos[$z++].$i,"PROMEDIO RESPUESTA (Días)")
            ;
        
        $sql="SELECT *             
                FROM vista_informe_tickets_tags  
                WHERE FechaApertura>='$FechaInicial' and FechaApertura<='$FechaFinal' $Condicion
                
               ";
        $Consulta=$this->Query($sql);
        $total_tickets=0;
        while ($datos_consulta = $this->FetchAssoc($Consulta)) {
            $i++;
            $z=0;
            
            $dias_promedio_respuesta=round($datos_consulta["horas_respuesta"]/24,2);
            $total_tickets=$total_tickets+1;
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja3)
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["FechaApertura"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["fecha_primer_respuesta"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreSolicitante"]." ".$datos_consulta["ApellidoSolicitante"]))
                   
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreModulo"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreTipoTicket"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreEstado"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode('1'))
                ->setCellValue($Campos[$z++].$i,utf8_encode(round($datos_consulta["horas_respuesta"],2)))
                
                ->setCellValue($Campos[$z++].$i,utf8_encode(round($dias_promedio_respuesta,2)))    

                ;
        }
        $i++;
        $z=0;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja3)
            ->setCellValue($Campos[$z++].$i,"")    
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"")    
            ->setCellValue($Campos[$z++].$i,"")    
            ->setCellValue($Campos[$z++].$i,"")
            ->setCellValue($Campos[$z++].$i,"TOTAL")
            ->setCellValue($Campos[$z++].$i,$total_tickets)
            
            ;
        $i=1;
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A3:I3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('21');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('21');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('42');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('21');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('15');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('7');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i++)->setWidth('12');
        //Detalle de los tickets
        
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja4)
            ->setCellValue("A1","TICKETS EN TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        $Condicion="";
        if($CmbEstado>0){
            $Condicion.=" AND Estado='$CmbEstado' ";
        }
        if($CmbProyectosTicketsListado>0){
            $Condicion.=" AND idProyecto='$CmbProyectosTicketsListado' ";
        }
        if($CmbModulosTicketsListado>0){
            $Condicion.=" AND idModuloProyecto='$CmbModulosTicketsListado' ";
        }
        if($CmbTiposTicketsListado>0){
            $Condicion.=" AND TipoTicket='$CmbTiposTicketsListado' ";
        }
        if($usuario_id>0){
            $Condicion.=" AND idUsuarioAsignado='$usuario_id' ";
        }
        
        $i=3;
        $z=0;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja4)
            ->setCellValue($Campos[$z++].$i,"ID") 
            ->setCellValue($Campos[$z++].$i,"FECHA DE APERTURA")    
            ->setCellValue($Campos[$z++].$i,"USUARIO QUE ABRE")    
             
            ->setCellValue($Campos[$z++].$i,"ASUNTO")
            ->setCellValue($Campos[$z++].$i,"ESTADO")
              
            ->setCellValue($Campos[$z++].$i,"MODULO")
            ->setCellValue($Campos[$z++].$i,"TIPO DE TICKET")
            ->setCellValue($Campos[$z++].$i,"RESPUESTA (Hrs)")
            
            ->setCellValue($Campos[$z++].$i,"RESPUESTA (DIAS)")
            ->setCellValue($Campos[$z++].$i,"DETALLE DEL TICKET")
            
            ;
        
        $sql="SELECT t1.* 
            
                FROM vista_informe_tickets_tags t1 
                WHERE FechaApertura>='$FechaInicial' and FechaApertura<='$FechaFinal' $Condicion
                
               ";
        $Consulta=$this->Query($sql);
        
        while ($datos_consulta = $this->FetchAssoc($Consulta)) {
            $i++;
            $z=0;
            $dias_respuesta=round($datos_consulta["horas_respuesta"]/24,2);
            $mensaje=strip_tags(utf8_encode($datos_consulta["mensajes"]));
            $mensaje= str_replace("&nbsp;", " ", $mensaje);
            //$horas_respuesta=$this->calcule_diferencia_fechas( $datos_consulta["FechaApertura"],$datos_consulta["fecha_primer_respuesta"]);
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja4)
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["ID"]))  
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["FechaApertura"]))      
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreSolicitante"]." ".$datos_consulta["ApellidoSolicitante"]))       
                
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["Asunto"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreEstado"]))
                
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreModulo"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode($datos_consulta["NombreTipoTicket"]))
                ->setCellValue($Campos[$z++].$i,utf8_encode(round($datos_consulta["horas_respuesta"],2)))
                
                ->setCellValue($Campos[$z++].$i,utf8_encode($dias_respuesta))   
                ->setCellValue($Campos[$z++].$i,utf8_encode($mensaje))   
                ;
            
            
        }
        $c=1;
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A3:Q3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("J4:J1000")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('A3:Q3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('8');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('18');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('40');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('56');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('21');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('15');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($c++)->setWidth('90');
        
   
        
        
   // resumen conciliaciones
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5);
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue("A1","INFORME DE GESTION DE SOPORTE Y USO DEL TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)->mergeCells('A1:D1');
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue("A3","ACTAS DE CONCILIACIÓN ABIERTAS ENTRE EL $FechaInicial Y $FechaFinal")
             
                ;
       $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)->mergeCells('A3:B3');
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleTitle);
        $z=0;
        $i=4;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[$z++].$i,"LIQUIDADOR")
            ->setCellValue($Campos[$z++].$i,"TOTAL ACTAS")
                              
            ;
        $objPHPExcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($styleTitle);
        $sql="SELECT nombre_liquidador,COUNT(ID) as total_actas  
                FROM vista_informe_conciliaciones_tags 
                WHERE FechaRegistro>='$FechaInicial' and FechaRegistro<='$FechaFinal' and Estado=0 
                GROUP BY nombre_liquidador ORDER BY total_actas DESC";
        $Consulta=$this->Query($sql);
        $z=0;
        $total_actas=0;
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $total_actas=$total_actas+$datos_consulta["total_actas"];
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[0].$i,$datos_consulta["nombre_liquidador"])
            ->setCellValue($Campos[1].$i,$datos_consulta["total_actas"])
                              
            ;
        }
        
        $i++;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[0].$i,"TOTAL")
            ->setCellValue($Campos[1].$i,$total_actas)
                              
            ;
        
        $objPHPExcel->getActiveSheet()->getStyle("A3:M3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth('45');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth('30');
        
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue("E3","ACTAS DE CONCILIACIÓN CERRADAS ENTRE EL $FechaInicial Y $FechaFinal")
             
                ;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)->mergeCells('E3:F3');
        
        $objPHPExcel->getActiveSheet()->getStyle('E3:F3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('E1:F1')->applyFromArray($styleTitle);
        
        $i=4;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[4].$i,"LIQUIDADOR")
            ->setCellValue($Campos[5].$i,"TOTAL ACTAS")
                              
            ;
        $objPHPExcel->getActiveSheet()->getStyle('E4:F4')->applyFromArray($styleTitle);
        $sql="SELECT nombre_liquidador,COUNT(ID) as total_actas  
                FROM vista_informe_liquidaciones_tags 
                WHERE FechaFirma>='$FechaInicial' and FechaFirma<='$FechaFinal' and Estado=1 
                GROUP BY nombre_liquidador ORDER BY total_actas DESC";
        $Consulta=$this->Query($sql);
        $total_actas=0;
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            $i++;
            $total_actas=$total_actas+$datos_consulta["total_actas"];
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[4].$i,$datos_consulta["nombre_liquidador"])
            ->setCellValue($Campos[5].$i,$datos_consulta["total_actas"])
                              
            ;
        }
        $i++;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja5)
            ->setCellValue($Campos[4].$i,"TOTAL")
            ->setCellValue($Campos[5].$i,$total_actas)
                              
            ;
        $objPHPExcel->getActiveSheet()->getStyle("E3:F3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(5)->setWidth('45');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6)->setWidth('30');
        
        
        //Fin resumen conciliaciones
        
        
        //Detalles Actas de concilacion
        
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja6)
            ->setCellValue("A1","DETALLE DE ACTAS REALIZADAS POR EL TAGS PERIODO: $FechaInicial - $FechaFinal")
             
                ;
        
        
        $i=3;
        $z=0;
        $objPHPExcel->setActiveSheetIndexByName($nombre_hoja6)
            ->setCellValue($Campos[$z++].$i,"LIQUIDADOR")    
            ->setCellValue($Campos[$z++].$i,"ID DEL ACTA")
            
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
        
        $sql="SELECT * FROM vista_informe_conciliaciones_tags 
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
            $objPHPExcel->setActiveSheetIndexByName($nombre_hoja6)
            ->setCellValue($Campos[$z++].$i,$datos_consulta["nombre_liquidador"])        
            ->setCellValue($Campos[$z++].$i,$datos_consulta["ID"])
            
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
        
        
       //fin detalles conciliacion
        
   $objPHPExcel->setActiveSheetIndexByName($nombre_hoja1);     
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Informe de gestion")
        ->setSubject("TAGS")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("TAGS");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."InformeGestion_$FechaInicial-$FechaFinal".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    function calcule_diferencia_fechas($fecha_inicial,$fecha_final) {
        $fecha1 = new DateTime($fecha_inicial);//fecha inicial
        $fecha2 = new DateTime($fecha_final);//fecha final

        $intervalo = $fecha1->diff($fecha2);
        $dias=$intervalo->format('%d');
        $total_horas=$dias*24;
        $horas=$intervalo->format('%H');
        $minutos=$intervalo->format('%i');
        $total_horas=$total_horas+$horas+round($minutos/59,1);
        
        return($total_horas);
    }
    
   //Fin Clases
}
    