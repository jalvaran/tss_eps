<?php
/**
 * Reportes de contabilidad
 * 2019-01-08, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="ReportesContabilidad.php";
$myTitulo="Reportes Contables";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css = new PageConstruct($myTitulo, "", "", "");
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);
        
    $css->CrearDiv("", "col-md-12", "center", 1, 1);   
    $css->CrearDiv("", "col-md-4", "center", 1, 1); 
    $css->CerrarDiv();
    $css->CrearDiv("", "col-md-4", "center", 1, 1);   
        $css->fieldset("", "", "FieldReporte", "Reporte", "", "");
            $css->legend("", "");
                print("<a href='#'>Reporte</a>");
            $css->Clegend();
            
            $css->select("CmbReporteContable", "form-control", "CmbReporteContable", "", "", "onchange=DibujeOpcionesReporte()", "");
                $css->option("", "", "", "", "", "");
                    print("Seleccione");
                $css->Coption();
                $css->option("", "", "Balance x Terceros", 1, "", "");
                    print("Balance de Comprobación");
                $css->Coption();
                
                $css->option("", "", "Estado de resultados", 3, "", "");
                    print("Estado de Resultados");
                $css->Coption();
                
                $css->option("", "", "Certificados de retencion", 2, "", "");
                    print("Certificados de retención");
                $css->Coption();
                
            $css->Cselect();
        $css->Cfieldset();
        $css->CerrarDiv();
        $css->CrearDiv("", "col-md-4", "center", 1, 1); 
        $css->CerrarDiv();
    $css->CerrarDiv();
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivDibujeOpcionesReporte", "col-md-12", "center", 1, 1);   
    
    
    $css->CerrarDiv();
    print("<br><br><br><br><br><br><br><br><br><br>");
    $css->CrearDiv("DivOpcionesReportes", "col-md-9", "center", 1, 1);
    $css->CerrarDiv();
    $css->CrearDiv("DivReportesContables", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    $css->CrearDiv("DivPDFReportes", "col-md-12", "center", 0, 1);
        print("<iframe id='FramePDF' name='FramePDF' class='col-md-12' style='height:1000px;border:0px;'></iframe>");
    $css->CerrarDiv();
    
    
      

    
$css->PageFin();

print('<script src="../../general/js/notificaciones.js"></script>');
//print('<script src="../../dist/js/jspdf.min.js"></script>');
print('<script src="jsPages/ReportesContabilidad.js"></script>');
$css->AddJSExcel();
$css->Cbody();
$css->Chtml();

?>