<?php

session_start();
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
        case 1: //Dibuja el listado general de tickets
            
            $TipoUser=$_SESSION["tipouser"];
            
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //$Busqueda= str_replace(" ", "%", $Busqueda);
            //print($Busqueda);
            $CmbEstadoTicketsListado=$obCon->normalizar($_REQUEST["CmbEstadoTicketsListado"]);
            $CmbFiltroUsuario=$obCon->normalizar($_REQUEST["CmbFiltroUsuario"]);
            
            if($CmbEstadoTicketsListado==0){
                $Condicional=" WHERE Estado>9 ";
                $OrderBy=" ORDER BY Prioridad DESC,FechaActualizacion DESC";
            }
            if($CmbEstadoTicketsListado==1){
                $Condicional=" WHERE Estado<=9 ";
                $OrderBy=" ORDER BY Prioridad DESC,FechaActualizacion ASC";
            }
            if($CmbEstadoTicketsListado==3){
                $Condicional=" WHERE Estado>0 ";
                $OrderBy=" ORDER BY Prioridad DESC,FechaActualizacion DESC";
            }
            if($CmbFiltroUsuario==2){
                $Condicional.=" AND (idUsuarioSolicitante='$idUser' or idUsuarioAsignado='$idUser') ";
            }
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" AND ( ID='$Busqueda' or Asunto like '%$Busqueda%' )";
                    //$Condicional.=" AND ( ID='$Busqueda' or MATCH(Asunto) AGAINST ('%$Busqueda%') )";
                    
                }
                
            }
            
            if(isset($_REQUEST['CmbProyectosTicketsListado'])){
                $Proyecto=$obCon->normalizar($_REQUEST['CmbProyectosTicketsListado']);
                if($Proyecto<>''){
                    $Condicional.=" AND idProyecto='$Proyecto' ";
                    
                    
                }
                
            }
            
            if(isset($_REQUEST['CmbModulosTicketsListado'])){
                
                $idModuloProyecto=$obCon->normalizar($_REQUEST['CmbModulosTicketsListado']);
                if($idModuloProyecto<>''){
                    $Condicional.=" AND idModuloProyecto='$idModuloProyecto' ";
                    
                    
                }
                
            }
            
            if(isset($_REQUEST['CmbTiposTicketsListado'])){
                $TipoTicket=$obCon->normalizar($_REQUEST['CmbTiposTicketsListado']);
                if($TipoTicket<>''){
                    $Condicional.=" AND TipoTicket='$TipoTicket' ";
                    
                    
                }
                
            }
            
            if($TipoUser=="ips"){
                $Condicional.=" AND idUsuarioSolicitante='$idUser' ";
            }
            
            
            $statement=" `vista_tickets` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 15;
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
                print("<h3 class='box-title'>Listado de Tickets</h3>");
                print('<span class="label label-primary pull-right"><h4><strong>'.$ResultadosTotales.'</strong></h4></span>');
            $css->CerrarDiv();
            
            $css->CrearDiv("", "box-body no-padding", "", 1, 1);
                $css->CrearDiv("", "mailbox-controls", "", 1, 1);
                    print('<button type="button" class="btn btn-default btn-sm" onclick="VerListadoTickets()"><i class="fa fa-refresh"></i></button>');
                    $css->CrearDiv("", "pull-right", "", 1, 1);
                       
                        print('<div class="input-group">');   
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            if($NumPage>1){
                                 $goPage=$NumPage-1;
                                 
                                 print('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left" onclick="VerListadoTickets('.$goPage.')"></i></button>');
                                 
                             }
                            print("Página $NumPage de $TotalPaginas ");
                            
                            
                             
                             if($NumPage<>$TotalPaginas){
                                $goPage=$NumPage+1;
                                print('<button type="button" class="btn btn-default btn-sm" onclick="VerListadoTickets('.$goPage.')"><i class="fa fa-chevron-right"></i></button>');
                            
                            }
                        $css->CerrarDiv();
                        
                    $css->CerrarDiv();  
                $css->CerrarDiv();
                
                $css->CrearDiv("", "table-responsive mailbox-messages", "", 1, 1);
                    print('<table class="table table-hover table-striped">');
                        print('<tbody>');
                        while($DatosTickets=$obCon->FetchAssoc($Consulta)){
                            $idTicket=$DatosTickets["ID"];
                            print("<tr>");
                                print("<td class='mailbox-name'>");
                                    print('<a href="#" onclick=VerTicket('.$idTicket.')>'.$DatosTickets["ID"].'</a>');
                                print("</td>");
                                print("<td class='mailbox-subject'>");
                                    print('<a href="#" onclick=VerTicket('.$idTicket.')><strong>'.utf8_encode($DatosTickets["NombreSolicitante"])." ".utf8_encode($DatosTickets["ApellidoSolicitante"])." -> ".utf8_encode($DatosTickets["NombreAsignado"])." ".utf8_encode($DatosTickets["ApellidoAsignado"])." </strong>: ".utf8_encode($DatosTickets["Asunto"]).'</a>');
                                    
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.$DatosTickets["NombreEstado"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.$DatosTickets["NombrePrioridad"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.utf8_encode($DatosTickets["NombreProyecto"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.utf8_encode($DatosTickets["NombreModulo"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.utf8_encode($DatosTickets["NombreTipoTicket"]).'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.$DatosTickets["FechaApertura"].'</b>');
                                print("</td>");
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.$DatosTickets["FechaActualizacion"].'</b>');
                                print("</td>");
                            print("</tr>");
                        }
                        print('</tbody>');
                    $css->CerrarTabla();
                $css->CerrarDiv();
                
                $css->CrearDiv("", "box-footer no-padding", "", 1, 1);
                
                $css->CerrarDiv();
                
                
            $css->CerrarDiv();
            
            
           
            
        break; //Fin caso 1
        
        case 2: //Formulario Nuevo Ticket
            
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print("<h3 class='box-title'>Nuevo Ticket</h3>");
            $css->CerrarDiv();
             print("<br>");
             $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbUsuarioDestino", "form-control", "CmbUsuarioDestino", "Para:", "", "", "");
                    $sql="SELECT idUsuarios,Nombre, Apellido FROM usuarios WHERE Habilitado='SI'";
                    $Consulta=$obCon->Query($sql);
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["idUsuarios"], "", "");
                            print($DatosProyectos["Nombre"]." ".$DatosProyectos["Apellido"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbTipoTicket", "form-control", "CmbTipoTicket", "Tipo de Ticket", "", "", "");
                    $css->option("", "", "", "", "", "");
                            print("Seleccione el Tipo de Ticket");
                        $css->Coption();
                    $Consulta=$obCon->ConsultarTabla("tickets_tipo", "");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print(utf8_encode($DatosProyectos["TipoTicket"]));
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbProyecto", "form-control", "CmbProyecto", "Proyecto:", "", "onchange=CargarModulosProyectosEnSelect(1);", "");
                    $Consulta=$obCon->ConsultarTabla("tickets_proyectos", " WHERE Estado=1");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print(utf8_encode($DatosProyectos["Proyecto"]));
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbModuloProyecto", "form-control", "CmbModuloProyecto", "Fase:", "", "", "");
                    $Consulta=$obCon->ConsultarTabla("tickets_modulos_proyectos", " WHERE Estado=1 AND idProyecto='1'");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print($DatosProyectos["NombreModulo"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();            
            print("<br><br><br><br>");
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbPrioridad", "form-control", "CmbPrioridad", "Prioridad:", "", "", "");
                    $Consulta=$obCon->ConsultarTabla("tickets_prioridad", "");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print($DatosProyectos["Prioridad"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();  
            $css->CrearDiv("", "col-md-9", "left", 1, 1);
                print("<strong>Título:</strong>");
                $css->input("text", "TxtAsunto", "form-control", "TxtAsunto", "", "", "Título", "off", "", "");
            $css->CerrarDiv();  
            print("<br><br><br><br>");
            $css->CrearDiv("", "form-group", "left", 1, 1);
                
                $css->textarea("TxtMensaje", "form-control", "TxtMensaje", "", "Mensaje", "", "", "style='height:400px;'");
                       
            $css->Ctextarea();
            $css->CerrarDiv();    
            //print("<br>");
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 1: </strong>");
            $css->input("file", "upAdjuntosTickets1", "form-control", "upAdjuntosTickets1", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 2: </strong>");
            $css->input("file", "upAdjuntosTickets2", "form-control", "upAdjuntosTickets2", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 3: </strong>");
            $css->input("file", "upAdjuntosTickets3", "form-control", "upAdjuntosTickets3", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Guardar este Ticket: </strong>");
            $css->CrearBotonEvento("BtnGuardarTicket", "Guardar", 1, "onclick", "CrearTicket()", "azul");
            $css->CerrarDiv();
        break;//Fin caso 2
        
        case 3: //Ver  los mensajes de un ticket
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);
            $DatosTickets=$obCon->DevuelveValores("tickets", "ID", $idTicket);
            
            $sql="SELECT t1.Nombre,t1.Apellido,
                    (SELECT NombreCargo FROM empresa_cargos t2 WHERE t2.ID=t1.Cargo LIMIT 1) AS NombreCargo,
                    (SELECT NombreProceso FROM empresa_nombres_procesos t3 WHERE t3.ID=t1.Proceso LIMIT 1) AS NombreProceso
                    FROM usuarios t1 WHERE t1.idUsuarios='".$DatosTickets["idUsuarioSolicitante"]."'";
            $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
            $NombreSolicitante=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]."<br>".$DatosUsuarioCreador["NombreCargo"]."<br>".$DatosUsuarioCreador["NombreProceso"]; 
            $ExtensionesImagenes=array("png", "bmp", "jpg", "jpeg");
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print('<a href="#" onclick=VerTicket('.$idTicket.')><h3>Ticket No.'.$idTicket.'</h3></a>');
                //print("<h3 class='box-title'>Ticket No. $idTicket</h3>");
            $css->CerrarDiv();
            $css->CrearDiv("", "mailbox-read-info", "left", 1, 1);
            print('
                <h3>'.$DatosTickets["Asunto"].'</h3>
                <h5>De: '.utf8_encode($NombreSolicitante).'
                  <span class="mailbox-read-time pull-right">'.$DatosTickets["FechaApertura"].'</span></h5>
              </div>');
            $Consulta=$obCon->ConsultarTabla("tickets_mensajes", "WHERE idTicket='$idTicket'");
            $i=0;
            while($DatosMensajes=$obCon->FetchAssoc($Consulta)){
                $i=$i+1;
                if($i==1){
                    $css->CrearTitulo("Mensaje No. $i");
                }else{
                    $NoRespuesta=$i-1;
                    $sql="SELECT Nombre,Apellido FROM usuarios WHERE idUsuarios='".$DatosMensajes["idUser"]."'";
                    $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
                    $NombreUsuarioRespuesta=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]; 
                    $css->CrearTitulo("Respuesta No. $NoRespuesta por <strong>$NombreUsuarioRespuesta</strong>, el ".$DatosMensajes["Created"],"verde");
                }
                $idMensaje=$DatosMensajes["ID"];
                $css->CrearDiv("", "mailbox-read-message", "left", 1, 1);
                    echo($DatosMensajes["Mensaje"]);
                $css->CerrarDiv();
                print("<hr>");
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $css->input("file", "upAdjuntosMensajes_$idMensaje", "form-control", "upAdjuntosMensajes_$idMensaje", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
                $css->CerrarDiv();
                
                $css->CrearDiv("", "col-md-2", "left", 1, 1);
                    $css->CrearBotonEvento("BtnAgregarAdjunto_$idMensaje", "Adjuntar", 1, "onclick", "AgregarAdjunto(`$idMensaje`,`$idTicket`)", "verde");
                $css->CerrarDiv();
                print("<br><br>");
                $css->CrearDiv("", "box-footer", "left", 1, 1);
                    $ConsultaAdjuntos=$obCon->ConsultarTabla("tickets_adjuntos", "WHERE idMensaje='$idMensaje'");
                    if($obCon->NumRows($ConsultaAdjuntos)){
                        print('<ul class="mailbox-attachments clearfix">');
                            while($DatosAdjuntos=$obCon->FetchAssoc($ConsultaAdjuntos)){

                                print('<li>');
                                    $ClassIcon="fa fa-file-o";
                                    $Extension=strtolower($DatosAdjuntos["Extension"]);
                                    if(!in_array($Extension,$ExtensionesImagenes)){
                                        if($Extension=='pdf'){
                                            $ClassIcon="fa fa-file-pdf-o";
                                        }
                                        if($Extension=='xls' or $Extension=='xlsx'){
                                            $ClassIcon="fa fa-file-excel-o";
                                        }
                                        if($Extension=='doc' or $Extension=='docx'){
                                            $ClassIcon="fa fa-file-word-o";
                                        }
                                        if($Extension=='zip' or $Extension=='rar'){
                                            $ClassIcon="fa fa-file-zip-o";
                                        }
                                        print('<span class="mailbox-attachment-icon"><i class="'.$ClassIcon.'"></i></span>');
                                    }else{
                                        print('<span class="mailbox-attachment-icon has-img"><img src="'.substr($DatosAdjuntos["Ruta"], 3).'" alt="NO"></span>');
                                                
                                    }
                                    $css->CrearDiv("", "mailbox-attachment-info", "center", 1, 1);
                                        print('<a href="'.substr($DatosAdjuntos["Ruta"], 3).'" target="blank" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> '.$DatosAdjuntos["NombreArchivo"].'</a>');
                                        $Tamano=$DatosAdjuntos["Tamano"]." Bytes";
                                        if($DatosAdjuntos["Tamano"]>=1000 and $DatosAdjuntos["Tamano"]<1000000){
                                            $Tamano= number_format($DatosAdjuntos["Tamano"]/1000,2)." KB";
                                        }
                                        if($DatosAdjuntos["Tamano"]>=1000000 and $DatosAdjuntos["Tamano"]<1000000000){
                                            $Tamano= number_format($DatosAdjuntos["Tamano"]/1000000,2)." MB";
                                        }
                                        if($DatosAdjuntos["Tamano"]>=1000000000 ){
                                            $Tamano= number_format($DatosAdjuntos["Tamano"]/1000000000,2)." GB";
                                        }
                                        print('<span class="mailbox-attachment-size">'.$Tamano.'</span>');
                                    $css->CerrarDiv();
                                print('</li>');
                            }
                        print("</ul>");
                    }
                
                $css->CerrarDiv();
            }
                        
            $css->CrearDiv("", "col-md-2", "left", 1, 1);
            
            $css->CrearBotonEvento("BtnResponderTicket", "Responder", 1, "onclick", "FormularioResponderTicket($idTicket)", "azul");
            $css->CerrarDiv();
        break;//Fin caso 3
        
        case 4: //Formulario Nueva Respuesta
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);
            $DatosTickets=$obCon->DevuelveValores("tickets", "ID", $idTicket);
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                $css->CrearDiv("", "col-md-6", "left", 1, 1);
                    print("<h3 class='box-title'><strong>Responder el Ticket $idTicket</strong></h3>");
                    print("<h6 >".$DatosTickets["Asunto"]."</h6>");
                $css->CerrarDiv();
                $css->CrearDiv("", "col-md-2", "left", 1, 1);
                    $css->select("CmbCerrarTicket", "form-control", "CmbCerrarTicket", "Estado:", "", "", "");
                        $sql="SELECT * FROM tickets_estados ORDER BY ID";
                        $Consulta=$obCon->Query($sql);
                        while($DatosEstados=$obCon->FetchAssoc($Consulta)){
                            if($DatosTickets["Estado"]==$DatosEstados["ID"]){
                                $Seleccionar=1;
                            }else{
                                $Seleccionar=0;
                            }
                            //$css->option($id, $class, $title, $value, $vectorhtml, $Script, $Seleccionar, $ng_app);
                        
                            $css->option("", "", "", $DatosEstados["ID"], "", "",$Seleccionar);
                                print($DatosEstados["Estado"]);
                            $css->Coption();
                        }
                        
                        
                    $css->Cselect();
                $css->CerrarDiv();
            $css->CerrarDiv();
            print("<br>");
            
            $css->CrearDiv("", "form-group", "left", 1, 1);
                $css->textarea("TxtMensaje", "form-control", "TxtMensaje", "", "Mensaje", "", "", "style='height:400px;'");
                       
            $css->Ctextarea();
            $css->CerrarDiv();    
            //print("<br>");
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 1: </strong>");
            $css->input("file", "upAdjuntosTickets1", "form-control", "upAdjuntosTickets1", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 2: </strong>");
            $css->input("file", "upAdjuntosTickets2", "form-control", "upAdjuntosTickets2", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 3: </strong>");
            $css->input("file", "upAdjuntosTickets3", "form-control", "upAdjuntosTickets3", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Guardar esta Respuesta: </strong>");
            $css->CrearBotonEvento("BtnGuardarTicket", "Guardar Respuesta", 1, "onclick", "GuardarRespuesta($idTicket)", "azul");
            $css->CerrarDiv();
        break;//Fin caso 4
      
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>