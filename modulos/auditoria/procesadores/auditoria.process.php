<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/auditoria.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Auditoria($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear la hoja de trabajo de auditoria
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
            //$CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->create_table_auditoria_hoja_de_trabajo_evento($db);            
            exit("OK;Se creó la tabla hoja de trabajo evento Satisfactoriamente");            
            
        break; //fin caso 1
        
        case 2://Crear tablas para el anexo de evento
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
               
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->create_tables_auditoria_anexo_evento($db);            
            exit("OK;Se creó la tabla anexo evento Satisfactoriamente");
            
        break;//Fin caso 2    
        
        case 3://Inicializar un anexo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]); 
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==1){
                $obCon->VaciarTabla("$db.auditoria_anexo_aly_evento");
                
            }else if($tipo_anexo==2){
                $obCon->VaciarTabla("$db.auditoria_anexo_aly_capita");
            }else if($tipo_anexo==3){
                $obCon->VaciarTabla("$db.auditoria_anexo_aly_pgp");
            }else{
                exit("E1;No se envió un tipo de Anexo Válido");
            }
            exit("OK;Anexo Inicializado");
            
        break;//Fin caso 3
        
        case 4: //Recibir el anexo
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            
            $keyArchivo=$obCon->getUniqId("aud_");
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['anexo_up']['name'])){
                
                $info = new SplFileInfo($_FILES['anexo_up']['name']);
                $Extension=($info->getExtension()); 
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/auditoria/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/auditoria/anexos";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destino=$carpeta.$keyArchivo.".".$Extension;
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['anexo_up']['tmp_name'],$destino);
                }else{
                    exit("E1;Solo se permiten archivos xls o xlsx");
                }    
            }else{
                exit("E1;No se envió ningún archivo");
                
            }
            $obCon->registra_anexo($keyArchivo,$CmbIPS,$destino,"",$NombreArchivo,$Extension,$idUser);
            print("OK;Archivo Recibido;$keyArchivo");            
            
        break; //fin caso 4
        
        case 5://Guarda los archivos en la temporal
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]); 
            $keyArchivo=$obCon->normalizar($_REQUEST["anexo_id"]);     
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            if($tipo_anexo==1){                
                $obCon->copiar_anexo_evento_temporal($keyArchivo,$db,$CmbIPS,$idUser);
            }
               
            print("OK;El archivo Se guardó en la tabla temporal correctamente");
        break; //fin caso 5
        
        case 6://cuenta los registros a copiar
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==1){//Evento
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_evento_temp WHERE Sync='0000-00-00 00:00:00'";
            }
            
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $total_items=$datos_consulta["total_items"];
            
            print("OK;$total_items registros encontrados para copiar;$total_items");
       
        break;//case 6    
        
         case 7://Copiar datos en la tabla real
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $page=$obCon->normalizar($_REQUEST["page"]);
            $total_items=$obCon->normalizar($_REQUEST["total_items"]);
            $fecha_actualizacion=date("Y:m:d H:i:s");
            $limit=1000;
            $startpoint = ($page * $limit) - $limit;
            $total_pages= ceil($total_items/$limit);
            
            $condition_limit=" LIMIT $limit ";
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            if($tipo_anexo==1){//Anexo tipo evento
                $sql="INSERT INTO $db.auditoria_anexo_aly_evento 
                        (departamento_radicacion,radicado,mes_servicio,factura,valor_facturado,retencion_impuestos,devoluciones,glosa_inicial,glosa_favor,notas_copagos,recuperacion_impuestos,otros_descuentos,valor_pagado)
                         
                        SELECT t1.departamento_radicacion,t1.radicado,t1.mes_servicio,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.devoluciones,t1.glosa_inicial,t1.glosa_favor,t1.notas_copagos,t1.recuperacion_impuestos,t1.otros_descuentos,t1.valor_pagado
                        FROM $db.auditoria_anexo_aly_evento_temp t1
                       
                        WHERE t1.Sync='0000-00-00 00:00:00' $condition_limit
                        

                        ";
                $obCon->Query($sql);
                $sql="UPDATE $db.auditoria_anexo_aly_evento_temp SET Sync='$fecha_actualizacion' WHERE Sync='0000-00-00 00:00:00' $condition_limit";
                $obCon->Query($sql);
                
            }
            
            $next_page=$page+1;
            if($total_pages<$next_page){
                exit("OK;Todos los registros fueron copiados");
            }
            exit("UP;Copiando $page de $total_pages bloques;$next_page");
            
        break;//Fin caso 7
        
        case 8://cuenta los registros a actualizar
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==1){//Evento
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_evento WHERE Sync='0000-00-00 00:00:00' or Sync IS NULL";
            }
            
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $total_items=$datos_consulta["total_items"];
            
            print("OK;$total_items registros encontrados para actualizar;$total_items");
       
        break;//case 8
        
        case 9://Actualiza el numero de contrato
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $page=$obCon->normalizar($_REQUEST["page"]);
            $total_items=$obCon->normalizar($_REQUEST["total_items"]);
            $fecha_actualizacion=date("Y-m-d H:i:s");
            $limit=1000;
            $startpoint = ($page * $limit) - $limit;
            $total_pages= ceil($total_items/$limit);
            
            $condition_limit=" LIMIT $limit ";
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="CREATE INDEX IF NOT EXISTS NumeroRadicado ON $db.historial_carteracargada_eps(NumeroRadicado );";
            $obCon->Query($sql);
            if($tipo_anexo==1){
                $sql="UPDATE $db.auditoria_anexo_aly_evento t1 

                        SET t1.contrato=(select NumeroContrato FROM $db.historial_carteracargada_eps t2 WHERE t2.NumeroFactura=t1.factura AND t2.NumeroRadicado=t1.radicado limit 1),
                        Sync='$fecha_actualizacion'
                        WHERE t1.Sync='0000-00-00 00:00:00' or t1.Sync IS NULL   
                        $condition_limit  ;";

                $obCon->Query($sql);
            }
            if($total_pages<=$page){
                print("OK;Registros Actualizados correctamente");
            }else{
                $next_page=$page+1;
                print("UP;Actualizando $page de $total_pages bloques de registros;$next_page");
            }       
            
        break; //fin caso 9
        
        case 10://Crear una hoja de trabajo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["cmb_tipo_negociacion"]);
            $txt_descripcion=$obCon->normalizar($_REQUEST["txt_descripcion"]);
            if($CmbIPS==''){
                exit("E1;Debe Seleccionar una IPS");
            }
            if($tipo_anexo==''){
                exit("E1;Debe Seleccionar un tipo de Negociación");
            }
            if($txt_descripcion==''){
                exit("E1;Debe escribir una descripción");
            }
            $hoja_trabajo_id=$obCon->getUniqId("hta_");
            $obCon->crear_hoja_trabajo_auditoria($CmbIPS, $hoja_trabajo_id, $tipo_anexo, date("Y-m-d H:i:s"), $txt_descripcion, $idUser);
            print("OK;Hoja de Trabajo creada");
        break;//Fin caso 10   
        
        case 11://Agrega un contrato a una hoja de trabajo
            
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
            $contrato=$obCon->normalizar($_REQUEST["contrato"]);
            
            if($hoja_trabajo_id==''){
                exit("E1;No se recibió una hoja de trabajo");
            }
            if($contrato==''){
                exit("E1;No se recibió un contrato");
            }
            $respuesta=$obCon->agregar_contrato_hoja_de_trabajo($hoja_trabajo_id, $contrato);        
            if($respuesta==1){
                print("OK;Contrato Agregado");
            }
            if($respuesta==2){
                print("E1;El Contrato ya está agregado a esta Hoja de Trabajo");
            }
            
        break;//Fin caso 11  
        
        case 12://Eliminar un contrato a una hoja de trabajo
            
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            $obCon->BorraReg("auditoria_hojas_trabajo_contrato", "ID", $item_id);
            print("OK;Contrato Eliminado");
            
            
        break;//Fin caso 12
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>