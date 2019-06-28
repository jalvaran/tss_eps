<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/CargarGlosasIDRA.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new GlosasIDRA($idUser);
    $db="ts_eps_resoluciones_glosas";
    switch ($_REQUEST["Accion"]) {
              
        case 1: //Recibir el archivo    
            
            //$obCon->VaciarTabla("$db.temp_resoluciones_glosas_idra");
            $obCon->BorraReg("$db.temp_resoluciones_glosas_idra", "idUser", $idUser);
            $destino='';            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());               
                   
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/ts_eps_resoluciones_glosas/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);   
                    $keyArchivo="GlosasIDRA_".date("Y-m-d");
                    $destino=$carpeta.$keyArchivo.".xls";
                    $NombreArchivo=$keyArchivo.".xls";
                    move_uploaded_file($_FILES['UpCartera']['tmp_name'],$destino);
                }else{
                    print("E1;La Extension: $Extension No está permitida");
                    exit();
                }
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
        
        case 2://Guarda los archivos en la temporal
            
            
            $keyArchivo="GlosasIDRA_".date("Y-m-d");
                   
            $obCon->GuardeArchivoEnTemporal($keyArchivo,$db,$idUser);            
            
            print("OK;El archivo Se guardó en la tabla temporal correctamente");
        break; //fin caso 2
        
        case 3://Insertar registros nuevos
            
            $sql="INSERT INTO $db.`resoluciones_glosas_idra`  
                   SELECT *
                  FROM $db.`temp_resoluciones_glosas_idra` 
                      
                  WHERE NOT EXISTS (SELECT NumeroRadicado FROM $db.resoluciones_glosas_idra
                        WHERE $db.resoluciones_glosas_idra.NumeroRadicado = $db.temp_resoluciones_glosas_idra.NumeroRadicado
                        AND $db.resoluciones_glosas_idra.NumeroFactura = $db.temp_resoluciones_glosas_idra.NumeroFactura    
                        AND $db.resoluciones_glosas_idra.Nit_IPS = $db.temp_resoluciones_glosas_idra.Nit_IPS  
                            ) AND $db.`temp_resoluciones_glosas_idra`.idUser='$idUser';
                    ";
            //print($sql);
            
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 3
    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>