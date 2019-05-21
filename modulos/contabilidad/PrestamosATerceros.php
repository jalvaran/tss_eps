<?php
/**
 * Prestamos a terceros
 * 2019-04-06, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="PrestamosATerceros.php";
$myTitulo="Pretamos a Terceros";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css = new PageConstruct($myTitulo, "", "", "");
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);
        
    $css->Modal("ModalAcciones", "Prestamos", "",1);
        $css->CrearDiv("DivFormulariosModal", "", "left", 1, 1);
        $css->Cdiv();
    $css->CModal("BtnAccionesModal", "onclick=GuardarAccion()", "submit", "Guardar");
    $css->CrearDiv("", "col-md-12", "center", 1, 1);   
        $css->fieldset("", "", "FieldReporte", "Reporte", "", "");
            $css->legend("", "");
                print("<a href='#'>Prestamos a Terceros</a>");
            $css->Clegend();
            
            $css->CrearDiv("", "col-md-2", "left", 1, 1);
                $css->CrearBotonEvento("BtnNuevo", "Nuevo", 1, "onClick", "FormularioPrestar()", "azul", "");           
                
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-8", "left", 1, 1);
            
            $css->CerrarDiv();
            
        $css->Cfieldset();
        
    $css->CrearDiv("DivOpcionesListadoCuentaXCobrar", "col-md-12", "center", 1, 1); 
    $css->CerrarDiv();    
    $css->CrearDiv("DivListadoCuentaXCobrar", "col-md-12", "center", 1, 1); 
    $css->CerrarDiv();
    
    $css->CerrarDiv();
    
$css->PageFin();

print('<script src="jsPages/PrestamosATerceros.js"></script>');
$css->AddJSExcel();
$css->Cbody();
$css->Chtml();

?>