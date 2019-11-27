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
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" WHERE Estado <=6 ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" AND Asunto like '$Busqueda%' ";
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
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            //$css->CrearTitulo("Listado de Tickets", "azul");
            
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print("<h3 class='box-title'>Listado de Tickets</h3>");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "box-body no-padding", "", 1, 1);
                $css->CrearDiv("", "mailbox-controls", "", 1, 1);
                    print('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>');
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
                            print("<tr>");
                                print("<td class='mailbox-name'>");
                                    print('<a href="#">'.$DatosTickets["NombreSolicitante"].' '.$DatosTickets["ApellidoSolicitante"].'</a>');
                                print("</td>");
                                print("<td class='mailbox-subject'>");
                                    print('<b>'.$DatosTickets["Asunto"].'</b>');
                                print("</td>");
                                
                                print("<td class='mailbox-date' style='text-align:right'>");
                                    print('<b>'.$DatosTickets["FechaApertura"].'</b>');
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
                $css->select("CmbTipoTicket", "form-control", "CmbProyecto", "Tipo de Ticket:", "", "", "");
                    $css->option("", "", "", "", "", "");
                            print("Seleccione el Tipo de Ticket");
                        $css->Coption();
                    $Consulta=$obCon->ConsultarTabla("tickets_tipo", "");
                    while($DatosProyectos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosProyectos["ID"], "", "");
                            print($DatosProyectos["TipoTicket"]);
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
            $css->input("text", "TxtAsunto", "form-control", "TxtAsunto", "", "", "Título", "off", "", "");
            print("<br>");
            $css->CrearDiv("", "form-group", "left", 1, 1);
                $css->textarea("TxtMensaje", "form-control", "TxtMensaje", "", "Mensaje", "", "", "style='height:400px;'");
                       
            $css->Ctextarea();
            $css->CerrarDiv();    
            //print("<br>");
            $css->CrearDiv("", "col-md-10", "left", 1, 1);
            print("<strong>Adjuntar: </strong>");
            $css->input("file", "upAdjuntosTickets", "form-control", "upAdjuntosTickets", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-2", "left", 1, 1);
            print("<strong>Guardar este Ticket: </strong>");
            $css->CrearBotonEvento("BtnGuardarTicket", "Guardar", 1, "onclick", "CrearTicket()", "azul");
            $css->CerrarDiv();
        break;//Fin caso 2
      
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>