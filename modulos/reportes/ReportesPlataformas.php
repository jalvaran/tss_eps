<?php
/**
 * Reportes de ventas y compras
 * 2019-02-11, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="ReportesPlataformas.php";
$myTitulo="Reporte de Ventas e Ingresos por plataformas";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css = new PageConstruct($myTitulo, "", "", "");
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);

    $css->CrearDiv("", "col-md-3", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Fecha Inicial</a>");
            $css->Clegend();          
        
            $css->input("date", "FechaInicial", "form-control", "FechaInicial", "Fecha Inicial", date("Y-m-d"), "Fecha Inicial", "NO", "", "style='line-height: 15px;'");
        $css->Cfieldset();
        $css->CerrarDiv();  
        
    $css->CrearDiv("", "col-md-3", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaFinal", "Fecha Final", "", "");
            $css->legend("", "");
                print("<a href='#'>Fecha Final</a>");
            $css->Clegend();          
        
            $css->input("date", "FechaFinal", "form-control", "FechaFinal", "Fecha Final", date("Y-m-d"), "Fecha Inicial", "NO", "", "style='line-height: 15px;'");
        $css->Cfieldset();
        $css->CerrarDiv();  
    
        $css->CrearDiv("", "col-md-3", "center", 1, 1);
            $css->fieldset("", "", "FieldTipo", "Nivel", "", "");
                $css->legend("", "");
                    print("<a href='#'>Plataforma</a>");
                $css->Clegend();
                $css->select("Plataforma", "form-control", "Plataforma", "", "", "", "");     
                
                $Consulta=$obCon->ConsultarTabla("comercial_plataformas_pago", "");
                while($DatosPlataformas=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "Plataforma", $DatosPlataformas["ID"], "", "");
                        print($DatosPlataformas["Nombre"]);
                    $css->Coption(); 
                    
                }
                
                $css->Cselect();
            $css->Cfieldset();
        $css->CerrarDiv();
        
    $css->CrearDiv("", "col-md-3", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Acciones</a>");
            $css->Clegend();          
        
           $css->CrearBotonEvento("BtnCrearReporte", "Crear", 1, "onclick", "CrearReportePlataformas()", "azul", "");
        $css->Cfieldset();
        $css->CerrarDiv();  
        
    $css->CrearDiv("DivProceso", "", "center", 1, 1);
    
    $css->CerrarDiv(); 
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivReportesIngresos", "col-md-6", "center", 1, 1);
    
    $css->CerrarDiv(); 
    
    $css->CrearDiv("DivReportesVentas", "col-md-6", "center", 1, 1);
    
    $css->CerrarDiv(); 
    
    
    
    
    //$css->CerrarDiv(); 
$css->PageFin();

//print('<script src="../../general/js/notificaciones.js"></script>');
print('<script src="jsPages/ReportesPlataformas.js"></script>');
$css->AddJSExcel();

$css->Cbody();
$css->Chtml();

?>