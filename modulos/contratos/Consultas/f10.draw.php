<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$TipoUser=$_SESSION['tipouser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/f10_construct.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //dibuje el listado de contratos en el f10
            
            $sql="SELECT role,TipoUser FROM usuarios WHERE idUsuarios='$idUser'";
            $datos_usuario_roles=$obCon->FetchAssoc($obCon->Query($sql));
            $Busqueda=$obCon->normalizar($_REQUEST["TxtBusquedas"]);
            $ips_id=$obCon->normalizar($_REQUEST["ips_id"]);
            $estado=$obCon->normalizar($_REQUEST["estado"]);
            $Condicional=" WHERE ID>0 ";
            $OrderBy=" ORDER BY ID DESC";
            if($datos_usuario_roles["role"]=='CARGUE'){
                $Condicional.=" AND ResponsableCargueActa = '$idUser' ";
            }
            if($Busqueda<>''){
                $Condicional.=" AND NumeroContrato like '%$Busqueda%' ";
            }
            if($ips_id<>''){
                $Condicional.=" AND NitIPSContratada = '$ips_id' ";
            }
            if($estado<>''){
                $Condicional.=" AND estado = '$estado' ";
            }
            if($TipoUser<>"administrador"){
                $Condicional.=" AND exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE `vista_f10`.NitIPSContratada=t2.idIPS AND t2.idUsuario='$idUser') ";
            }
            
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            
            $statement=" `vista_f10` $Condicional ";
            $st_f10="SELECT * FROM `vista_f10` $Condicional ";
            $st_f10_control_cambios=" `vista_f10_control_cambios` $Condicional ";            
            $limit = 10;
            $startpoint = ($NumPage * $limit) - $limit;
            
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num` FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $OrderBy $Limit ");
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print("<h3 class='box-title'>F10</h3>");
                print('<span class="label label-primary pull-right"><h4><strong>'.$ResultadosTotales.'</strong></h4></span>');
            $css->CerrarDiv();
            
            $css->CrearDiv("", "box-body no-padding", "", 1, 1);
                $css->CrearDiv("", "mailbox-controls", "", 1, 1);
                    print('<button type="button" class="btn btn-default btn-sm" onclick="listarF10();"><i class="fa fa-refresh"></i></button>');
                    //print($st_f10);
                    $st_f10= base64_encode(urlencode($st_f10));
                    $link_export_excel="procesadores/f10_excel.process.php?Accion=1&st=$st_f10";
                    print(' <a class="btn btn-default btn-success btn-sm" href="'.$link_export_excel.'" target="_blank"><i class="fa fa-file-excel-o" style="color:white"></i></a>');
                    $css->CrearDiv("", "pull-right", "", 1, 1);
                       
                        print('<div class="input-group">');   
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            if($NumPage>1){
                                 $goPage=$NumPage-1;
                                 
                                 print('<button type="button" class="btn btn-default btn-sm" onclick="listarF10('.$goPage.')"><i class="fa fa-chevron-left" ></i></button>');
                                 
                             }
                            print("Página $NumPage de $TotalPaginas ");
                            
                            
                             
                             if($NumPage<>$TotalPaginas){
                                $goPage=$NumPage+1;
                                print('<button type="button" class="btn btn-default btn-sm" onclick="listarF10('.$goPage.')"><i class="fa fa-chevron-right"></i></button>');
                            
                            }
                        $css->CerrarDiv();
                        
                    $css->CerrarDiv();  
                $css->CerrarDiv();
                
                $css->CrearDiv("", "table-responsive mailbox-messages", "", 1, 1);
                    print('<table class="table table-hover table-striped">');
                        print('<tbody>');
                        print("<tr>");
                            print('<th><strong>Acciones</strong></th>');
                            print('<th><strong>ID</strong></th>');
                            print('<th><strong>NIT IPS</strong></th>');
                            print('<th><strong>Razon Social IPS</strong></th>');
                            print('<th><strong>Contrato</strong></th>');
                            print('<th><strong>Modalidad</strong></th>');
                            print('<th><strong>Fecha Inicial</strong></th>');
                            print('<th><strong>Fecha Final</strong></th>');
                            print('<th><strong>Valor</strong></th>');
                            print('<th><strong>Estado</strong></th>');
                            print('<th><strong>Asignar Cargue:</strong></th>');
                            print('<th><strong>Última Actualización Manual</strong></th>');
                            print('<th><strong>Última Actualización Automatica</strong></th>');
                        print("</tr>");
                        $sql="SELECT idUsuarios AS ID,CONCAT(Nombre,' ',Apellido) AS nombre_usuario FROM usuarios WHERE Role='CARGUE'";
                        $consulta_usuarios=$obCon->Query($sql);
                        $usuarios_cargue=[];                        
                        while($datos_usuario_cargue=$obCon->FetchAssoc($consulta_usuarios)){
                            $ID=$datos_usuario_cargue["ID"];
                            $usuarios_cargue[$ID]["nombre"]=utf8_encode($datos_usuario_cargue["nombre_usuario"]);
                        }
                        
                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                            $item_id=$datos_consulta["ID"];
                            print("<tr>");
                                print("<td class='mailbox-name'>");
                                    print('<a class="btn btn-default btn-primary btn-sm" href="#" onclick="ver_f10('.$item_id.')"><li class="fa fa-edit" style="color:white"></li></a>');
                                    $link_export_excel="procesadores/f10_excel.process.php?Accion=2&contrato_id=".$datos_consulta["contrato_id"];
                                    print(' <a class="btn btn-default btn-danger btn-sm" href="'.$link_export_excel.'" target="_blank"><i class="fa fa-file-excel-o" style="color:white"></i></a>');
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.($datos_consulta["ID"]).'</b>');
                                print("</td>");                               
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.($datos_consulta["NitIPSContratada"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.utf8_encode($datos_consulta["RazonSocial"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.$datos_consulta["NumeroContrato"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:green'>");
                                    print('<b>'.($datos_consulta["Modalidad"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.($datos_consulta["FechaInicioContrato"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.($datos_consulta["FechaFinalContrato"]).'</b>');
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:right;color:black'>");
                                    print('<b>'.number_format($datos_consulta["ValorContrato"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left'>");
                                    print('<b>'.$datos_consulta["nombre_estado"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left'>");
                                $caja_id="ResponsableCargueActa_".$item_id;
                                    $css->select($caja_id, "form-control", "ResponsableCargueActa", "", "", 'onchange="editar_campo_f10(`'.$item_id.'`,`ResponsableCargueActa`,`'.$caja_id.'`)"', 'style="width:150px;"');
                                        $css->option("", "", "", "", "", "");
                                            print("Seleccione...");
                                        $css->Coption();
                                        foreach ($usuarios_cargue as $key => $value) {
                                            $sel=0;
                                            if($datos_consulta["ResponsableCargueActa"]==$key){
                                                $sel=1;
                                            }
                                            $css->option("", "", "", $key, "", "",$sel);
                                                print($usuarios_cargue[$key]["nombre"]);
                                            $css->Coption();
                                        }
                                    $css->Cselect();
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.$datos_consulta["FechaActualizacionManual"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:left;color:black'>");
                                    print('<b>'.$datos_consulta["FechaActualizacion"].'</b>');
                                print("</td>");
                            print("</tr>");
                        }
                        print('</tbody>');
                    $css->CerrarTabla();
                $css->CerrarDiv();
                
                $css->CrearDiv("", "box-footer no-padding", "", 1, 1);
                
                $css->CerrarDiv();
                
                
            $css->CerrarDiv();
            
            
            
        break;//Fin caso 1
        
        case 2://Formulario para el ingreso de informacion por parte del liquidador
            $obF10 = new F10_Construct($idUser);
            $f10_id=$obCon->normalizar($_REQUEST["f10_id"]);
            $sql="SELECT role,TipoUser FROM usuarios WHERE idUsuarios='$idUser'";
            $datos_usuario_roles=$obCon->FetchAssoc($obCon->Query($sql));   
            $datos_f10=$obCon->DevuelveValores("vista_f10", "ID", $f10_id);
            
            $css->CrearDiv("", "row", "left", 1, 1);
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $css->CrearBotonEvento("btnToogle", "Cerrar/Abrir Cajas", 1, "onclick", "toogle_box()", "rojo");
                $css->CerrarDiv();
            $css->CerrarDiv();
            print("<br>");
            $css->CrearDiv("", "row", "left", 1, 1);
                //caja con la informacion general del contrato
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $html=$obF10->get_html_general_info($datos_f10);
                    $css->box_toogle("INFORMACIÓN DEL CONTRATOS IPS", $html);
                $css->CerrarDiv();

                //caja con la informacion de las glosas
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $html=$obF10->get_html_glosas_conciliar($datos_f10);
                    $css->box_toogle("GLOSAS POR CONCILIAR", $html,"success");
                $css->CerrarDiv();

                //cuentas por pagar y conciliaciones de cartera
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $html=$obF10->get_html_cuentas_x_pagar($datos_f10);
                    $css->box_toogle("CUENTAS X PAGAR Y CONCILIACIONES", $html);
                $css->CerrarDiv();
            $css->CerrarDiv();
            $css->CrearDiv("", "row", "left", 1, 1);
                //Liquidación de contratos
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $html=$obF10->get_html_liquidacion_contratos($datos_f10);
                    $css->box_toogle("LIQUIDACIÓN DE CONTRATOS", $html);
                $css->CerrarDiv();
                
                //Gestión de Liquidación de contratos
                $css->CrearDiv("", "col-md-8", "left", 1, 1);
                    $html=$obF10->get_html_gestion_liquidacion_contratos($datos_f10);
                    $css->box_toogle("GESTIÓN DE LIQUIDACIÓN DE CONTRATOS", $html,"warning");
                $css->CerrarDiv();
                
                //Cargue de contratos
                
                if($datos_usuario_roles["role"]=='CARGUE'){
                    $css->CrearDiv("", "col-md-4", "left", 1, 1);
                        $html=$obF10->get_html_cargue_actas($datos_f10);
                        $css->box_toogle("CARGUE DE ACTAS", $html,"danger");
                    $css->CerrarDiv();
                }
                
            
            
                $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->CrearTitulo("<strong>Subir adjuntos a este F10</strong>", "verde");
                print('<div class="panel">

                            <div class="panel-body">
                                <form data-f10_id="'.$f10_id.'" action="/" class="dropzone dz-clickable" id="f10_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                            </div>
                        </div>
                    ');
                $css->Cdiv();
                $css->CrearDiv("div_adjuntos_f10", "col-md-4", "center", 1, 1);

                $css->CerrarDiv();
            
            $css->CerrarDiv();
            
        break;//Fin caso 2
            
        case 3: //Dibuja los adjuntos de un f10
            
            $f10_id=$obCon->normalizar($_REQUEST["f10_id"]);                
            $datos_f10=$obCon->DevuelveValores("f10", "ID", $f10_id);
            $contrato_id=$datos_f10["contrato_id"];
            $css->CrearTitulo("Adjuntos de este f10");
            $css->CrearTabla();
                
                $sql="SELECT t1.*
                        FROM contratos_adjuntos t1 
                        WHERE contrato_id='$contrato_id' 
                            ";
                $Consulta=$obCon->Query($sql);
                
                if($obCon->NumRows($Consulta)>0){
                    
                    $css->FilaTabla(16);
                    
                        $css->ColTabla("<strong>Adjuntos del Contrato</strong>", 3,"C");
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);

                        $css->ColTabla("ID", 1);
                        $css->ColTabla("Nombre de Archivo", 2);

                    $css->CierraFilaTabla();
                    
                    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosConsulta["ID"];
                        $Nombre=$DatosConsulta["NombreArchivo"];
                        $css->FilaTabla(14);

                            $css->ColTabla($idItem, 1);

                            print('<td colspan=2 style="text-align:center;color:blue;font-size:18px;">');
                                $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                                print('<a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a>');
                            print('</td>');

                        $css->CierraFilaTabla();
                    }
                    
                }
                if($datos_f10["soporte_acta_conciliacion"]<>''){
                    $css->FilaTabla(16);
                        $css->FilaTabla(16);
                        $css->ColTabla("<strong>Acta de Conciliación:</strong>", 3,"C");
                        
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        print('<td colspan=3 style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $datos_f10["soporte_acta_conciliacion"]);
                            print('<a href="'.$Ruta.'" target="blank">Acta de Concialiación <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
                if($datos_f10["soporte_acta_liquidacion"]<>''){
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Acta de Liquidación:</strong>", 3,"C");
                        
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        print('<td colspan=3 style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $datos_f10["soporte_acta_liquidacion"]);
                            print('<a href="'.$Ruta.'" target="blank">Acta de Liquidación <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
                $css->FilaTabla(16);
                    $colspan=2;
                    if($TipoUser=='administrador'){
                        $colspan=3;
                    }
                    $css->ColTabla("<strong>Adjuntos F10</strong>", $colspan,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    if($TipoUser=='administrador'){
                        $css->ColTabla("Eliminar", 1);
                    }
                    
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM f10_adjuntos t1 
                        WHERE f10_id='$f10_id' 
                            ";
                $Consulta=$obCon->Query($sql);
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $Nombre=$DatosConsulta["NombreArchivo"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($idItem, 1);
                       
                        print('<td style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                            print('<a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                        if($TipoUser=='administrador'){
                            print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   

                                $css->li("", "fa  fa-remove", "", "onclick=EliminarItemf10(`1`,`$idItem`,`$f10_id`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                $css->Cli();
                            print("</td>");
                        }  
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 3
               
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>