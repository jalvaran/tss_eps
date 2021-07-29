<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el formulario para subir el anexo del ALY
            
            $css->div("", "row", "", "", "", "", "");
                $css->div("", "col-md-12", "", "", "", "", "");
                    $css->CrearTitulo("<strong>SUBIR EL ANEXO DE LA LIQUIDACIÓN</strong>", "azul");
                $css->Cdiv();
                                
                $css->CrearDiv("", "col-md-8", "left", 1, 1);
                    print("<strong>Seleccione el archivo:</strong><br>");
                    $css->input("file", "anexo_up", "form-control", "anexo_up", "", "", "", "", "", "style='line-height: 15px;'");
                $css->Cdiv();
                
                $css->CrearDiv("", "col-md-2", "center", 1, 1);
                    print("<strong>Inicializar Anexo:</strong><br>");
                    $css->CrearBotonEvento("btn_vaciar_anexo", "Inicializar", 1, "onclick", "confirma_inicializar_anexo()", "rojo");
                $css->Cdiv();
                
                $css->CrearDiv("", "col-md-2", "center", 1, 1);
                    print("<strong>Cargar:</strong><br>");
                    $css->CrearBotonEvento("btn_subir_anexo", "Ejecutar", 1, "onclick", "confirma_subir_anexo()", "verde");
                $css->Cdiv();
    
            $css->Cdiv();
            print("<br>");
            $css->div("", "row", "", "", "", "", "");
                $css->CrearDiv("progres_div", "col-md-12", "center", 1, 1);
                    $css->ProgressBar("PgProgresoUp", "LyProgresoUP", "", 0, 0, 100, 0, "0%", "", "");
                $css->Cdiv();  
                print("<br>");
                $css->CrearDiv("mensajes_div", "col-md-12", "center", 1, 1);
                    
                $css->Cdiv();
            $css->Cdiv();
            
        break; //Fin caso 1
        
        case 2:// formulario para construir una hoja de trabajo
            
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);  
            $datos_hoja_trabajo=$obCon->DevuelveValores("auditoria_hojas_trabajo", "hoja_trabajo_id", $hoja_trabajo_id);
            $datos_ips=$obCon->DevuelveValores("ips", "NIT", $datos_hoja_trabajo["ips_id"]);
            $tipo_anexo=$datos_hoja_trabajo["tipo_negociacion"];  
            
            if($tipo_anexo==''){
                $css->CrearTitulo("<strong>DEBE SELECCIONAR EL TIPO DE NEGOCIACIÓN</strong>", "rojo");
                exit();
            }
            
            $css->div("", "row", "", "", "", "", "");
                $css->div("", "col-md-12", "", "", "", "", "");
                    $css->CrearTitulo("<strong>Construir la Hoja de Trabajo No. ".$datos_hoja_trabajo["ID"]." </strong> para la Entidad: ".$datos_ips["Nombre"], "azul");
                $css->Cdiv();
                                
                $css->CrearDiv("", "col-md-8", "left", 1, 1);
                    //print("<strong>Contratos disponibles en el anexo:</strong><br>");
                    $css->CrearDiv("contratos_anexo_div", "col-md-12", "left", 1, 1);
                    $css->Cdiv();   
                $css->Cdiv();   
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    //print("<strong>Contratos agregados:</strong><br>");
                    $css->CrearDiv("contratos_anexo_agregados_div", "col-md-12", "left", 1, 1);
                    
                    $css->Cdiv();   
                $css->Cdiv();   
                
            $css->Cdiv();
            print("<br>");
            $css->div("", "row", "", "", "", "", "");
                $css->CrearDiv("opciones_construccion", "col-md-4", "center", 1, 1);
                    $css->CrearBotonEvento("btn_construir_hoja_trabajo", "Construir Hoja de Trabajo", 1, "onclick", "confirma_construir_hoja_trabajo(`".$hoja_trabajo_id."`)", "azul");
                $css->Cdiv();
                $css->CrearDiv("opciones_construccion", "col-md-4", "center", 1, 1);
                    $css->CrearBotonEvento("btn_actualizar_hoja_trabajo", "Actualizar Hoja de Trabajo", 1, "onclick", "confirma_actualizar_hoja_trabajo(`".$hoja_trabajo_id."`)", "verde");
                $css->Cdiv();
            $css->Cdiv();
            print("<br>");
            $css->div("", "row", "", "", "", "", "");
                $css->CrearDiv("progres_div_insert", "col-md-12", "center", 1, 1);
                    $css->ProgressBar("PgProgresoUp_insert", "LyProgresoUP_insert", "", 0, 0, 100, 0, "0%", 1, "");
                $css->Cdiv();  
                $css->CrearDiv("progres_div_update", "col-md-12", "center", 1, 1);
                    $css->ProgressBar("PgProgresoUp_update", "LyProgresoUP_update", "", 0, 0, 100, 0, "0%", 2, "");
                $css->Cdiv(); 
                print("<br>");
                $css->CrearDiv("mensajes_div", "col-md-12", "center", 1, 1);
                    
                $css->Cdiv();
            $css->Cdiv();
            
            
        break;//Fin caso 2    
        
        case 3://tabla de contratos disponibles para construir la hoja de trabajo
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]); 
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);  
            $datos_hoja_trabajo=$obCon->DevuelveValores("auditoria_hojas_trabajo", "hoja_trabajo_id", $hoja_trabajo_id);
            
            $tipo_anexo=$datos_hoja_trabajo["tipo_negociacion"];  
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            if($tipo_anexo==""){
                exit("E1;Seleccione un tipo de negociacion");
            }
            if($tipo_anexo==1){
                $sql="SELECT contrato,count(contrato) as numero_contratos,
                    sum(valor_facturado) as valor_facturado                     
                    FROM $db.auditoria_anexo_aly_evento GROUP BY contrato
                    
                    ";
                $consulta=$obCon->Query($sql);
            }
            
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    print('<td>');
                        $css->CrearBotonEvento("btnOpcionesMasivas", "Ajustes A Facturas", 1, "onclick", "AbreOpcionesMasivas()", "naranja");
                    print('</td>');
                    print('<td>');
                        $css->CrearBotonEvento("btnEliminarAjsutes", "Eliminar ajustes a facturas", 1, "onclick", "eliminar_ajustes_facturas()", "rojo");
                    print('</td>');
                    print('<td colspan="2">');
                        $css->input("text", "txt_descripcion_hoja", "form-control", "txt_descripcion_hoja", "Descripcion de la hoja de trabajo", $datos_hoja_trabajo["Descripcion"], "Descipcion", "off", "", 'onchange="editar_campo(`1`,`txt_descripcion_hoja`,`Descripcion`,`hoja_trabajo_id`,`'.$hoja_trabajo_id.'`)"');
                    print('</td>');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Contratos Disponibles en el Anexo</strong>", 4,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Acciones</strong>", 1,"C");
                    
                    $css->ColTabla("<strong>Contrato</strong>", 1,"C");
                    $css->ColTabla("<strong>Número Facturas</strong>", 1,"C");
                    $css->ColTabla("<strong>Valor Facturado</strong>", 1,"C");
                    
                $css->CierraFilaTabla();
            while($datos_consulta=$obCon->FetchAssoc($consulta)){
                $css->FilaTabla(16);
                    $ID_hoja_trabajo=$datos_hoja_trabajo["ID"];
                    $contrato=$datos_consulta["contrato"];
                    print('<td>');
                        print('<div class="text-left">');
                            print('<a title="Agregar" onclick="agregar_contrato_hoja_trabajo(`'.$hoja_trabajo_id.'`,`'.$contrato.'`);"  class="btn btn-social-icon btn-bitbucket"><i class="fa fa-mail-forward"></i></a> ');
                            print('<a title="Renombrar" onclick="ModalRenombrarContrato(`'.$contrato.'`)" class="btn btn-social-icon btn-flickr"><i class="fa fa-edit"></i></a> ');
                            print('<a title="Percapitas" class="btn btn-social-icon btn-github"><i class="fa fa-indent"></i></a>');
                        
                        
                        print('</div>');
                    print('</td>');
                    
                    $css->ColTabla("<span>".$datos_consulta["contrato"]."</span>", 1,"L");
                    $css->ColTabla(number_format($datos_consulta["numero_contratos"]), 1,"R");
                    $css->ColTabla(number_format($datos_consulta["valor_facturado"]), 1,"R");
                $css->CierraFilaTabla();
            }
            
            $css->CerrarTabla();
            
        break;//fin caso 3
        
        case 4://dibuje el listado de hojas de trabajo
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);        
            $Busqueda=$obCon->normalizar($_REQUEST["TxtBusquedas"]);            
            $tipo_anexo=$obCon->normalizar($_REQUEST["tipo_anexo"]);
            $datos_ips=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $Condicional=" WHERE ips_id='$CmbIPS' ";
            if($tipo_anexo<>''){
                $Condicional.=" AND tipo_negociacion='$tipo_anexo' ";
                
            }
                        
            if($Busqueda<>''){
                $Condicional.=" AND (ID='$Busqueda' or Descripcion like '%$Busqueda%') ";
            }
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            
            $OrderBy=" ORDER BY ID DESC";
            
            
                        
            $limit = 20;
            $startpoint = ($NumPage * $limit) - $limit;
            
            
            $query = "SELECT COUNT(*) as `num` FROM `auditoria_hojas_trabajo` $Condicional ";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            
            $Limit=" LIMIT $startpoint,$limit";
            
            $sql="SELECT t1.*, 
                    (SELECT tipo_negociacion FROM auditoria_tipos_anexo t2 WHERE t1.tipo_negociacion=t2.ID LIMIT 1) as nombre_tipo_negociacion, 
                    (SELECT nombre_estado FROM auditoria_hojas_trabajo_estados t2 WHERE t1.estado=t2.ID LIMIT 1) as nombre_estado , 
                    (SELECT CONCAT(Nombre,' ',Apellido ) FROM usuarios t2 WHERE t1.user_id=t2.idUsuarios LIMIT 1) as nombre_usuario  
                    
                    FROM  auditoria_hojas_trabajo t1 $Condicional $OrderBy  $Limit";
            $Consulta=$obCon->Query($sql);
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print("<h3 class='box-title'>Listado de Hojas de Trabajo para la entidad: <strong>".$datos_ips["Nombre"]."</strong></h3>");
                print('<span class="label label-primary pull-right"><h4><strong>'.$ResultadosTotales.'</strong></h4></span>');
            $css->CerrarDiv();
            
            $css->CrearDiv("", "box-body no-padding", "", 1, 1);
                $css->CrearDiv("", "mailbox-controls", "", 1, 1);
                    print('<button type="button" class="btn btn-primary btn" onclick="frm_crear_hoja_trabajo_nueva()"><i class="fa fa-plus"></i></button>');
                    
                    $css->CrearDiv("", "pull-right", "", 1, 1);
                       
                        print('<div class="input-group">');   
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            if($NumPage>1){
                                 $goPage=$NumPage-1;
                                 
                                 print('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left" onclick="listar_hojas_trabajo('.$goPage.')"></i></button>');
                                 
                             }
                            print("Página $NumPage de $TotalPaginas ");
                            
                            
                             
                             if($NumPage<>$TotalPaginas){
                                $goPage=$NumPage+1;
                                print('<button type="button" class="btn btn-default btn-sm" onclick="listar_hojas_trabajo('.$goPage.')"><i class="fa fa-chevron-right"></i></button>');
                            
                            }
                        $css->CerrarDiv();
                        
                    $css->CerrarDiv();  
                $css->CerrarDiv();
                
                $css->CrearDiv("", "table-responsive mailbox-messages", "", 1, 1);
                    print('<table class="table table-hover table-striped">');
                        print('<tbody>');
                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                            $hoja_trabajo_id=$datos_consulta["hoja_trabajo_id"];
                            print('<tr>');
                                print("<td class='mailbox-name'>");
                                    print('<div class="text-left">');
                                        print('<a title="Ver" onclick="frm_construir_hoja_trabajo(`'.$hoja_trabajo_id.'`)" class="btn btn-social-icon btn-bitbucket"><i class="fa fa-eye"></i></a> ');
                                        print('<a title="Exportar" class="btn btn-social-icon btn-success"><i class="fa fa-file-excel-o"></i></a> ');
                                        
                                    print('</div>');
                                print("</td>");
                                print("<td class='mailbox-name'>");
                                    print($datos_consulta["ID"]);
                                print("</td>");
                                print("<td class='mailbox-subject'>");
                                    print($datos_consulta["Fecha"]);
                                    
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:left'>");
                                    print($datos_consulta["nombre_tipo_negociacion"]);
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left'>");
                                    print('<b>'.utf8_encode($datos_consulta["Descripcion"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left'>");
                                    print('<b>'.($datos_consulta["nombre_estado"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left'>");
                                    print('<b>'.utf8_encode($datos_consulta["nombre_usuario"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.($datos_consulta["created"]).'</b>');
                                print("</td>");
                            print("</tr>");
                        }
                        print('</tbody>');
                    $css->CerrarTabla();
                $css->CerrarDiv();
                
                $css->CrearDiv("", "box-footer no-padding", "", 1, 1);
                
                $css->CerrarDiv();
                
                
            $css->CerrarDiv();
            
        break;//Fin caso 4  
        
        case 5://Formulario para crear una hoja de trabajo nueva
            
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            if($CmbIPS==''){
                $css->CrearTitulo("<strong>DEBE SELECCIONAR UNA IPS</strong>", "rojo");
                exit();
            }
            $datos_ips=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            
            $css->div("", "row", "", "", "", "", "");
                $css->div("", "col-md-12", "", "", "", "", "");
                    $css->CrearTitulo("CREAR UNA HOJA DE TRABAJO AUDITORÍA PARA LA IPS: <strong>".$datos_ips["Nombre"]."</strong>", "naranja");
                $css->Cdiv();
                                
                $css->CrearDiv("", "col-md-3", "left", 1, 1);
                    print("<strong>Tipo de Negociación:</strong><br>");
                    $sql="SELECT * FROM auditoria_tipos_anexo WHERE estado=1";
                    $consulta=$obCon->Query($sql);
                    $css->select("cmb_tipo_negociacion", "form-control", "cmb_tipo_negociacion", "", "", "", "");
                        $css->option("", "", "", "", "", "");
                            print("Seleccione un tipo de negociacion");
                        $css->Coption();
                        while($datos_consulta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $datos_consulta["ID"], "", "");
                                print($datos_consulta["tipo_negociacion"]);
                            $css->Coption();
                        }
                    $css->Cselect();
                $css->Cdiv();   
                $css->CrearDiv("", "col-md-7", "left", 1, 1);
                    print("<strong>Descripción:</strong><br>");
                    $css->input("text", "txt_descripcion", "form-control", "txt_descripcion", "Descripcion", "", "Descripción", "off", "", "");
                $css->Cdiv(); 
                $css->CrearDiv("", "col-md-2", "left", 1, 1);
                    print("<strong>Crear:</strong><br>");
                    $css->CrearBotonEvento("btn_crear_hoja_trabajo", "Crear", 1, "onclick", "confirma_crear_hoja_trabajo()", "rojo");
                $css->Cdiv(); 
                
            $css->Cdiv();
                       
        break;//Fin caso 5
        
        case 6://tabla de contratos agregados para construir la hoja de trabajo
            
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);  
                        
            $sql="SELECT *                      
                FROM auditoria_hojas_trabajo_contrato WHERE hoja_trabajo_id='$hoja_trabajo_id'

                ";
            $consulta=$obCon->Query($sql);
           
            
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Contratos Agregados</strong>", 2,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                                     
                    $css->ColTabla("<strong>Contrato</strong>", 1,"C");
                    $css->ColTabla("<strong>Eliminar</strong>", 1,"C");
                    
                $css->CierraFilaTabla();
            while($datos_consulta=$obCon->FetchAssoc($consulta)){
                $css->FilaTabla(16);
                    $item_id=$datos_consulta["ID"]; 
                    
                    $css->ColTabla("<span>".$datos_consulta["contrato"]."</span>", 1,"L");
                    print('<td>');
                        print('<div class="text-center">');
                            print('<a title="Eliminar" onclick="eliminar_contrato_hoja_trabajo(`'.$hoja_trabajo_id.'`,`'.$item_id.'`);"  class="btn btn-social-icon btn-danger btn-sm"><i class="fa fa-times"></i></a> ');
                            
                        
                        print('</div>');
                    print('</td>');
                    
                $css->CierraFilaTabla();
            }
            
            $css->CerrarTabla();
            
        break;//fin caso 6
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>