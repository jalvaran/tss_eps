<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/f10.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new F10($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Copiar los contratos que no estén al F10 
            
            $obCon->crear_contratos_f10($idUser);            
            exit("OK;Contratos agregados al F10");
            
        break; //fin caso 1
        
        case 2: //Actualizar marca f10 de contratos que se copiaron al f10
            
            $obCon->actualizar_marca_f10_contratos($idUser);
            exit("OK;Marca Actualizada");
            
        break; //fin caso 2
    
        case 3: //Actualizar control de cambios
            
            $obCon->actualizar_control_cambios_f10($idUser);
            exit("OK;Control de cambios actualizado");
            
        break; //fin caso 3
        
        case 4: //inicialice los registros f10 para ser actualizados
            $sql="UPDATE f10 t1 SET t1.f10_actualizacion_campos_automaticos=0 
                    WHERE exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t2.idUsuario='$idUser')";
            //$sql="UPDATE f10 t1 SET t1.f10_actualizacion_campos_automaticos=0 ";
            $obCon->Query($sql);
            
            exit("OK;iniciando actualización automatica");
            
            
        break; //fin caso 4
        
        case 5: //Actualizar valores f10
            $sql="SELECT * FROM f10 t1 
                    WHERE f10_actualizacion_campos_automaticos=0 and exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t2.idUsuario='$idUser') limit 1";
            //$sql="SELECT * FROM f10 t1 WHERE f10_actualizacion_campos_automaticos=0 limit 1";
            $datos_f10=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_f10["ID"]>0){
                $total_datos=$obCon->actualizar_valores_ts_f10($datos_f10,$idUser);
                exit("OK;Se Actualizaron los valores del contrato ".$datos_f10["ID"].";$datos_f10[NumeroContrato] $total_datos");
            }else{
                exit("FIN;No hay contratos por actualizar;$datos_f10[NumeroContrato] 0");
            }
            
        break; //fin caso 5
        
        case 6://editar un valor del f10
            $f10_id=$obCon->normalizar($_REQUEST["f10_id"]);
            $campo_editar=$obCon->normalizar($_REQUEST["campo_id"]);
            $valor=$obCon->normalizar($_REQUEST["valor_campo"]);
            $array_campo[$campo_editar]=$valor;
            $campos_obligatorios=["NumeroInterno"=>'',"Naturaleza"=>''];
            $campos_numericos=["NumeroInterno"=>'',
                               "ValorGlosaxConciliar"=>'',
                               "SaldoCuentaXPagar"=>'',
                               "NumeroCuotasAcuerdo"=>'',
                               'ValorTotalAcuerdo'=>'',
                               'ValorCuotaAcuerdo'=>'',
                               'ValorSaldoAcuerdo'=>'',
                               'AnioFinalizacionContrato'=>'',
                               'SaldoInicialSeven'=>'',
                               'PendienteAuditoria'=>''
                                
                                ];
            $campos_mayor_cero=["NumeroInterno"=>'',
                                "NumeroCuotasAcuerdo"=>'',
                                'ValorTotalAcuerdo'=>'',
                                'ValorCuotaAcuerdo'=>'',
                                'ValorSaldoAcuerdo'=>'',
                                'AnioFinalizacionContrato'=>'',
                                'PendienteAuditoria'=>''
                                
                
                                ];
            $campos_unicos=["NumeroInterno"=>''];
            $sql="SELECT $campo_editar FROM f10 WHERE ID='$f10_id'";
            $datos_actuales=$obCon->FetchAssoc($obCon->Query($sql));
            $valor_actual=$datos_actuales[$campo_editar];
            foreach ($campos_obligatorios as $key => $value) {
                if($campo_editar==$key and $valor==''){
                    exit("E1;El campo $key No puede estar vacío;$key;$valor_actual");
                }
            }
            
            foreach ($campos_numericos as $key => $value) {
                if($campo_editar==$key and !is_numeric($valor)){
                    exit("E1;El campo $key debe ser un valor númerico;$key;$valor_actual");
                }
            }
            
            foreach ($campos_mayor_cero as $key => $value) {
                if($campo_editar==$key and $valor<0 ){
                    exit("E1;El campo $key debe ser un número mayor a Cero;$key;$valor_actual");
                }
            }
            foreach($campos_unicos as $key => $value){
                $sql="SELECT ID FROM f10 WHERE $key='$valor' and ID<>'$f10_id'";
                $validacion=$obCon->FetchAssoc($obCon->Query($sql));
                if($campo_editar==$key and $validacion["ID"]>0){
                    exit("E1;El campo $key ya existe;$key;$valor_actual");
                }
            }
            
            if($campo_editar=='AnioFinalizacionContrato' and strlen($valor)<>'4'  ){
                
                exit("E1;El campo Año de Finalización debe ser un valor numerico de 4 caracteres;$key;$valor_actual");
            }
            
            if($campo_editar=='FechaEnvioCruceCartera'){
                $dias=$obCon->CalculeDiferenciaFechas($valor, date("Y-m-d"), "");
                $dias_transcurridos=($dias["Anios"]*365)+($dias["Meses"]*30)+$dias["Dias"];
                $obCon->ActualizaRegistro("f10", "DiasTranscurridos", $dias_transcurridos, "ID", $f10_id,1);
            }
            
            if($datos_actuales[$campo_editar]<>$valor){
                $obCon->ActualizaRegistro("f10", $campo_editar, $valor, "ID", $f10_id,1);
                $obCon->ActualizaRegistro("f10", "FechaActualizacionManual", date("Y-m-d H:i:s"), "ID", $f10_id,1);
                $obCon->ActualizaRegistro("f10", "usuario_edicion", $idUser, "ID", $f10_id,1);
                $obCon->ActualizaRegistro("f10", "f10_control_cambios_marca", 0, "ID", $f10_id,1);
            }
            
            exit("OK;Registro Actualizado");
        break;//Fin caso 6    
        
        case 7://Recibir un adjunto para un f10
            
            $f10_id=$obCon->normalizar($_REQUEST["f10_id"]);
            $datos_f10=$obCon->DevuelveValores("f10", "ID", $f10_id);
            $Extension="";
            if(!empty($_FILES['adjunto_f10']['name'])){
                
                $info = new SplFileInfo($_FILES['adjunto_f10']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['adjunto_f10']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta="../../../".$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$datos_f10["NitIPSContratada"]."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="f10/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$f10_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("f10_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['adjunto_f10']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoF10($f10_id, $destino, $Tamano, $_FILES['adjunto_f10']['name'], $Extension, $idUser);
            }else{
                exit("E1;No se recibió el archivo");
            }
            print("OK;Archivo adjuntado");
           
        break;//Fin caso 7
        
        case 8://eliminar un registro
            
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            if($tabla_id==''){
                exit("E1;No se envio tabla");
            }
            
            if($tabla_id==1){
                $tabla="f10_adjuntos";
                $DatosAdjunto=$obCon->DevuelveValores("contratos_adjuntos", "ID", $item_id);
                if(file_exists($DatosAdjunto["Ruta"])){
                    unlink($DatosAdjunto["Ruta"]);
                }
            }
            
            $obCon->BorraReg($tabla, "ID", $item_id);
            print("OK;Registro Eliminado");
        break;//Fin caso 8
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>