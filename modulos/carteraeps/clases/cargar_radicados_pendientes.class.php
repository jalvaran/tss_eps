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
        
class CargarRadicadosPendientes extends conexion{
    
    public function getKeyFile($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("radicados_pendientes_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
        $sql=$this->getSQLInsert("controlcargueseps", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    public function formatearFechaCsv($Dato) {
        if($Dato<>""){
            $FechaArchivo= explode("/", $Dato);
            if(count($FechaArchivo)>1){
                $FechaFormateada= $FechaArchivo[2]."-".$FechaArchivo[1]."-".$FechaArchivo[0];
            }else{
                $FechaFormateada=$Dato;
            }

         }else{
            $FechaFormateada="0000-00-00";
         }
         return($FechaFormateada);
    }
    
    public function LeerArchivo($keyArchivo,$FechaCorte,$idIPS,$Separador,$idUser) {
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Soporte=$DatosUpload["Soporte"];
        $EPS=$DatosUpload["Nit_EPS"];
        $FechaRegistro=$DatosUpload["FechaRegistro"];
        $FechaActualizacion=$DatosUpload["FechaActualizacion"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
                
        $i=0;
        if($Separador==1){
           $Separador=";"; 
        }else{
           $Separador=",";  
        }
                    
        $handle = fopen($RutaArchivo, "r");
        
        $r=0;
        $z=0;
        
        $CamposDatos["NumeroRadicado"]=$z++;                   //0   
        $CamposDatos["Origen"]=$z++;       
        $CamposDatos["DepartamentoRadicacion"]=$z++;          
        $CamposDatos["FechaRadicacion"]=$z++;
        $CamposDatos["Nit_IPS"]=$z++;
        $CamposDatos["RazonSocial"]=$z++;
        $CamposDatos["TipoContrato"]=$z++;
        $CamposDatos["ModalidadContratacion"]=$z++;
        $CamposDatos["NumeroContrato"]=$z++;
        $CamposDatos["Valor"]=$z++;        
        $CamposDatos["EstadoAuditoria"]=$z++;                       //10
        $CamposDatos["EstadoRadicacion"]=$z++;
        $CamposDatos["FechaAprobacion"]=$z++;
        $CamposDatos["UsuarioAprobacion"]=$z++;
        $CamposDatos["Soporte"]=$z++;
        $CamposDatos["idUser"]=$z++;                                //15
        $CamposDatos["FechaRegistro"]=$z++;
        $CamposDatos["keyFile"]=$z++;
        $sqlCampos = "INSERT INTO $db.`temp_radicadospendientes` (";
        $sqlValores= ' VALUES ';   
        $length_array=count($CamposDatos);
        $i = 1;        
        foreach ($CamposDatos as $key => $value) {
            $sqlCampos .= "`$key`";            
            if ($i!= $length_array) {
              $sqlCampos .= ", " ;              
            }else {
              $sqlCampos .= ')';              
            }
            $i++;
        }
        $Verifique=1;      
        while ( ($data = fgetcsv($handle, 1000, $Separador,'"')) !== FALSE) {
            if($Verifique==1){
                $Verifique=0;
                $TotalColumnas=count($data);
                //print_r($data);
                if($TotalColumnas<>14){
                    exit("E1;El archivo enviado no corresponde al esperado, tiene $TotalColumnas Columnas");
                }
                continue;
            }
            $r++;
            $z++;
            if(!isset($data[0])){
                continue;
            }
            if(!isset($data[4])){
                continue;
            }
            
            if($data[4]<>$idIPS){
                exit("E1;El archivo contiene registros de otra ips con NIT: $data[4]");
            }
            
            
            $sqlValores.="(";
            $i = 1;
            foreach ($CamposDatos as $key => $value) {
                
                if(isset($data[$value])){
                    $dato=str_replace(".", "", $data[$value]);
                    $dato=str_replace("'", "", $dato);                   
                    $dato= preg_replace("/[\r\n|\n|\r]+/", " ", $dato);
                    if($key=='FechaRadicacion' or $key=="FechaAprobacion"){
                        $dato=$this->formatearFechaCsv($data[$value]);
                    }  
                }else{
                    $dato='';
                }
                if($key=="Soporte"){
                    $dato=$Soporte;
                }
                
                if($key=="idUser"){
                    $dato=$idUser;
                }
                if($key=="FechaRegistro"){
                    $dato=$FechaRegistro;
                }
                if($key=="keyFile"){
                    $dato=$keyArchivo;
                }
                $sqlValores .= "'$dato'";
                if ($i!= $length_array) {                  
                  $sqlValores .= "," ;
                }else {                  
                  $sqlValores .= '),';
                }
                $i++;
              }
                               
            
             if($r==1000){
                 //print($sqlValores);
                 $r=0;
                 $sqlValores=substr($sqlValores, 0, -1);                
                 $sql=$sqlCampos.$sqlValores;
                // print($sql);
                $this->Query($sql);                
                $sqlValores= ' VALUES ';
                
             }
             
             
        }
        
        fclose($handle); 
        
        $sqlValores=substr($sqlValores, 0, -1);
                
        $sql=$sqlCampos.$sqlValores;
        //print("<pre>".$sql."</pre>");
       $this->Query($sql);

       $sqlValores='';
        
       $sql="";
        unset($CamposDatos);
        
        return $z;
    }
    
    
    //Fin Clases
}
