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
$myTitulo="Plataforma TS5";
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
        print('<section class="content-header">
            <h1>
              Módulos de Tickets
              <small id="info1">13 nuevos mensajes sin leer</small>
            </h1>
            
          </section><br><section class="content">');
        
        print('<div class="row">
            <div class="col-md-3">
          <a href="#" class="btn btn-primary btn-block margin-bottom"><strong>Abrir Nuevo Ticket</strong></a>

          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Carpetas</h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="#" onclick="VerListadoTickets()"><i class="fa fa-inbox"></i> Tus Tickets
                  <span class="label label-primary pull-right">12</span></a></li>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Estado</h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="#"><i class="fa fa-circle-o text-red"></i> Abiertos</a></li>
                <li><a href="#"><i class="fa fa-circle-o text-green"></i> Cerrados</a></li>
                
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>');
        $css->CrearDiv("", "col-md-9", "left", 1, 1);
        $css->CrearDiv("", "box-tools pull-right", "left", 1, 1);                
                print('<div class="input-group">');               
                    $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar", "", "", "onchange=BuscarFactura()");

                print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
                          </div>');
            $css->CerrarDiv(); 
        $css->CerrarDiv();     
        $css->CrearDiv("", "col-md-9", "left", 1, 1);
             
            $css->CrearDiv("", "box box-primary", "left", 1, 1);
                $css->CrearDiv("DivDrawTickets", "box-header with-border", "left", 1, 1);  
                $css->CerrarDiv();
            $css->CerrarDiv();    
        $css->CerrarDiv();
       
        
        
        print(' </div>
                <!-- /.row -->
              </section>');
$css->PageFin();
print('<script src="jsPages/tickets.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>