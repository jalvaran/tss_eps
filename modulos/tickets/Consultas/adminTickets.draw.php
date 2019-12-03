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
            
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            $CmbEstado=$obCon->normalizar($_REQUEST["CmbEstado"]);
            $CmbProyectosTicketsListado=$obCon->normalizar($_REQUEST["CmbProyectosTicketsListado"]);  
            $CmbModulosTicketsListado=$obCon->normalizar($_REQUEST["CmbModulosTicketsListado"]);
            $CmbTiposTicketsListado=$obCon->normalizar($_REQUEST["CmbTiposTicketsListado"]);
            if($FechaInicial==''){
                exit("E1;Debes seleccionar una Fecha Inicial;FechaInicial");
            }
            if($FechaFinal==''){
                exit("E1;Debes seleccionar una Fecha Final;FechaFinal");
            }
            if($CmbEstado==''){
                $CmbEstado=0;
            }
            if($CmbProyectosTicketsListado==''){
                $CmbProyectosTicketsListado=0;
            }
            if($CmbModulosTicketsListado==''){
                $CmbModulosTicketsListado=0;
            }
            if($CmbTiposTicketsListado==''){
                $CmbTiposTicketsListado=0;
            }
            $page="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=38&FechaInicial=$FechaInicial&FechaFinal=$FechaFinal"; 
            $page.="&CmbEstado=$CmbEstado&CmbProyectosTicketsListado=$CmbProyectosTicketsListado&CmbModulosTicketsListado=$CmbModulosTicketsListado&CmbTiposTicketsListado=$CmbTiposTicketsListado";
            $Target="FramePDF";
            //$Target="_blank";
            print("<a id='LinkPDF' target='$Target' href='$page'></a>");
                        
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
                $css->select("CmbTipoTicket", "form-control", "CmbProyecto", "Tipo de Ticket:", "", "", "");
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
                $css->select("CmbProyecto", "form-control", "CmbProyecto", "Proyecto:", "", "", "");
                    $Consulta=$obCon->ConsultarTabla("tickets_proyectos", " WHERE Estado=1");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print($DatosProyectos["Proyecto"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->select("CmbModuloProyecto", "form-control", "CmbModuloProyecto", "Fase:", "", "", "");
                    $Consulta=$obCon->ConsultarTabla("tickets_modulos_proyectos", " WHERE Estado=1");
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
            
            $sql="SELECT Nombre,Apellido FROM usuarios WHERE idUsuarios='".$DatosTickets["idUsuarioSolicitante"]."'";
            $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
            $NombreSolicitante=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]; 
            $ExtensionesImagenes=array("png", "bmp", "jpg", "jpeg");
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print('<a href="#" onclick=VerTicket('.$idTicket.')><h3>Ticket No.'.$idTicket.'</h3></a>');
                //print("<h3 class='box-title'>Ticket No. $idTicket</h3>");
            $css->CerrarDiv();
            $css->CrearDiv("", "mailbox-read-info", "left", 1, 1);
            print('
                <h3>'.$DatosTickets["Asunto"].'</h3>
                <h5>De: '.$NombreSolicitante.'
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
                    $css->select("CmbCerrarTicket", "form-control", "CmbCerrarTicket", "Cerrar este Ticket?", "", "", "");
                        $css->option("", "", "", 0, "", "");
                            print("NO");
                        $css->Coption();
                        $css->option("", "", "", 1, "", "");
                            print("SI");
                        $css->Coption();
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