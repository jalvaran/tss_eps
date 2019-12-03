<?php
/**
 * Pagina para crear un ticket
 * 2019-05-20, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="tickets.php";
$myTitulo="Tickets TSS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];

$css->PageInit($myTitulo);
    //$css->div("", "col-md-12", "", "", "", "", "");
    $css->section("", "content-header", "", "");
        print("<h1>Módulo de Tickets");
            //print('<small id="info1">13 nuevos mensajes sin leer</small>');
        print("</h1>");
    $css->Csection();
    //print("<br>");
    $css->section("", "content", "", "");
    $css->CrearDiv("", "row", "left", 1, 1);
    $css->CrearDiv("", "col-md-2", "left", 1, 1);
    $css->CrearBotonEvento("BtnNuevoTicket", "Abrir Nuevo Ticket", 1, "onclick", "FormularioNuevoTicket()", "azul");
    $css->CrearDiv("", "box box-solid", "left", 1, 1);
    $css->CrearDiv("", "box-header with-border", "left", 1, 1);
    print('<h3 class="box-title">Carpetas</h3>');
    $css->CrearDiv("", "box-tools", "left", 1, 1);    
    print('  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="#" onclick="VerListadoTickets()"><i class="fa fa-inbox"></i> Tus Tickets
                  </a></li>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Filtros</h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li>
            ');
            
            $css->select("CmbEstadoTicketsListado", "form-control", "CmbEstadoTicketsListado", "", "", "onchange=VerListadoTickets()", "");
                $css->option("", "", "", 1, "", "");
                    print("Abiertos");
                $css->Coption();
                
                $css->option("", "", "", 0, "", "");
                    print("Cerrados");
                $css->Coption();
                
                $css->option("", "", "", 3, "", "");
                    print("Todos");
                $css->Coption();
            $css->Cselect();
            //print("<br>");
            
            $css->select("CmbProyectosTicketsListado", "form-control", "CmbProyectosTicketsListado", "", "", "onchange=VerListadoTickets();CargarModulosProyectosEnSelect(2);", "");
                $css->option("", "", "", '', "", "");
                    print("Todos los proyectos");
                $css->Coption();
                
                $Consulta=$obCon->ConsultarTabla("tickets_proyectos", "");
                while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosSelect["ID"], "", "");
                        print(utf8_encode($DatosSelect["Proyecto"]));
                    $css->Coption();
                }
                
            $css->Cselect();
            //print("<br>");
            
            $css->select("CmbModulosTicketsListado", "form-control", "CmbModulosTicketsListado", "", "", "onchange=VerListadoTickets();", "");
                $css->option("", "", "", '', "", "");
                    print("Todos los Módulos");
                $css->Coption();
                
                $Consulta=$obCon->ConsultarTabla("tickets_modulos_proyectos", "");
                while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosSelect["ID"], "", "");
                        print(utf8_encode($DatosSelect["NombreModulo"]));
                    $css->Coption();
                }
                
            $css->Cselect();
            //print("<br>");
            
            $css->select("CmbTiposTicketsListado", "form-control", "CmbTiposTicketsListado", "", "", "onchange=VerListadoTickets()", "");
                $css->option("", "", "", '', "", "");
                    print("Todos los Tipos");
                $css->Coption();
                
                $Consulta=$obCon->ConsultarTabla("tickets_tipo", "");
                while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosSelect["ID"], "", "");
                        print(utf8_encode($DatosSelect["TipoTicket"]));
                    $css->Coption();
                }
                
            $css->Cselect();
            //print("<br>");
            
            $css->select("CmbFiltroUsuario", "form-control", "CmbFiltroUsuario", "", "", "onchange=VerListadoTickets()", "");
                
                if($_SESSION["Role"]=='SUPERVISOR'){
                    $css->option("", "", "", 1, "", "");
                        print("De Todos los Usuarios");
                    $css->Coption();
                    $css->option("", "", "", 2, "", "");
                        print("Solo Míos");
                    $css->Coption();
                }else{
                    $css->option("", "", "", 2, "", "");
                        print("Solo Míos");
                    $css->Coption();
                }
                
            $css->Cselect();
    
        print('
        </li>
                
                
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>');
        $css->CrearDiv("", "col-md-10", "left", 1, 1);
        $css->CrearDiv("", "box-tools pull-right", "left", 1, 1);                
                print('<div class="input-group">');               
                    $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar", "", "", "onchange=VerListadoTickets()");

                print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
                          </div>');
            $css->CerrarDiv(); 
        $css->CerrarDiv();     
        $css->CrearDiv("", "col-md-10", "left", 1, 1);
             
            $css->CrearDiv("", "box box-primary", "left", 1, 1);
                $css->CrearDiv("DivDrawTickets", "box-header with-border", "left", 1, 1);  
                $css->CerrarDiv();
            $css->CerrarDiv();    
        $css->CerrarDiv();
       
        
        
        print(' </div>
                <!-- /.row -->
              </section>');
$css->PageFin();
$css->AddJSTextAreaEnriquecida();
print('<script src="jsPages/tickets.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>