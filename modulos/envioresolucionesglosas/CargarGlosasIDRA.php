<?php
/**
 * Pagina para Subir las glosas del IDRA
 * 2019-06-27, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="CargarGlosasIDRA.php";
$myTitulo="Cargar el Archivo de Glosas IDRA";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$_SESSION["Role"];

$css->PageInit($myTitulo);
    
    $css->CrearDiv("", "col-md-12", "left", 1, 1); 
        $css->h3("", "", "", "");
                print("<strong>Cargar el Archivo de Glosas IDRA</strong>");
        $css->Ch3();
    $css->CerrarDiv(); 
    
    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        print("<strong>Seleccione el archivo:</strong><br>");
        $css->input("file", "UpCartera", "form-control", "UpCartera", "", "", "", "", "", "style='line-height: 15px;'");
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        
        print("<strong>Enviar:</strong><br>");
        $css->CrearBotonEvento("BtnSubir", "Ejecutar", 1, "onclick", "ConfirmarCarga()", "verde", "");
    $css->CerrarDiv();
    print("<br><br><br><br><br><br><br>");
    $css->CrearDiv("DivProgress", "col-md-12", "center", 1, 1);
        $css->ProgressBar("PgProgresoUp", "LyProgresoUP", "", 0, 0, 100, 0, "0%", "", "");
    $css->CerrarDiv();
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivProcess", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    
$css->PageFin();
print('<script src="jsPages/CargarGlosasIDRA.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>