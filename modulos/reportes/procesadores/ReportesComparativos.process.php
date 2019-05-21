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
            $Nivel=$obCon->normalizar($_REQUEST["Nivel"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            if($Nivel=="D"){
                $Clasificacion=$obCon->ObtengaClasificacionXDepartamento();
                $Compras=$obCon->ObtengaComprasXDepartamento($FechaInicial,$FechaFinal);
                $Ventas=$obCon->ObtengaVentasXDepartamento($FechaInicial,$FechaFinal);
            }
            if($Nivel==1){
                $Clasificacion=$obCon->ObtengaClasificacionXSub1();
                $Compras=$obCon->ObtengaComprasXSub1($FechaInicial,$FechaFinal);
                $Ventas=$obCon->ObtengaVentasXSub1($FechaInicial,$FechaFinal);
                
            }
            if($Nivel==2){
                $Clasificacion=$obCon->ObtengaClasificacionXSub2();
                $Compras=$obCon->ObtengaComprasXSub2($FechaInicial,$FechaFinal);
                $Ventas=$obCon->ObtengaVentasXSub2($FechaInicial,$FechaFinal);
            }
            $InformacionCompleta= $obCon->ObtengaDatosCompletos($Clasificacion,$Compras,$Ventas);
            
            $css->CrearDiv("DivTabla", "col-md-12", "center", 1, 1);
            $css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporte')", "verde", "");
                $css->CrearTabla("TblReporte");
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Departamento</strong>", 1,"C");
                        $css->ColTabla("<strong>Sub1</strong>", 1,"C");
                        $css->ColTabla("<strong>Sub2</strong>", 1,"C");
                        $css->ColTabla("<strong>Exitencias Actuales</strong>", 1,"C");
                        $css->ColTabla("<strong>Costo Total</strong>", 1,"C");
                        $css->ColTabla("<strong>Precio Venta Total</strong>", 1,"C");
                        $css->ColTabla("<strong>Cantidad Comprada</strong>", 1,"C");
                        $css->ColTabla("<strong>Cantidad Vendida</strong>", 1,"C");
                        $css->ColTabla("<strong>Diferencia Cantidades</strong>", 1,"C");
                        
                        $css->ColTabla("<strong>Subtotal Compras</strong>", 1,"C");
                        $css->ColTabla("<strong>IVA Compras</strong>", 1,"C");
                        $css->ColTabla("<strong>Total Compras</strong>", 1,"C");
                        
                        $css->ColTabla("<strong>Subtotal Costo en Ventas</strong>", 1,"C");
                        $css->ColTabla("<strong>Diferencia Moneda</strong>", 1,"C");
                        $css->ColTabla("<strong>Subtotal Ventas</strong>", 1,"C");
                        $css->ColTabla("<strong>IVA Ventas</strong>", 1,"C");
                        $css->ColTabla("<strong>Total Ventas</strong>", 1,"C");
                        
                    $css->CierraFilaTabla();
                
                    foreach ($InformacionCompleta as $key => $value) {
                        $css->FilaTabla(14);
                            $css->ColTabla($InformacionCompleta[$key]["NombreDepartamento"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["NombreSub1"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["NombreSub2"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["Existencias"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["CostoTotal"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["PrecioVentaTotal"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["CantidadComprada"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["CantidadVendida"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["CantidadComprada"]-$InformacionCompleta[$key]["CantidadVendida"], 1);
                            
                            $css->ColTabla($InformacionCompleta[$key]["SubtotalCompras"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["IVACompras"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["TotalCompras"], 1);
                            
                            $css->ColTabla($InformacionCompleta[$key]["TotalCostoVentas"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["SubtotalCompras"]-$InformacionCompleta[$key]["TotalCostoVentas"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["SubtotalVentas"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["IVAVentas"], 1);
                            $css->ColTabla($InformacionCompleta[$key]["TotalVentas"], 1);
                        $css->CierraFilaTabla();
                    }    
                    
                $css->CerrarTabla();
            $css->CerrarDiv();  
            unset($Compras);
            unset($Ventas);
            unset($InformacionCompleta);
            
            
        break; //Fin caso 1
        
        case 2: //obtiene el objeto json con la info de las compras
            $Nivel=$obCon->normalizar($_REQUEST["Nivel"]);
            
            if($Nivel=="D"){
                $Compras=$obCon->ObtengaComprasXDepartamento();
            }
            if($Nivel==1){
                $Compras=$obCon->ObtengaComprasXSub1();
            }
            if($Nivel==2){
                $Compras=$obCon->ObtengaComprasXSub2();
            }
            $ComprasJSON= json_encode($Compras);
            print("OK;$ComprasJSON");
            unset($ComprasJSON);
            unset($Compras);
        break; //Fin caso 2
        
        case 3: //obtiene el objeto json con la info de las compras
            $Nivel=$obCon->normalizar($_REQUEST["Nivel"]);
            if($Nivel=="D"){
                $Ventas=$obCon->ObtengaVentasXDepartamento();
            }
            if($Nivel==1){
                $Ventas=$obCon->ObtengaVentasXSub1();
            }
            if($Nivel==2){
                $Ventas=$obCon->ObtengaVentasXSub2();
            }
            $VentasJSON= json_encode($Ventas);
            print("OK;$VentasJSON");
            unset($VentasJSON);
            unset($Ventas);
        break; //Fin caso 3
        
        case 4: //obtiene el objeto json con la info completa
            $Nivel=$obCon->normalizar($_REQUEST["Nivel"]);
            $Clasificacion=($_REQUEST["Clasificacion"]);
            $Compras=($_REQUEST["Compras"]);
            $Ventas=($_REQUEST["Ventas"]);
            
            //print_r(json_decode($Clasificacion));
            $Json=$obCon->ObtengaJsonCompleto($Clasificacion,$Compras,$Ventas);
            $JsonCompleto= json_encode($Json);
            print("OK;$JsonCompleto");
            unset($Json);
            unset($JsonCompleto);
        break; //Fin caso 3
        
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>