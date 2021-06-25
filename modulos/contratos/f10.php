<?php
/**
 * Pagina para administrar el f10
 * 2020-03-08, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="f10.php";
$myTitulo="Plataforma TAGS";
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
    
    print("<br>");
    $css->CrearDiv("div_spinner", "", "left", 1, 1);
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "left", 1, 1);
        $css->CrearBotonEvento("btnCrearF10", "Crear y Actualizar F10", 1, "onclick", "IniciarCreacionActualizacionF10()", "azul", "");
    $css->CerrarDiv();
    $css->CrearDiv("div_mensajes_f10", "col-md-4", "left", 1, 1);
        
    $css->CerrarDiv();
    print("<br><br><br>");
    $css->CrearDiv("", "row container", "left", 1, 1,'','width:100%');
    
        $css->CrearDiv("", "col-md-12", "left", 1, 1);
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                if($TipoUser=="administrador"){
                    $sql="SELECT NIT as idIPS, Nombre FROM ips";
                }else{
                    $sql="SELECT r.idIPS,i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
                }

                $Consulta=$obCon->Query($sql);

                $css->select("ips_id", "form-control", "ips_id", "", "", "onchange=listarF10();", "");
                    $css->option("", "", "", "", "", "");
                        print("Todas las IPS");
                    $css->Coption();
                    while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosIPS["idIPS"], "", "");
                            print($DatosIPS["Nombre"].$DatosIPS["idIPS"]);
                        $css->Coption();
                    }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                

                $css->select("estado", "form-control", "estado", "", "", "onchange=listarF10();", "");
                    
                    $sql="SELECT * FROM f10_estados";
                    $css->option("", "", "", "", "", "");
                        print("Todos los estados");
                    $css->Coption();
                    $Consulta=$obCon->Query($sql);
                    while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $datos_consulta["ID"], "", "");
                            print($datos_consulta["nombre_estado"]);
                        $css->Coption();
                    }
                
                    
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "box-tools pull-right", "left", 1, 1);                
                print('<div class="input-group">');               
                    $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar", "", "", "");

                print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
                          </div>');
            $css->CerrarDiv(); 
        $css->CerrarDiv();     
        $css->CrearDiv("", "col-md-12", "left", 1, 1);
             
            $css->CrearDiv("", "box box-primary", "left", 1, 1);
                $css->CrearDiv("div_listado_f10", "box-header with-border", "left", 1, 1);  
                $css->CerrarDiv();
                
            $css->CerrarDiv();    
        $css->CerrarDiv();
    $css->CerrarDiv();
    
$css->PageFin();
print('<script src="jsPages/f10.js"></script>');  //script propio de la pagina

print('<script src="../../general/js/CreacionContratos.js"></script>');  //script para la creacion de contratos
$css->Cbody();
$css->Chtml();

?>