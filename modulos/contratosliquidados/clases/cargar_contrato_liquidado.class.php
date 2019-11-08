<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class CargarContratos extends conexion{
    
       
       
    public function RegistreArchivo($idEPS,$idIPS,$NombreArchivoContrato,$Ruta,$Extension,$Soporte,$idUser) {
        $Fecha=date("Y-m-d H:i:s");
        
        $Datos["NombreArchivo"]=$NombreArchivoContrato;        
        $Datos["Extension"]=$Extension;
        $Datos["Ruta"]=$Ruta;
        $Datos["Soporte"]=$Soporte;        
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        
        $sql=$this->getSQLInsert("control_cargue_contratos_liquidados", $Datos);
        $this->Query($sql);
        
    }
    
    public function RegistreEncabezadoContrato($CmbIPS,$CmbEPS,$NombreArchivo,$Ruta,$Soporte,$idUser) {
        clearstatcache();
        
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $db=$DatosIPS["DataBase"];
        $DatosContratos=$this->DevuelveValores("registro_liquidacion_contratos", "NombreArchivo", $NombreArchivo);
        if($DatosContratos["ID"]<>''){
            return($DatosContratos["ID"]);
        }
        require_once('../../../librerias/Excel/PHPExcel2.php');
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$Ruta;
             
        $objReader = IOFactory::createReader('Xlsx');
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                  
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        
        $objPHPExcel->setActiveSheetIndex(0);
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $z=2;
        $NIT=$objPHPExcel->getActiveSheet()->getCell('B4')->getCalculatedValue();
        if($NIT<>$CmbIPS){
            exit("E1;El archivo contiene los registros de una IPS diferente a la seleccionada, $NIT");
        }
        $RazonSocial=$objPHPExcel->getActiveSheet()->getCell('B3')->getCalculatedValue();
        $NumeroContrato=$objPHPExcel->getActiveSheet()->getCell('B5')->getCalculatedValue();
        
        $cell = $objPHPExcel->getActiveSheet()->getCell('B6');
        if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
            $FechaInicial=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('B6')->getValue());
            $FechaInicial=get_object_vars($FechaInicial);
            $FechaInicial = $FechaInicial["date"];

        }else{
            exit("E1;La Vigencia inicial no tiene un formato tipo fecha en la celda 'B6' ");
        }
        
        $cell = $objPHPExcel->getActiveSheet()->getCell('B7');
        if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
            $FechaFinal=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('B7')->getValue());
            $FechaFinal=get_object_vars($FechaFinal);
            $FechaFinal = $FechaFinal["date"];

        }else{
            exit("E1;La Vigencia Final no tiene un formato tipo fecha en la celda 'B7' ");
        }
                    
        
        $ValorContrato=$objPHPExcel->getActiveSheet()->getCell('B8')->getCalculatedValue();
        $Modalidad=$objPHPExcel->getActiveSheet()->getCell('B9')->getCalculatedValue();
        $ValorPercapita=$objPHPExcel->getActiveSheet()->getCell('B10')->getCalculatedValue();
        $PorcentajePoblacional=$objPHPExcel->getActiveSheet()->getCell('B11')->getCalculatedValue();
        $Datos["NitIPS"]=$NIT;
        $Datos["RazonSocialIPS"]=$RazonSocial;
        $Datos["Contrato"]=$NumeroContrato;
        $Datos["VigenciaInicial"]=$FechaInicial;
        $Datos["VigenciaFinal"]=$FechaFinal;
        $Datos["ValorContrato"]=$ValorContrato;
        $Datos["Modalidad"]=$Modalidad;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaActual;
        $Datos["NombreArchivo"]=$NombreArchivo;
        $Datos["Soporte"]=$Soporte;
        $Datos["BaseDatos"]=$db;
        $Datos["ValorPercapita"]=$ValorPercapita;
        $Datos["PorcentajePoblacional"]=$PorcentajePoblacional;
        $sql=$this->getSQLInsert("registro_liquidacion_contratos", $Datos);
        $this->Query($sql);
        $idContrato=$this->ObtenerMAX("registro_liquidacion_contratos", "ID", 1, "");
        
        $z=3;
        $Sale=0;
        while($Sale==0){
            $NumeroOtroSI=$objPHPExcel->getActiveSheet()->getCell($Cols[$z].'5')->getCalculatedValue();
            $OtroSI=$NumeroContrato."-".$NumeroOtroSI;
            if($NumeroOtroSI==''){
                $Sale=1;
                break;
            }
            $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$z].'6');
            if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                $FechaInicial=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell($Cols[$z].'6')->getValue());
                $FechaInicial=get_object_vars($FechaInicial);
                $FechaInicial = $FechaInicial["date"];

            }else{
                exit("E1;La Vigencia inicial no tiene un formato tipo fecha en la celda 'B6' ");
            }

            $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$z].'7');
            if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                $FechaFinal=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell($Cols[$z].'7')->getValue());
                $FechaFinal=get_object_vars($FechaFinal);
                $FechaFinal = $FechaFinal["date"];

            }else{
                exit("E1;La Vigencia Final no tiene un formato tipo fecha en la celda 'B7' ");
            }


            $ValorContrato=$objPHPExcel->getActiveSheet()->getCell($Cols[$z].'8')->getCalculatedValue();
            
            $ValorPercapita=$objPHPExcel->getActiveSheet()->getCell($Cols[$z].'10')->getCalculatedValue();
            $PorcentajePoblacional=$objPHPExcel->getActiveSheet()->getCell($Cols[$z].'11')->getCalculatedValue();
        
            $Datos["NitIPS"]=$NIT;
            $Datos["RazonSocialIPS"]=$RazonSocial;
            $Datos["Contrato"]=$OtroSI;
            $Datos["VigenciaInicial"]=$FechaInicial;
            $Datos["VigenciaFinal"]=$FechaFinal;
            $Datos["ValorContrato"]=$ValorContrato;
            $Datos["Modalidad"]=$Modalidad;
            $Datos["idUser"]=$idUser;
            $Datos["FechaRegistro"]=$FechaActual;
            $Datos["NombreArchivo"]=$NombreArchivo;
            $Datos["Soporte"]=$Soporte;
            $Datos["BaseDatos"]=$db;
            $Datos["ValorPercapita"]=$ValorPercapita;
            $Datos["PorcentajePoblacional"]=$PorcentajePoblacional;
            $sql=$this->getSQLInsert("registro_liquidacion_contratos", $Datos);
            $this->Query($sql);
            $z=$z+1;
        }
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        
        unset($sql);
       
        unset($ColumnasTabla);        
        return($idContrato);
        
    }
    
    public function GuardeArchivoEnTemporal($idContrato,$CmbIPS,$CmbEPS,$NombreArchivo,$Ruta,$Soporte,$idUser) {
        clearstatcache();
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $db=$DatosIPS["DataBase"];
        
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$Ruta;
             
        $objReader = IOFactory::createReader('Xlsx');
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                  
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        
        $objPHPExcel->setActiveSheetIndex(0);
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
                       
            $sql= "INSERT INTO $db.`temporal_registro_liquidacion_contratos_items` ( ";
            
            $sql.="`ID`,`idContrato`,`DepartamentoRadicacion`,`Radicado`,`MesServicio`,`NumeroFactura`,`ValorFacturado`,`ImpuestosRetencion`,`Devolucion`,`GlosaInicial`,`GlosaFavorEPS`,`NotasCopagos`,`RecuperacionImpuestos`,`OtrosDescuentos`,";
            $sql.="`ValorPagado`,`Saldo`,`idUser`,`FechaRegistro`";
            
            $sql.=") VALUES ";
            $r=0;
            $Salto=0;
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){
                    continue; 
                }
                
                if($FilaA=='DPTO RADICACION' and $Salto==0){
                    $Salto=1;
                    continue; 
                }
                if($Salto==0){
                    continue; 
                }
                if($FilaA=='TOTAL'){
                    break;
                }
                $c=0;  
                $r++;//Contador de filas a insertar
                           
                $Departamento=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $Departamento= str_replace("'", "", $Departamento);

                $Radicado=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $Radicado= str_replace("'", "", $Radicado);
                
                $MesServicio=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $MesServicio= str_replace("'", "", $MesServicio);
                
                $Factura=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                $Factura= str_replace("'", "", $Factura);
                if($Factura==''){
                    exit("E1;El archivo no tiene la factura relacionanda en la celda D$i");
                }
                $ValorFactura=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $ValorFactura= str_replace("'", "", $ValorFactura);
                
                $ValorRetenciones=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $ValorRetenciones= str_replace("'", "", $ValorRetenciones);
                
                $Devoluciones=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                $Devoluciones= str_replace("'", "", $Devoluciones);
                
                $Glosas=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $Glosas= str_replace("'", "", $Glosas);
                
                $GlosaFavor=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $GlosaFavor= str_replace("'", "", $GlosaFavor);
                
                $NotasCredito=$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $NotasCredito= str_replace("'", "", $NotasCredito);
                
                $ImpuestosRecupedos=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                $ImpuestosRecupedos= str_replace("'", "", $ImpuestosRecupedos);
                
                $OtrosDescuentos=$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                $OtrosDescuentos= str_replace("'", "", $OtrosDescuentos);
                
                $ValorPagado=$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                $ValorPagado= str_replace("'", "", $ValorPagado);
                
                $Saldo=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                $Saldo= str_replace("'", "", $Saldo);
                                
                $sql.="(";
                $sql.="'',";
                $sql.="'$idContrato',";
                $sql.="'$Departamento',";
                $sql.="'$Radicado',";
                $sql.="'$MesServicio',";
                $sql.="'$Factura',";
                $sql.="'$ValorFactura',";
                $sql.="'$ValorRetenciones',";
                $sql.="'$Devoluciones',";
                $sql.="'$Glosas',";     
                $sql.="'$GlosaFavor',";
                $sql.="'$NotasCredito',";
                
                $sql.="'$ImpuestosRecupedos',";
                $sql.="'$OtrosDescuentos',";
                $sql.="'$ValorPagado',";
                $sql.="'$Saldo',";
                
                $sql.="'$idUser','$FechaActual'),";
                       
                if($r==2345){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_registro_liquidacion_contratos_items` ( ";
                    $sql.="`ID`,`idContrato`,`DepartamentoRadicacion`,`Radicado`,`MesServicio`,`NumeroFactura`,`ValorFacturado`,`ImpuestosRetencion`,`Devolucion`,`GlosaInicial`,`GlosaFavorEPS`,`NotasCopagos`,`RecuperacionImpuestos`,`OtrosDescuentos`,";
                    $sql.="`ValorPagado`,`Saldo`,`idUser`,`FechaRegistro`";
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        //}
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    public function GuardeArchivoCapitaEnTemporal($idContrato,$CmbIPS,$CmbEPS,$NombreArchivo,$Ruta,$Soporte,$idUser) {
        clearstatcache();
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $db=$DatosIPS["DataBase"];
        
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$Ruta;
             
        $objReader = IOFactory::createReader('Xlsx');
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                  
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        
        $objPHPExcel->setActiveSheetIndex(0);
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
                       
            $sql= "INSERT INTO $db.`temporal_registro_liquidacion_contratos_items` ( ";
            
            $sql.="`ID`,`idContrato`,`DepartamentoRadicacion`,`Municipio`,`Radicado`,`MesServicio`,`DiasLMA`,`ValorAPagarLMA`,`DescuentoReconocimientoBDUA`,`NumeroFactura`,`ValorFacturado`,`DescuentoInicial`,`DescuentosConciliadosAFavorASMET`,`RecuperacionImpuestos`,";
            $sql.="`ValorPagado`,`Saldo`,`idUser`,`FechaRegistro`";
            
            $sql.=") VALUES ";
            $r=0;
            $Salto=0;
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){
                    continue; 
                }
                
                if($FilaA=='DEPARTAMENTO' and $Salto==0){
                    $Salto=1;
                    continue; 
                }
                if($Salto==0){
                    continue; 
                }
                if($FilaA=='TOTAL'){
                    break;
                }
                $c=0;  
                $r++;//Contador de filas a insertar
                           
                $Departamento=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $Departamento= str_replace("'", "", $Departamento);
                
                $Municipio=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $Municipio= str_replace("'", "", $Municipio);
                
                
                $MesServicio=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $MesServicio= str_replace("'", "", $MesServicio);
                
                $DiasLMA=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                $DiasLMA= str_replace("'", "", $DiasLMA);
                
                $ValorSegunLMA=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $ValorSegunLMA= str_replace("'", "", $ValorSegunLMA);
                
                $Radicado=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $Radicado= str_replace("'", "", $Radicado);
                                
                $Factura=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                $Factura= str_replace("'", "", $Factura);
                if($Factura==''){
                    exit("E1;El archivo no tiene la factura relacionanda en la celda G$i");
                }
                $ValorFactura=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $ValorFactura= str_replace("'", "", $ValorFactura);
                
                $ValorRetenciones=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $ValorRetenciones= str_replace("'", "", $ValorRetenciones);
                
                $DescuentoBDUA=$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $DescuentoBDUA= str_replace("'", "", $DescuentoBDUA);
                
                
                $DescuentosConciliadosAFavor=$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                $DescuentosConciliadosAFavor= str_replace("'", "", $DescuentosConciliadosAFavor);
                
                                
                $DescuentoInicial=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                $DescuentoInicial= str_replace("'", "", $DescuentoInicial);
                
                $ValorPagado=$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                $ValorPagado= str_replace("'", "", $ValorPagado);
                
                $Saldo=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                $Saldo= str_replace("'", "", $Saldo);
                                
                $sql.="(";
                $sql.="'',";
                $sql.="'$idContrato',";
                $sql.="'$Departamento',";
                $sql.="'$Municipio',";
                $sql.="'$Radicado',";
                $sql.="'$MesServicio',";
                
                $sql.="'$DiasLMA',";
                $sql.="'$ValorSegunLMA',";
                $sql.="'$DescuentoBDUA',";
                
                $sql.="'$Factura',";
                $sql.="'$ValorFactura',";
                $sql.="'$DescuentoInicial',";
                $sql.="'$DescuentosConciliadosAFavor',";
                $sql.="'$ValorRetenciones',";     
                
                $sql.="'$ValorPagado',";
                $sql.="'$Saldo',";
                
                $sql.="'$idUser','$FechaActual'),";
                       
                if($r==2345){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_registro_liquidacion_contratos_items` ( ";
                    $sql.="`ID`,`idContrato`,`DepartamentoRadicacion`,`Municipio`,`Radicado`,`MesServicio`,`DiasLMA`,`ValorAPagarLMA`,`DescuentoReconocimientoBDUA`,`NumeroFactura`,`ValorFacturado`,`DescuentoInicial`,`DescuentosConciliadosAFavorASMET`,`RecuperacionImpuestos`,";
                    $sql.="`ValorPagado`,`Saldo`,`idUser`,`FechaRegistro`";
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        //}
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    
    
    //Fin Clases
}
