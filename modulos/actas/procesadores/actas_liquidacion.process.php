<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/actas_liquidaciones.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ActasLiquidacion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear un Acta
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $TipoActa=$obCon->normalizar($_REQUEST["TipoActa"]);
            $TxtPrefijo=$obCon->normalizar($_REQUEST["TxtPrefijo"]);
            $TxtConsecutivo= ltrim(rtrim($obCon->normalizar($_REQUEST["TxtConsecutivo"])));
            $TxtAnio=$obCon->normalizar($_REQUEST["TxtAnio"]);
            $NombreRepresentanteEPS=$obCon->normalizar($_REQUEST["NombreRepresentanteEPS"]);
            $NombreRepresentanteIPS=$obCon->normalizar($_REQUEST["NombreRepresentanteIPS"]);
            $ApellidosRepresentanteEPS=$obCon->normalizar($_REQUEST["ApellidosRepresentanteEPS"]);
            $ApellidosRepresentanteIPS=$obCon->normalizar($_REQUEST["ApellidosRepresentanteIPS"]);
            $IdentificacionRepresentanteEPS=$obCon->normalizar($_REQUEST["IdentificacionRepresentanteEPS"]);
            $IdentificacionRepresentanteIPS=$obCon->normalizar($_REQUEST["IdentificacionRepresentanteIPS"]);
            $DomicilioRepresentanteEPS=$obCon->normalizar($_REQUEST["DomicilioRepresentanteEPS"]);
            $DomicilioRepresentanteIPS=$obCon->normalizar($_REQUEST["DomicilioRepresentanteIPS"]);
            $DireccionRepresentanteEPS=$obCon->normalizar($_REQUEST["DireccionRepresentanteEPS"]);
            $DireccionRepresentanteIPS=$obCon->normalizar($_REQUEST["DireccionRepresentanteIPS"]);
            $TelefonoRepresentanteEPS=$obCon->normalizar($_REQUEST["TelefonoRepresentanteEPS"]);
            $TelefonoRepresentanteIPS=$obCon->normalizar($_REQUEST["TelefonoRepresentanteIPS"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            
            foreach ($_POST as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            if(!is_numeric($TxtConsecutivo)){
                exit("E1;El campo Consecutivo debe ser un valor númerico;TxtConsecutivo");
            }
            if(!is_numeric($TxtAnio) or strlen($TxtAnio)<>4){
                exit("E1;El campo Año no es válido;TxtAnio");
            }
            
            $obCon->CrearActaLiquidacion($FechaInicial,$FechaFinal,$CmbIPS,$CmbEPS,$TipoActa,$TxtPrefijo, $TxtConsecutivo, $TxtAnio, $NombreRepresentanteEPS,$NombreRepresentanteIPS, $ApellidosRepresentanteEPS,$ApellidosRepresentanteIPS,$IdentificacionRepresentanteEPS, $IdentificacionRepresentanteIPS,$DomicilioRepresentanteEPS, $DomicilioRepresentanteIPS, $DireccionRepresentanteEPS,$DireccionRepresentanteIPS, $TelefonoRepresentanteEPS, $TelefonoRepresentanteIPS, $idUser);
            print("OK;Acta de Liquidación Creada Correctamente");
            
        break; //fin caso 1
        
        case 2: //Editar Acta Liquidacion
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $idCampoTexto=$obCon->normalizar($_REQUEST["idCampoTexto"]);
            $NuevoValor=$obCon->normalizar($_REQUEST["NuevoValor"]);
            $CampoAEditar=$obCon->normalizar($_REQUEST["CampoAEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
                        
            if($idActaLiquidacion==''){
                exit("E1;No se recibió el id del Acta a Editar");
                
            }
            if($NuevoValor==''){
                exit("E1;la caja de texto no puede estar vacía;$idCampoTexto");
                
            }
                        
            $obCon->ActualizaRegistro("actas_liquidaciones", $CampoAEditar, $NuevoValor, "ID", $idActaLiquidacion, 0);
            
            if($CampoAEditar=='FechaInicial'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioInicial", $MesServicioInicial, "ID", $idActaConciliacion, 0);
            }
            if($CampoAEditar=='FechaFinal'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioFinal", $MesServicioFinal, "ID", $idActaConciliacion, 0);
            }
            print("OK;Campo $CampoAEditar del Acta de Liquidación se ha Editado");
        break; // Fin caso 2 
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>