<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ReportesComparativos.class.php");
//include_once("../clases/PDF_ReportesContables.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Reportes($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //obtiene la clasificacion del inventario, datos iniciales
            $Plataforma=$obCon->normalizar($_REQUEST["Plataforma"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            
            $css->CrearDiv("DivTabla", "col-md-12", "center", 1, 1);
            $css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporteIngresos')", "verde", "");
                $css->CrearTabla("TblReporteIngresos");
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Fecha</strong>", 1,"C");
                        $css->ColTabla("<strong>Hora</strong>", 1,"C");
                        $css->ColTabla("<strong>Tercero</strong>", 1,"C");
                        $css->ColTabla("<strong>Valor</strong>", 1,"C");
                                                
                    $css->CierraFilaTabla();
                    $sql="SELECT Fecha,Hora,Tercero,round(Valor) as Valor FROM comercial_plataformas_pago_ingresos "
                            . "WHERE Fecha>='$FechaInicial' AND Fecha<='$FechaFinal' AND idPlataformaPago='$Plataforma'";
                    $Consulta=$obCon->Query($sql);
                    
                    while ($DatosIngresos = $obCon->FetchAssoc($Consulta)) {
                        $css->FilaTabla(14);
                            $css->ColTabla($DatosIngresos["Fecha"], 1);
                            $css->ColTabla($DatosIngresos["Hora"], 1);
                            $css->ColTabla($DatosIngresos["Tercero"], 1);
                            $css->ColTabla($DatosIngresos["Valor"], 1);
                            
                        $css->CierraFilaTabla();
                    }    
                    
                $css->CerrarTabla();
            $css->CerrarDiv();  
            unset($DatosIngresos);
                       
            
        break; //Fin caso 1
        
        case 2: //obtiene el objeto json con la info de las compras
            $Plataforma=$obCon->normalizar($_REQUEST["Plataforma"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            
            $css->CrearDiv("DivTabla", "col-md-12", "center", 1, 1);
            $css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporteVentas')", "verde", "");
                $css->CrearTabla("TblReporteVentas");
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Fecha</strong>", 1,"C");
                        $css->ColTabla("<strong>Hora</strong>", 1,"C");
                        $css->ColTabla("<strong>Tercero</strong>", 1,"C");
                        $css->ColTabla("<strong>Valor</strong>", 1,"C");
                                                
                    $css->CierraFilaTabla();
                    $sql="SELECT Fecha,Hora,Tercero,round(Valor) as Valor FROM comercial_plataformas_pago_ventas "
                            . "WHERE Fecha>='$FechaInicial' AND Fecha<='$FechaFinal' AND idPlataformaPago='$Plataforma'";
                    $Consulta=$obCon->Query($sql);
                    
                    while ($DatosVentas = $obCon->FetchAssoc($Consulta)) {
                        $css->FilaTabla(14);
                            $css->ColTabla($DatosVentas["Fecha"], 1);
                            $css->ColTabla($DatosVentas["Hora"], 1);
                            $css->ColTabla($DatosVentas["Tercero"], 1);
                            $css->ColTabla($DatosVentas["Valor"], 1);
                            
                        $css->CierraFilaTabla();
                    }    
                    
                $css->CerrarTabla();
            $css->CerrarDiv();  
            unset($DatosIngresos);
        break; //Fin caso 2
        
        
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>