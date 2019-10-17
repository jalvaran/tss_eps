<?php
/**
 * Página donde se visualizan las actas de conciliación
 * 2019-09-10, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="ActasConciliacion.php";
$myTitulo="Actas de Conciliación";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$DatosUsuario["Role"];
$css->PageInit($myTitulo);
    $css->Modal("ModalAcciones", "TS5", "", 1);
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();        
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    
//print("<br>");
   $css->section("", "content", "", "");
   
   $css->CrearDiv("", "col-md-4", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT NIT as idIPS, Nombre FROM ips";
        }else{
            $sql="SELECT r.idIPS,i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "onchange=CargarHistorialActas()", "");
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["idIPS"], "", "");
                    print($DatosIPS["Nombre"].$DatosIPS["idIPS"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        $sql="SELECT * FROM eps";
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbEPS", "form-control", "CmbEPS", "EPS", "", "", "");
            while($DatosEPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosEPS["NIT"], "", "");
                    print($DatosEPS["Nombre"]." ".$DatosEPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
   
   $css->CrearDiv("", "col-md-4", "center", 1, 1);
   $css->CerrarDiv();
   $css->CrearDiv("", "col-md-4", "center", 1, 1);
   $css->CerrarDiv();
   
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        print('<br><div class="input-group">');
        $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Búsqueda", "", "", "onchange=CargarHistorialActas(1)");


         print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
              </div>');
    $css->CerrarDiv();
    print("<br><br><br><br>");
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
   $css->TabInit();
            $css->TabLabel("TabCuentas1", "<strong >Historial de Actas de Conciliación</strong>", "Tab_1", 1,"onclick=CargarHistorialActas()");
            //$css->TabLabel("TabCuentas2", "<strong >Detalles</strong>", "Tab_2",0,"onclick=DetallesActas()");
            
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            
            
            $css->CrearDiv("DivTab1", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        /*
        $css->TabPaneInit("Tab_2");
            
            $css->CrearDiv("DivTab2", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
         * 
         */
    $css->Csection();
$css->PageFin();

print('<script src="jsPages/ActasConciliacion.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>