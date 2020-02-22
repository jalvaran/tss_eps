<?php 
if(isset($_REQUEST["idDocumento"])){
    $myPage="GeneradorExcel.php";
    
    include_once("../clases/ClasesDocumentosExcel.class.php");
    session_start();    
    $idUser=$_SESSION['idUser'];
   
    $obCon=new TS_Excel($idUser);
    $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
    
    switch ($idDocumento){
        case 1: //Formato de conciliaciones masivas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $obCon->GenerarFormatoConciliacionesMasivas($db,$CmbIPS);
            
        break;//Fin caso 1
        case 2: //Formato de anexo a acta de liquidaciones
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $TipoConsulta=$obCon->normalizar($_REQUEST["TipoConsulta"]); //Para saber si se obtiene la consulta de los items de las conciliaciones o de liquidaciones
            $FacturaRadicado=$obCon->normalizar($_REQUEST["FacturaRadicado"]); //Define si se saca por factura (0) o por radicado (1)
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            //$TipoActa=$obCon->normalizar($_REQUEST["TipoActa"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosActa= $obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);     
            //$DatosActaTipo=$obCon->DevuelveValores("actas_liquidaciones_tipo", "ID", $DatosActa["TipoActaLiquidacion"]);
            //print($DatosActaTipo["Header"]);
            if($DatosActa["TipoActaLiquidacion"]==1 or $DatosActa["TipoActaLiquidacion"]==2 or $DatosActa["TipoActaLiquidacion"]==3 or $DatosActa["TipoActaLiquidacion"]==7 or $DatosActa["TipoActaLiquidacion"]==8 or $DatosActa["TipoActaLiquidacion"]==9 or $DatosActa["TipoActaLiquidacion"]==10){
                if($FacturaRadicado==0){
                    $obCon->GenerarAnexoActaLiquidacionXFacturas1($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta);
                    //$obCon->GenerarAnexoActaLiquidacionXFacturasSpout($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta);

                }
                if($FacturaRadicado==1){
                    $obCon->GenerarAnexoActaLiquidacionXRadicados1($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta);
                }
            }
            if($DatosActa["TipoActaLiquidacion"]==4 or $DatosActa["TipoActaLiquidacion"]==6){
                $obCon->GenerarAnexoActaLiquidacionXFacturasCapita($db,$DatosIPS,$idActaLiquidacion,$DatosActa,$TipoConsulta);
            }
            
        break;//Fin caso 2
        
    }
}else{
    print("No se recibió parametro de documento");
}

?>