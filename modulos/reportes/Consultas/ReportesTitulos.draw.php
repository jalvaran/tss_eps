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
        case 1: // Titulos vendidos con abonos
            $idPromocion=$obCon->normalizar($_REQUEST["Promocion"]);
            $TipoReporte=$obCon->normalizar($_REQUEST["CmbTipoReporte"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            
            $css->CrearDiv("DivTabla", "col-md-12", "center", 1, 1);
            $css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporte')", "verde", "");
                $css->CrearTabla("TblReporte");
                
                if($TipoReporte<4){
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>ID</strong>", 1,"C");
                        $css->ColTabla("<strong>Fecha</strong>", 1,"C");
                        $css->ColTabla("<strong>Promocion</strong>", 1,"C");
                        $css->ColTabla("<strong>Mayor1</strong>", 1,"C");
                        $css->ColTabla("<strong>Mayor2</strong>", 1,"C");
                        $css->ColTabla("<strong>Adicional</strong>", 1,"C");
                        $css->ColTabla("<strong>Valor</strong>", 1,"C");
                        $css->ColTabla("<strong>TotalAbonos</strong>", 1,"C");
                        $css->ColTabla("<strong>Saldo</strong>", 1,"C");
                        $css->ColTabla("<strong>idCliente</strong>", 1,"C");
                        $css->ColTabla("<strong>NombreCliente</strong>", 1,"C");
                        $css->ColTabla("<strong>idColaborador</strong>", 1,"C");
                        $css->ColTabla("<strong>NombreColaborador</strong>", 1,"C");
                        $css->ColTabla("<strong>ComisionAPagar</strong>", 1,"C");
                        $css->ColTabla("<strong>SaldoComision</strong>", 1,"C");
                        $css->ColTabla("<strong>idUsuario</strong>", 1,"C");
                        $css->ColTabla("<strong>Estado</strong>", 1,"C");
                                              
                                                
                    $css->CierraFilaTabla();
                    if($TipoReporte==1){ //Vendidos con abonos
                        $sql="SELECT * FROM `titulos_ventas` WHERE `Promocion`='$idPromocion' AND `TotalAbonos`>0 AND `Estado`='' AND `Fecha`>='$FechaInicial' AND `Fecha`<='$FechaFinal';";
                    }
                    
                    if($TipoReporte==2){ //Vendidos sin abonos
                        $sql="SELECT * FROM `titulos_ventas` WHERE `Promocion`='$idPromocion' AND `TotalAbonos`=0 AND `Estado`='' AND `Fecha`>='$FechaInicial' AND `Fecha`<='$FechaFinal';";
                    }
                    
                    if($TipoReporte==3){ //Comisiones x pagar
                        $sql="SELECT * FROM `titulos_ventas` WHERE `Promocion`='$idPromocion' AND  `Estado`='' AND `Fecha`>='$FechaInicial' AND `Fecha`<='$FechaFinal';";
                    }
                    
                    $Consulta=$obCon->Query($sql);
                    
                    while ($DatosIngresos = $obCon->FetchAssoc($Consulta)) {
                        $css->FilaTabla(14);
                            $css->ColTabla($DatosIngresos["ID"], 1);
                            $css->ColTabla($DatosIngresos["Fecha"], 1);
                            $css->ColTabla($DatosIngresos["Promocion"], 1);
                            $css->ColTabla($DatosIngresos["Mayor1"], 1);
                            $css->ColTabla($DatosIngresos["Mayor2"], 1);
                            $css->ColTabla($DatosIngresos["Adicional"], 1);
                            $css->ColTabla($DatosIngresos["Valor"], 1);
                            $css->ColTabla($DatosIngresos["TotalAbonos"], 1);
                            $css->ColTabla($DatosIngresos["Saldo"], 1);
                            $css->ColTabla($DatosIngresos["idCliente"], 1);
                            $css->ColTabla($DatosIngresos["NombreCliente"], 1);
                            $css->ColTabla($DatosIngresos["idColaborador"], 1);
                            $css->ColTabla($DatosIngresos["NombreColaborador"], 1);
                            $css->ColTabla($DatosIngresos["ComisionAPagar"], 1);
                            $css->ColTabla($DatosIngresos["SaldoComision"], 1);
                            $css->ColTabla($DatosIngresos["idUsuario"], 1);
                            $css->ColTabla($DatosIngresos["Estado"], 1);
                            
                        $css->CierraFilaTabla();
                    }    
                }
                
                if($TipoReporte==4){
                    
                    $css->FilaTabla(14);
                        
                        $css->ColTabla("<strong>Mayor1</strong>", 1,"C");
                        $css->ColTabla("<strong>Mayor2</strong>", 1,"C");
                        $css->ColTabla("<strong>Adicional</strong>", 1,"C");
                        $css->ColTabla("<strong>idColaborador</strong>", 1,"C");
                        $css->ColTabla("<strong>Nombre Colaborador</strong>", 1,"C");
                        $css->ColTabla("<strong>Fecha de Entrega al Colaborador</strong>", 1,"C");
                        $css->ColTabla("<strong>idActa</strong>", 1,"C");
                        $css->ColTabla("<strong>TotalPagoComisiones</strong>", 1,"C");
                        $css->ColTabla("<strong>idCliente</strong>", 1,"C");
                        $css->ColTabla("<strong>NombreCliente</strong>", 1,"C");
                        $css->ColTabla("<strong>FechaVenta</strong>", 1,"C");
                        $css->ColTabla("<strong>TotalAbonos</strong>", 1,"C");
                        $css->ColTabla("<strong>Saldo</strong>", 1,"C");
                                                                      
                                                
                    $css->CierraFilaTabla();
                    $Tabla="titulos_listados_promocion_".$idPromocion;
                    $sql="SELECT * FROM `$Tabla` WHERE `idColaborador` > 0 AND `idCliente` = 0;";
                    
                    $Consulta=$obCon->Query($sql);
                    
                    while ($DatosIngresos = $obCon->FetchAssoc($Consulta)) {
                        $css->FilaTabla(14);
                            
                            $css->ColTabla($DatosIngresos["Mayor1"], 1);
                            $css->ColTabla($DatosIngresos["Mayor2"], 1);
                            $css->ColTabla($DatosIngresos["Adicional"], 1);
                            $css->ColTabla($DatosIngresos["idColaborador"], 1);
                            $css->ColTabla($DatosIngresos["NombreColaborador"], 1);
                            $css->ColTabla($DatosIngresos["FechaEntregaColaborador"], 1);
                            $css->ColTabla($DatosIngresos["idActa"], 1);
                            $css->ColTabla($DatosIngresos["TotalPagoComisiones"], 1);
                            $css->ColTabla($DatosIngresos["idCliente"], 1);
                            $css->ColTabla($DatosIngresos["NombreCliente"], 1);
                            $css->ColTabla($DatosIngresos["FechaVenta"], 1);
                            $css->ColTabla($DatosIngresos["TotalAbonos"], 1);
                            $css->ColTabla($DatosIngresos["Saldo"], 1);
                                                        
                        $css->CierraFilaTabla();
                    }  
                    
                }
                $css->CerrarTabla();
            $css->CerrarDiv();  
            unset($DatosIngresos);
                       
            
        break; //Fin caso 1
         
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>