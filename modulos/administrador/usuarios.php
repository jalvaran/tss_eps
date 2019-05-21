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
$myPage="usuarios.php";
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
    print("<br>");
    $css->CrearDiv("", "col-md-12", "left", 1, 1);
    
    $css->fieldset("", "", "FieldDatosCotizacion", "DatosCotizacion", "", "");
            $css->legend("", "");
                print("<a href='#'>Administrar</a>");
            $css->Clegend();   
            
    $css->Cfieldset();
    
    $css->CrearDiv("DivDatosTabla", "col-md-7", "left", 1, 1);
    
    $css->CrearDiv("DivOpcionesTabla", "", "left", 1, 1);
    
    $css->CerrarDiv();
    $css->CrearDiv("DivRegistrosTabla", "", "left", 1, 1);
    
    $css->CerrarDiv();
    
    
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivOpcionesUsuarios", "col-md-5", "left", 1, 1);
    $css->CerrarDiv();
    
    print("<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>");
	
$css->PageFin();
print('<script src="jsPages/usuarios.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>