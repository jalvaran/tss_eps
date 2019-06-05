<?php
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
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcarguesips WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Fecha=$DatosUpload["FechaRegistro"];
        $Soporte=$DatosUpload["Soporte"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);
        $objFecha = new PHPExcel_Shared_Date();       
        $objPHPExcel->setActiveSheetIndex(0);
        
        $count=0;
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        //print("<br>Columnas $columnas<br>");
        if($columnas<>'V'){
            exit("E1;El archivo recibido no corresponde al formato de <strong>cartera de IPS</strong>");
        }
        date_default_timezone_set('UTC'); //establecemos la hora local
        for ($i=2;$i<=$filas;$i++){
            if($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()<>''){
                $data=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
                $FechaRadicado=date("Y-m-d",$data); 
                $data=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
                $FechaFactura=date("Y-m-d",$data); 
                $_DATOS_EXCEL[$i]['FechaFactura']=$FechaFactura;
                $_DATOS_EXCEL[$i]['FechaRadicado']=$FechaRadicado;
                $_DATOS_EXCEL[$i]['NitEPS']= $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NitIPS']= $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroCuentaGlobal'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroRadicado'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['TipoNegociacion'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
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
                
            }
        } 
        $sql="";
        
        foreach($_DATOS_EXCEL as $campo => $valor){
            $sql= "INSERT INTO $db.temporalcarguecarteraips (FechaFactura,FechaRadicado,NitEPS,NitIPS,NumeroFactura,NumeroCuentaGlobal,NumeroRadicado,TipoNegociacion,NumeroContrato,DiasPactados,TipoRegimen,ValorDocumento,ValorGlosaInicial,ValorGlosaAceptada,ValorGlosaConciliada,ValorDescuentoBdua,ValorAnticipos,ValorRetencion,Copagos,Devoluciones,Pagos,ValorTotalpagar,FechaHasta,Soporte,idUser,FechaRegistro,FechaActualizacion)  VALUES ('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaActualizacion" ? $sql.= $valor2."');" : $sql.= $valor2."','";
            }
            
            //print($sql);    
            $this->Query($sql);
        }    
        
        $errores=0;

        //print($DatosUpload["Soporte"]);
    }
    //Fin Clases
}
