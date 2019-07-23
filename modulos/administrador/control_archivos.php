<?php
/**
 * Pagina para crear o editar las diferentes opciones del software
 * 2019-05-20, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="control_archivos.php";
$myTitulo="Control de archivos EPS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];
if(isset($_REQUEST["btnConstruir"])){
    
    $sql="DROP VIEW IF EXISTS `vista_consolidado_control_eps`;";
    $obCon->Query($sql);
    
    $sqlVista="CREATE VIEW vista_consolidado_control_eps AS ";
    
    $sql="show databases LIKE 'ts_eps_ips_%'";
    $Consulta=$obCon->Query($sql);
    
    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
       
        $database=$DatosConsulta["Database (ts_eps_ips_%)"];
        $sqlVista.="SELECT * FROM $database.controlcargueseps ";
        $sqlVista.=" UNION ALL ";
    }
    $sqlVista= substr($sqlVista,0, -10);
    //print($sqlVista);
    $obCon->Query($sqlVista);
    
}
$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];

$css->PageInit($myTitulo);
   
    $css->CrearDiv("", "col-md-12", "left", 1, 1);
        $css->form("", "form-control", "", "post", $myPage, "_self", "", "");
            $css->input("submit", "btnConstruir", "form-control", "btnConstruir", "", "Consolidar", "", "", "", "");
        $css->Cform();
    $css->CerrarDiv();
	
$css->PageFin();
print('<script src="jsPages/control_archivos.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>