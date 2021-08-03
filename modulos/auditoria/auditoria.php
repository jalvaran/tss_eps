<?php
/**
 * Módulo de auditoria
 * 2021-07-27, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="auditoria.php";
$myTitulo="Auditoría TAGS";
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
    $css->Modal("ModalAcciones", "TS5", "", 1);
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();        
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    
    //$css->div("", "col-md-12", "", "", "", "", "");
    $css->section("", "content-header", "", "");
        print("<h1>Módulo de Auditoría");
            //print('<small id="info1">13 nuevos mensajes sin leer</small>');
        print("</h1>");
    $css->Csection();
    //print("<br>");
    $css->section("", "content", "", "");
    $css->CrearDiv("", "row", "left", 1, 1);
    $css->CrearDiv("", "col-md-2", "left", 1, 1);
    //$css->CrearBotonEvento("BtnNuevoTicket", "Abrir Nuevo Ticket", 1, "onclick", "FormularioNuevoTicket()", "azul");
    $css->CrearDiv("", "box box-solid", "left", 1, 1);
    $css->CrearDiv("", "box-header with-border", "left", 1, 1);
    print('<h3 class="box-title">Opciones</h3>');
    $css->CrearDiv("", "box-tools", "left", 1, 1);    
    print('  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="#" onclick="frm_subir_anexos()"><i class="fa fa-inbox"></i> Subir Anexos ALY </a></li>
                <li><a href="#" onclick="listar_hojas_trabajo()"><i class="fa fa-inbox"></i> Hojas de Trabajo </a></li>
                <li><a href="#" onclick="generalidades()"><i class="fa fa-inbox"></i> Generalidades </a></li>    
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <div class="box box-solid">
            
            <!-- /.box-header -->
            <div class="box-body no-padding" style="">
              <ul class="nav nav-pills nav-stacked">
                <li>
            ');
            
            
    
        print('
        </li>
                
                
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>');
        $css->CrearDiv("", "col-md-10", "left", 1, 1);
        $css->CrearDiv("", "col-md-12", "left", 1, 1); 
        
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                if($TipoUser=="administrador"){
                    $sql="SELECT NIT as idIPS, Nombre FROM ips";
                }else{
                    $sql="SELECT r.idIPS,i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
                }
                $Consulta=$obCon->Query($sql);
                //$css->select($id, $class, $name, $title, $vectorhtml, $Script, $style)
                $css->select("CmbIPS", "form-control", "CmbIPS", "IPS<br>", "", "onchange=listar_hojas_trabajo()", "");
                    while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosIPS["idIPS"], "", "");
                            print($DatosIPS["Nombre"]." ".$DatosIPS["idIPS"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                $sql="SELECT * FROM eps ORDER BY ID";
                $Consulta=$obCon->Query($sql);

                $css->select("CmbEPS", "form-control", "CmbEPS", "EPS<br>", "", "onchange=listar_hojas_trabajo()", "");
                    while($DatosEPS=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosEPS["NIT"], "", "");
                            print($DatosEPS["Nombre"]." ".$DatosEPS["NIT"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                $sql="SELECT * FROM auditoria_tipos_anexo";
                $Consulta=$obCon->Query($sql);
                $css->select("tipo_anexo", "form-control", "tipo_anexo", "Tipo de Negociacion:<br>", "", "", "");
                    $css->option("", "", "", "", "", "");
                        print("Seleccione el Tipo de Anexo");
                    $css->Coption();
                    
                    while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $datos_consulta["ID"], "", "");
                            print($datos_consulta["tipo_negociacion"]);
                        $css->Coption();
                    }
                            
                    
                $css->Cselect();
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                print("<strong>Buscar</strong><br>");
                print('<div class="input-group">');               
                    $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar", "", "", "onchange=listar_hojas_trabajo()");

                print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
                          </div>');
            $css->CerrarDiv();
        
        $css->CerrarDiv(); 
        $css->CerrarDiv(); 
        $css->CrearDiv("", "col-md-10", "left", 1, 1);
             
            $css->CrearDiv("", "box box-primary", "left", 1, 1);
                $css->CrearDiv("general_div", "box-header with-border", "left", 1, 1);  
                $css->CerrarDiv();
            $css->CerrarDiv();    
        $css->CerrarDiv();
       
        
        
        print(' </div>
                <!-- /.row -->
              </section>');
$css->PageFin();
$css->AddJSTextAreaEnriquecida();
print('<script src="jsPages/auditoria.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>