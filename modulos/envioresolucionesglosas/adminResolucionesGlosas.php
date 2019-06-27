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
$myPage="adminResolucionesGlosas.php";
$myTitulo="Resoluciones de Glosas";
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
print("<br>");
   $css->section("", "content", "", "");
   
   $css->TabInit();
            $css->TabLabel("TabCuentas1", "<strong >Comparacion de Radicados</strong>", "Tab_1", 1,"onclick=CargarComparacionRadicados()");
            
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            
            $css->CrearDiv("DivOpcionesTab1", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivTab1", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
    $css->Csection();
$css->PageFin();

print('<script src="jsPages/adminResolucionesGlosas.js"></script>');  //script propio de la pagina


$css->Cbody();
$css->Chtml();

?>