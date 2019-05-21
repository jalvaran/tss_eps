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
$myPage="ReportesTitulos.php";
$myTitulo="Reporte para venta de titulos";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css = new PageConstruct($myTitulo, "", "", "");
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);

    $css->CrearDiv("", "col-md-3", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Tipo de Reporte</a>");
            $css->Clegend();          
        
            $css->select("CmbTipoReporte", "form-control", "CmbTipoReporte", "", "", "", "");     
                
                
                $css->option("", "", "", 1, "", "");
                    print("Titulos Vendidos con abonos");
                $css->Coption(); 
                
                $css->option("", "", "", 2, "", "");
                    print("Titulos Vendidos sin abonos");
                $css->Coption(); 
                
                $css->option("", "", "", 3, "", "");
                    print("Comisiones por pagar");
                $css->Coption(); 
                
                $css->option("", "", "", 4, "", "");
                    print("Titulos en poder los vendedores");
                $css->Coption(); 
                
                $css->Cselect();
            
        $css->Cfieldset();
    $css->CerrarDiv(); 
        
    $css->CrearDiv("", "col-md-2", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Fecha Inicial</a>");
            $css->Clegend();          
        
            $css->input("date", "FechaInicial", "form-control", "FechaInicial", "Fecha Inicial", date("Y-m-d"), "Fecha Inicial", "NO", "", "style='line-height: 15px;'");
        $css->Cfieldset();
        $css->CerrarDiv();  
        
    $css->CrearDiv("", "col-md-2", "center", 1, 1);       
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
                    print("<a href='#'>Promoción</a>");
                $css->Clegend();
                $css->select("Promocion", "form-control", "Promocion", "", "", "", "");     
                
                $Consulta=$obCon->ConsultarTabla("titulos_promociones", "");
                while($DatosPlataformas=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "Plataforma", $DatosPlataformas["ID"], "", "");
                        print($DatosPlataformas["Nombre"]);
                    $css->Coption(); 
                    
                }
                
                $css->Cselect();
            $css->Cfieldset();
        $css->CerrarDiv();
        
    $css->CrearDiv("", "col-md-2", "center", 1, 1);       
        $css->fieldset("", "", "FieldFechaInicial", "Fecha Inicial", "", "");
            $css->legend("", "");
                print("<a href='#'>Acciones</a>");
            $css->Clegend();          
        
           $css->CrearBotonEvento("BtnCrearReporte", "Crear", 1, "onclick", "CrearReporte()", "azul", "");
        $css->Cfieldset();
        $css->CerrarDiv();  
        
    $css->CrearDiv("DivProceso", "", "center", 1, 1);
    
    $css->CerrarDiv(); 
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivReportes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv(); 
    
        
    //$css->CerrarDiv(); 
$css->PageFin();

//print('<script src="../../general/js/notificaciones.js"></script>');
print('<script src="jsPages/ReportesTitulos.js"></script>');
$css->AddJSExcel();

$css->Cbody();
$css->Chtml();

?>