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
        
class CarteraIPS extends conexion{
    
    public function getKeyCarteraIPS($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return($CmbIPS."_".$CmbEPS."_".$Fecha);
    }
    
    public function RegistreArchivo($key,$idEPS,$idIPS,$Soporte,$Ruta,$Extension,$idUser) {
        $Fecha=date("Y-m-d H:i:s");
        $DatosCargas=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosCargas["DataBase"];
        $Datos["NombreCargue"]=$key;        
        $Datos["Nit_EPS"]=$idEPS;
        $Datos["Soporte"]=$Soporte;
        $Datos["RutaArchivo"]=$Ruta;
        $Datos["ExtensionArchivo"]=$Extension;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        $Datos["FechaActualizacion"]=$Fecha;
        $sql=$this->getSQLInsert("controlcarguesips", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    public function LeerArchivo($keyArchivo,$FechaCorte,$idIPS,$idUser) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        //require_once('../../../librerias/Excel/PHPExcel.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcarguesips WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Fecha=$DatosUpload["FechaRegistro"];
        $Soporte=$DatosUpload["Soporte"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);
        //$objFecha = new excelToTimestamp();       
        $objPHPExcel->setActiveSheetIndex(0);
        
        $count=0;
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        //print("<br>Columnas $columnas<br>");
        if($columnas<>'W' AND $columnas<>'V'){ //Linea que valida el numero dde columnas correctas para el formato
            exit("E1;El archivo recibido no corresponde al formato de <strong>cartera de IPS $columnas</strong>");
        }
        date_default_timezone_set('UTC'); //establecemos la hora local
        for ($i=2;$i<=$filas;$i++){
            $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue();
            $FilaB=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue();
            if($FilaA=="" or $FilaB==''){
                
                continue;
            }
            //print("Filas $FilaA, $FilaB");
            //if($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()<>''){
                $cell = $objPHPExcel->getActiveSheet()->getCell('G'.$i);
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                    $data=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
                    $data=get_object_vars($data);
                    //print($data["date"]);
                    $FechaRadicado=$data["date"]; 
                }else{
                    //print($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
                    //exit("E1;El Campo G $i, Fecha de Radicado, debe ser Tipo Fecha");
                    $FechaRadicado=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue();
                }
                
                $cell = $objPHPExcel->getActiveSheet()->getCell('D'.$i);
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                    $data=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
                    $data=get_object_vars($data);
                    //print($data["date"]);
                    $FechaFactura=$data["date"]; 
                }else{
                    //exit("E1;El Campo D $i, Fecha de Factura, debe ser Tipo Fecha");
                    $FechaFactura=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue();
                }
                
               
                $_DATOS_EXCEL[$i]['FechaFactura']=$FechaFactura;
                $_DATOS_EXCEL[$i]['FechaRadicado']=$FechaRadicado;
                $_DATOS_EXCEL[$i]['NitEPS']= $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NitIPS']= $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                if($_DATOS_EXCEL[$i]['NitIPS']<>$idIPS){
                    exit("E1;El archivo enviado contiene registros de una IPS diferente en la fila $i, NIT enviado: ".$_DATOS_EXCEL[$i]['NitIPS']);
                }
                $_DATOS_EXCEL[$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroCuentaGlobal'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroRadicado'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['TipoNegociacion'] =trim(strtoupper($objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue()));
                if($_DATOS_EXCEL[$i]['TipoNegociacion']<>'' AND $_DATOS_EXCEL[$i]['TipoNegociacion'] <> 'EVENTO' AND $_DATOS_EXCEL[$i]['TipoNegociacion']<>'CAPITA' AND $_DATOS_EXCEL[$i]['TipoNegociacion']<>'PGP'){
                    exit("E1;El Campo H en la Fila $i debe contener el tipo de negociación, se acepta vacío o 'Evento', 'Capita' O 'PGP' y contiene: ".$_DATOS_EXCEL[$i]['TipoNegociacion']);
                }
                $_DATOS_EXCEL[$i]['NumeroContrato'] = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['DiasPactados'] = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['TipoRegimen'] = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                
                $_DATOS_EXCEL[$i]['ValorDocumento'] = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaInicial'] = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaAceptada'] = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaConciliada'] = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorDescuentoBdua'] = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorAnticipos'] = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                
                $_DATOS_EXCEL[$i]['ValorRetencion'] = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['Copagos'] = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['Devoluciones'] = $objPHPExcel->getActiveSheet()->getCell('T'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['Pagos'] = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorTotalpagar'] = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['FechaHasta'] = $FechaCorte;
                $_DATOS_EXCEL[$i]['Soporte'] = $Soporte;
                $_DATOS_EXCEL[$i]['idUser'] = $idUser;
                
                $_DATOS_EXCEL[$i]['FechaRegistro'] = $Fecha;
                $_DATOS_EXCEL[$i]['FechaActualizacion'] = $Fecha;
                
           // }
        } 
        $sql="";
        $r=0;
        $sql= "INSERT INTO $db.temporalcarguecarteraips (FechaFactura,FechaRadicado,NitEPS,NitIPS,NumeroFactura,NumeroCuentaGlobal,NumeroRadicado,TipoNegociacion,NumeroContrato,DiasPactados,TipoRegimen,ValorDocumento,ValorGlosaInicial,ValorGlosaAceptada,ValorGlosaConciliada,ValorDescuentoBdua,ValorAnticipos,ValorRetencion,Copagos,Devoluciones,Pagos,ValorTotalpagar,FechaHasta,Soporte,idUser,FechaRegistro,FechaActualizacion)  VALUES ";
        foreach($_DATOS_EXCEL as $campo => $valor){
            $r++;
            $sql.=" ('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaActualizacion" ? $sql.= $valor2."')," : $sql.= $valor2."','";
            }
            if($r>=1000){
                $r=0;
                $sql=substr($sql, 0, -1);
                $this->Query($sql);
                $sql= "INSERT INTO $db.temporalcarguecarteraips (FechaFactura,FechaRadicado,NitEPS,NitIPS,NumeroFactura,NumeroCuentaGlobal,NumeroRadicado,TipoNegociacion,NumeroContrato,DiasPactados,TipoRegimen,ValorDocumento,ValorGlosaInicial,ValorGlosaAceptada,ValorGlosaConciliada,ValorDescuentoBdua,ValorAnticipos,ValorRetencion,Copagos,Devoluciones,Pagos,ValorTotalpagar,FechaHasta,Soporte,idUser,FechaRegistro,FechaActualizacion)  VALUES ";
            }
            //print($sql);    
            
        }    
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        $errores=0;
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        //unset($key);
        unset($ColumnasTabla);
        //print($DatosUpload["Soporte"]);
    }
    //Fin Clases
}
