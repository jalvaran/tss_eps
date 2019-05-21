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
$myPage="ReportesComparativos.php";
$myTitulo="Reportes Compras VS Ventas";
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
                    print("<a href='#'>Nivel</a>");
                $css->Clegend();
                $css->select("CmbNivel", "form-control", "CmbNivel", "", "", "", "");                
                    $css->option("", "", "Nivel", "D", "", "");
                        print("Departamento");
                    $css->Coption();
                    $css->option("", "", "Nivel", 1, "", "");
                        print("Subnivel 1");
                    $css->Coption();  
                    $css->option("", "", "Nivel", 2, "", "");
                        print("Subnivel 2");
                    $css->Coption();
                $css->Cselect();
            $css->Cfieldset();
        $css->CerrarDiv();
        
    $css->CrearDiv("", "col-md-3", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Acciones</a>");
            $css->Clegend();          
        
           $css->CrearBotonEvento("BtnCrearReporte", "Crear", 1, "onclick", "CrearReporteComprasXVentas()", "azul", "");
        $css->Cfieldset();
        $css->CerrarDiv();  
        
    $css->CrearDiv("DivProceso", "", "center", 1, 1);
    
    $css->CerrarDiv(); 
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivReportes", "", "center", 1, 1);
    
    $css->CerrarDiv(); 
    
    
    
    
    //$css->CerrarDiv(); 
$css->PageFin();

//print('<script src="../../general/js/notificaciones.js"></script>');
print('<script src="jsPages/ReportesComparativos.js"></script>');
$css->AddJSExcel();

$css->Cbody();
$css->Chtml();

?>