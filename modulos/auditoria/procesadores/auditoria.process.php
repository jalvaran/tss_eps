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
        
        case 1: //Crear las tablas necesarias para auditoria
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
            //$CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->create_table_auditoria_hoja_de_trabajo_evento($db); 
            $obCon->create_table_auditoria_hoja_de_trabajo_pgp($db); 
            $obCon->create_tables_auditoria_anexo_evento($db);
            $obCon->create_tables_auditoria_anexo_pgp($db);
            $obCon->create_tables_auditoria_anexo_capita($db);
            exit("OK;Modulo Inicializado");            
            
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
            if($tipo_anexo==2){                
                $obCon->copiar_anexo_capita_temporal($keyArchivo,$db,$CmbIPS,$idUser);
            }
            if($tipo_anexo==3){                
                $obCon->copiar_anexo_pgp_temporal($keyArchivo,$db,$CmbIPS,$idUser);
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
            if($tipo_anexo==2){//CAPITA
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_capita_temp WHERE Sync='0000-00-00 00:00:00'";
            }
            if($tipo_anexo==3){//PGP
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_pgp_temp WHERE Sync='0000-00-00 00:00:00'";
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
                        (departamento_radicacion,radicado,mes_servicio,factura,valor_facturado,retencion_impuestos,devoluciones,glosa_inicial,glosa_favor,notas_copagos,recuperacion_impuestos,otros_descuentos,valor_pagado,saldo)
                         
                        SELECT t1.departamento_radicacion,t1.radicado,t1.mes_servicio,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.devoluciones,t1.glosa_inicial,t1.glosa_favor,t1.notas_copagos,t1.recuperacion_impuestos,t1.otros_descuentos,t1.valor_pagado,t1.saldo
                        FROM $db.auditoria_anexo_aly_evento_temp t1
                       
                        WHERE t1.Sync='0000-00-00 00:00:00' $condition_limit
                        

                        ";
                $obCon->Query($sql);
                $sql="UPDATE $db.auditoria_anexo_aly_evento_temp SET Sync='$fecha_actualizacion' WHERE Sync='0000-00-00 00:00:00' $condition_limit";
                $obCon->Query($sql);
                
            }
            
            if($tipo_anexo==2){//Anexo capita
                $sql="INSERT INTO $db.auditoria_anexo_aly_capita 
                        (departamento_radicacion,municipio,mes_servicio,dias_lma,valor_segun_lma,radicado,factura,valor_facturado,retencion_impuestos,descuento_reconocimiento_bdua,descuento_inicial,descuento_conciliado_asmet,valor_pagado,saldo)
                         
                        SELECT t1.departamento_radicacion,t1.municipio,t1.mes_servicio,t1.dias_lma,t1.valor_segun_lma,t1.radicado,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.descuento_reconocimiento_bdua,t1.descuento_inicial,t1.descuento_conciliado_asmet,t1.valor_pagado,t1.saldo
                        FROM $db.auditoria_anexo_aly_capita_temp t1
                       
                        WHERE t1.Sync='0000-00-00 00:00:00' $condition_limit
                        

                        ";
                $obCon->Query($sql);
                $sql="UPDATE $db.auditoria_anexo_aly_capita_temp SET Sync='$fecha_actualizacion' WHERE Sync='0000-00-00 00:00:00' $condition_limit";
                $obCon->Query($sql);
                
            }
            
            if($tipo_anexo==3){//Anexo tipo pgp
                $sql="INSERT INTO $db.auditoria_anexo_aly_pgp 
                        (departamento_radicacion,radicado,mes_servicio,factura,valor_facturado,retencion_impuestos,glosa_inicial,glosa_favor,notas_copagos,descuento_pgp,valor_pagado,saldo)
                         
                        SELECT t1.departamento_radicacion,t1.radicado,t1.mes_servicio,t1.factura,t1.valor_facturado,t1.retencion_impuestos,t1.glosa_inicial,t1.glosa_favor,t1.notas_copagos,t1.descuento_pgp,t1.valor_pagado,t1.saldo
                        FROM $db.auditoria_anexo_aly_pgp_temp t1
                       
                        WHERE t1.Sync='0000-00-00 00:00:00' $condition_limit
                        

                        ";
                $obCon->Query($sql);
                $sql="UPDATE $db.auditoria_anexo_aly_pgp_temp SET Sync='$fecha_actualizacion' WHERE Sync='0000-00-00 00:00:00' $condition_limit";
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
            
            if($tipo_anexo==2){//CAPITA
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_capita WHERE Sync='0000-00-00 00:00:00' or Sync IS NULL";
            }
            
            if($tipo_anexo==3){//PGP
                $sql="SELECT COUNT(*) as total_items FROM $db.auditoria_anexo_aly_pgp WHERE Sync='0000-00-00 00:00:00' or Sync IS NULL";
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
            if($page==1){
                $sql="CREATE INDEX IF NOT EXISTS NumeroRadicado ON $db.historial_carteracargada_eps(NumeroRadicado );";
                $obCon->Query($sql);
            }
            
            $tabla_anexo="";
            if($tipo_anexo==1){
                $tabla_anexo="auditoria_anexo_aly_evento";
            }
            
            if($tipo_anexo==2){
                $tabla_anexo="auditoria_anexo_aly_capita";
            }
            
            if($tipo_anexo==3){
                $tabla_anexo="auditoria_anexo_aly_pgp";
            }
            
            
            $sql="UPDATE $db.$tabla_anexo t1 

                        SET t1.contrato=(select NumeroContrato FROM $db.historial_carteracargada_eps t2 WHERE t2.NumeroFactura=t1.factura AND t2.NumeroRadicado=t1.radicado limit 1),
                        Sync='$fecha_actualizacion'
                        WHERE t1.Sync='0000-00-00 00:00:00' or t1.Sync IS NULL   
                        $condition_limit  ;";

                $obCon->Query($sql);
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
    
        case 13://vaciar las conciliaciones a las facturas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $obCon->VaciarTabla("$db.conciliaciones_cruces");
            print("OK;Ajustes Borrados");
        break;//Fin caso 13    
    
        case 14://editar un campo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tabla=$obCon->normalizar($_REQUEST["tabla"]);
            $nuevo_valor=$obCon->normalizar($_REQUEST["nuevo_valor"]);
            $campo_edit=$obCon->normalizar($_REQUEST["campo_edit"]);
            $table_id=$obCon->normalizar($_REQUEST["table_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            if($tabla==1){// auditoria Hojas de trabajo
                $obCon->ActualizaRegistro("auditoria_hojas_trabajo",$campo_edit , $nuevo_valor, $table_id, $item_id);
            }
            if($tabla==2){// auditoria Hojas de trabajo
                $obCon->ActualizaRegistro("auditoria_hojas_trabajo","estado" , 2, "hoja_trabajo_id", $item_id);
            }
            if($tabla==2){
                exit("OK;Hoja de trabajo cerrada");
            }
            print("OK;Campo actualizado");
        break;//Fin caso 14  
        
        case 15://cuenta los registros a copiar
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
            $datos_hoja_trabajo=$obCon->DevuelveValores("auditoria_hojas_trabajo", "hoja_trabajo_id", $hoja_trabajo_id);
            $tipo_anexo=$datos_hoja_trabajo["tipo_negociacion"];
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==1){
                $tabla_hoja_trabajo="auditoria_hoja_de_trabajo_evento";
                $tabla_anexo="auditoria_anexo_aly_evento";
            }
            if($tipo_anexo==3){
                $tabla_hoja_trabajo="auditoria_hoja_de_trabajo_pgp";
                $tabla_anexo="auditoria_anexo_aly_pgp";
            }
            
            $obCon->BorraReg("$db.$tabla_hoja_trabajo", "hoja_trabajo_id", $hoja_trabajo_id);
            $obCon->update("$db.$tabla_anexo", "copied_work_sheet", '0000-00-00 00:00:00', "");
            $sql="SELECT COUNT(*) as total_items FROM $db.$tabla_anexo t1 WHERE EXISTS (SELECT 1 FROM auditoria_hojas_trabajo_contrato t2 WHERE t2.contrato like t1.contrato and t2.hoja_trabajo_id='$hoja_trabajo_id')

                    AND  t1.copied_work_sheet='0000-00-00 00:00:00' or t1.copied_work_sheet IS NULL";

            
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $total_items=$datos_consulta["total_items"];
            
            print("OK;$total_items registros encontrados para copiar;$total_items;$tipo_anexo");
       
        break;//case 15  
        
        case 16://Copiar datos del anexo a la hoja de trabajo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
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
                
                $obCon->insertar_registros_hoja_trabajo_evento($db, $hoja_trabajo_id, $condition_limit, $fecha_actualizacion);
                
            }
            
            if($tipo_anexo==3){//Anexo tipo pgp
                
                $obCon->insertar_registros_hoja_trabajo_pgp($db, $hoja_trabajo_id, $condition_limit, $fecha_actualizacion);
                
            }
            
            $next_page=$page+1;
            if($total_pages<$next_page){
                exit("OK;Todos los registros fueron copiados a la hoja de trabajo");
            }
            $total_pages_div=$total_pages;
            if($total_pages==0){
                $total_pages_div=1;
            }
            $porcentaje=round((100/$total_pages_div)*$page);
            exit("UP;Copiando $page de $total_pages bloques;$next_page;$porcentaje");
            
        break;//Fin caso 16
        
        case 17://cuenta los registros a actualizar
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
            $datos_hoja_trabajo=$obCon->DevuelveValores("auditoria_hojas_trabajo", "hoja_trabajo_id", $hoja_trabajo_id);
            $tipo_anexo=$datos_hoja_trabajo["tipo_negociacion"];
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==1){
                $tabla_hoja_trabajo="auditoria_hoja_de_trabajo_evento";
                
            }
            if($tipo_anexo==3){
                $tabla_hoja_trabajo="auditoria_hoja_de_trabajo_pgp";
                
            }
            
                
            $obCon->update("$db.$tabla_hoja_trabajo", "hoja_trabajo_id", $hoja_trabajo_id, "");
            $sql="SELECT COUNT(*) as total_items FROM $db.$tabla_hoja_trabajo WHERE hoja_trabajo_id='$hoja_trabajo_id' ";
            
            
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $total_items=$datos_consulta["total_items"];
            
            print("OK;$total_items registros encontrados para actualizar;$total_items;$tipo_anexo");
       
        break;//case 17
        
        case 18://Actualizar datos de la hoja de trabajo segun valores del ts
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
            $page=$obCon->normalizar($_REQUEST["page"]);
            $total_items=$obCon->normalizar($_REQUEST["total_items"]);
            $fecha_actualizacion=date("Y:m:d H:i:s");
            $limit=1000;
            $startpoint = ($page * $limit) - $limit;
            $total_pages= ceil($total_items/$limit);
            
            $condition_limit=" LIMIT $limit ";
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            if($tipo_anexo==1){//hoja de trabajo tipo evento
                
                $obCon->actualizar_hoja_trabajo_evento($db, $hoja_trabajo_id, $fecha_actualizacion, $condition_limit);
                
            }
            
            if($tipo_anexo==3){//hoja de trabajo tipo pgp
                
                $obCon->actualizar_hoja_trabajo_pgp($db, $hoja_trabajo_id, $fecha_actualizacion, $condition_limit);
                
            }
            $next_page=$page+1;
            if($total_pages<$next_page){
                exit("OK;Todos los registros fueron copiados a la hoja de trabajo");
            }
            $total_pages_div=$total_pages;
            if($total_pages==0){
                $total_pages_div=1;
            }
            $porcentaje=round((100/$total_pages_div)*$page);
            exit("UP;Actualizando $page de $total_pages bloques;$next_page;$porcentaje");
            
        break;//Fin caso 18
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>