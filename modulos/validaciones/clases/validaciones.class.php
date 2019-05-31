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
        
class ValidacionesEPS extends conexion{
    
    
    
    public function ActualizarFactura($NumeroFacturaAnterior,$NumeroFacturaNueva,$idIPS,$Observaciones,$idUser) {
        
        
        $Fecha=date("Y-m-d H:i:s");
        $DatosCargas=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosCargas["DataBase"];
        
        $this->ActualizaRegistro("$db.carteracargadaips", "NumeroFactura", $NumeroFacturaNueva, "NumeroFactura", $NumeroFacturaAnterior);
        
        $Datos["FacturaAnterior"]=$NumeroFacturaAnterior;        
        $Datos["FacturaNueva"]=$NumeroFacturaNueva;
        $Datos["Observaciones"]=$Observaciones;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        
        $sql=$this->getSQLInsert("registro_actualizacion_facturas", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    function LeerCargarTemporal($keyArchivo,$idIPS,$RutaArchivo,$Extension,$idUser) {
        
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        
        $FechaActual=date("Y-m-d H:i:s");
        
        if($Extension=="xlsx"){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }else if($Extension=="xls"){
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        $objFecha = new PHPExcel_Shared_Date();    
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        $Proceso="";
        $DescripcionProceso="";
        $Estado="";
        $Cuenta="";
        $Banco="";
        $Cols=['A','B','C'];
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            if($columnas<>'C'){
                exit('E1;<h3>No se recibió el archivo de <strong>Actualización de facturas para IPS</strong></h3>');
            }
            
            for ($i=2;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){

                    continue; 

                }
                

                    $c=0;
                    $_DATOS_EXCEL[$i]['FacturaAnterior']= $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['FacturaNueva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['Observaciones'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    
                    $_DATOS_EXCEL[$i]['idUser'] = $idUser;                    
                    $_DATOS_EXCEL[$i]['FechaRegistro'] = $FechaActual;
                    

            } 
        
        }
        
        $sql= "INSERT INTO $db.`temporal_actualizacion_facturas` ( `FacturaAnterior`, `FacturaNueva`,`Observaciones`, `idUser`, `FechaRegistro`) VALUES ";
        $i=0;    
        foreach($_DATOS_EXCEL as $campo => $valor){
            $i++;
            $sql.="('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaRegistro" ? $sql.= $valor2."')," : $sql.= $valor2."','";
            }
            
            if($i==1000){
                
                $sql=substr($sql, 0, -1);
                //print($sql);
                $this->Query($sql);
                $sql= "INSERT INTO $db.`temporal_actualizacion_facturas` ( `FacturaAnterior`, `FacturaNueva`,`Observaciones`, `idUser`, `FechaRegistro`) VALUES ";
                $i=0;
            }    
            
        }   
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($sql);
    }
    
    
    
    //Fin Clases
}
