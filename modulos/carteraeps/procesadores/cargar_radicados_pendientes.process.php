<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/cargar_radicados_pendientes.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new CargarRadicadosPendientes($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            print('OK;Verificando que no se haya cargado el archivo previamente');
            exit();
              
        break; //fin caso 1
        
        case 2: //Recibir el archivo
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyFile($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->BorraReg("$db.temporalcarguecarteraeps", "idUser", $idUser);
            $obCon->VaciarTabla("$db.temp_radicadospendientes");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());                
                $carpeta="../../../soportes/$CmbIPS/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $destino=$carpeta.$keyArchivo.".".$Extension;
                $NombreArchivo=$keyArchivo.".".$Extension;
                move_uploaded_file($_FILES['UpCartera']['tmp_name'],$destino);
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            $obCon->RegistreArchivo($keyArchivo,$CmbEPS,$CmbIPS,$NombreArchivo,$destino,$Extension,$idUser);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
          
        case 3://Lee el archivo y lo sube a la temporal
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $Separador=$obCon->normalizar($_REQUEST["Separador"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $keyArchivo=$obCon->getKeyFile($FechaCorteCartera, $CmbIPS, $CmbEPS);
            if($DatosEPS["ID"]==1 or $DatosEPS["ID"]==2){
                $obCon->LeerArchivo($keyArchivo,$FechaCorteCartera,$CmbIPS,$Separador,$idUser);
            }
            
            print("OK;Archivo cargado");
        break; //fin caso 3   
    
        case 4://LLevar elos registros nuevos en la temporal al historial de cargas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyFile($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.radicadospendientes");
            $sql="INSERT INTO $db.`radicadospendientes` 
                    SELECT * FROM $db.`temp_radicadospendientes`";
            $obCon->Query($sql);
            
            $obCon->VaciarTabla("$db.pendientes_de_envio");
            $sql="INSERT INTO $db.`pendientes_de_envio` (TablaOrigen,NumeroRadicado,Valor) "
                    . "SELECT Radicados,NumeroRadicado,Total FROM $db.vista_pendientes ";
            $obCon->Query($sql);
            
            print("OK;Registros copiados");
        break; //fin caso 4  
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyFile($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->VaciarTabla("$db.temporalcarguecarteraeps");
            $obCon->BorraReg("$db.controlcargueseps", "NombreCargue", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
         
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>