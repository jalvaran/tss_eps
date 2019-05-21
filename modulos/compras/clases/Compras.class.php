<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
include_once 'Recetas.class.php';
class Compras extends ProcesoVenta{
    /**
     * Crea una compra
     * @param type $Fecha
     * @param type $idTercero
     * @param type $Observaciones
     * @param type $CentroCostos
     * @param type $idSucursal
     * @param type $idUser
     * @param type $TipoCompra
     * @param type $NumeroFactura
     * @param type $Concepto
     * @param type $Vector
     * @return type
     */
    public function CrearCompra($Fecha, $idTercero, $Observaciones,$CentroCostos, $idSucursal, $idUser,$TipoCompra,$NumeroFactura,$Concepto,$Vector ) {
        
        //////Creo la compra            
        $tab="factura_compra";
        $NumRegistros=11;

        $Columnas[0]="Fecha";		$Valores[0]=$Fecha;
        $Columnas[1]="Tercero";         $Valores[1]=$idTercero;
        $Columnas[2]="Observaciones";   $Valores[2]=$Observaciones;
        $Columnas[3]="Estado";		$Valores[3]="ABIERTA";
        $Columnas[4]="idUsuario";	$Valores[4]=$idUser;
        $Columnas[5]="idCentroCostos";	$Valores[5]=$CentroCostos;
        $Columnas[6]="idSucursal";	$Valores[6]=$idSucursal;
        $Columnas[7]="TipoCompra";	$Valores[7]=$TipoCompra;
        $Columnas[8]="NumeroFactura";	$Valores[8]=$NumeroFactura;
        $Columnas[9]="Soporte";         $Valores[9]="";
        $Columnas[10]="Concepto";        $Valores[10]=$Concepto;
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);

        $idCompra=$this->ObtenerMAX($tab,"ID", 1,"");
        
        //Miro si se recibe un archivo
        //
        $destino="";
        $Atras="";
        $carpeta="";
        if(!empty($_FILES['Soporte']['name'])){
            //echo "<script>alert ('entra foto')</script>";
            $Atras="../";
            $carpeta="soportes/";
            opendir($Atras.$carpeta);
            $Name=$idCompra."_".str_replace(' ','_',$_FILES['Soporte']['name']);
            $destino=$carpeta.$Name;
            move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$destino);
	}
        $this->ActualizaRegistro("factura_compra", "Soporte", $destino, "ID", $idCompra);
        return $idCompra;
    }
    
    //Clase para agregar un item a una compra
    public function AgregueProductoCompra($idCompra,$idProducto,$Cantidad,$CostoUnitario,$PrecioVenta,$TipoIVA,$IVAIncluido,$Vector,$IVACalculado=0) {
        if($IVACalculado==0){
            $DatosTipoImpuesto= $this->DevuelveValores("porcentajes_iva", "ID", $TipoIVA);
            $TipoIVA=$DatosTipoImpuesto["Valor"];
        }
        
        
        if($IVAIncluido==1){
            if(is_numeric($TipoIVA)){
                $CostoUnitario=round($CostoUnitario/(1+$TipoIVA),2);
            }            
        }
        $Subtotal= round($CostoUnitario*$Cantidad,2);
        if(is_numeric($TipoIVA)){
            $Impuestos=round($Subtotal*$TipoIVA,2);
        }else{
            $Impuestos=0;
        }
        $Total=round($Subtotal+$Impuestos,2);
        //////Agrego el registro           
        $tab="factura_compra_items";
        $NumRegistros=9;

        $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
        $Columnas[1]="idProducto";          $Valores[1]=$idProducto;
        $Columnas[2]="Cantidad";            $Valores[2]=$Cantidad;
        $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$CostoUnitario;
        $Columnas[4]="SubtotalCompra";      $Valores[4]=$Subtotal;
        $Columnas[5]="ImpuestoCompra";      $Valores[5]=$Impuestos;
        $Columnas[6]="TotalCompra";         $Valores[6]=$Total;
        $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$TipoIVA;
        $Columnas[8]="PrecioVenta";         $Valores[8]=$PrecioVenta;
                    
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Clase para agregar un insumo a una compra
    public function AgregueInsumoCompra($idCompra,$idProducto,$Cantidad,$CostoUnitario,$TipoIVA,$IVAIncluido,$Vector) {
        $DatosTipoImpuesto= $this->DevuelveValores("porcentajes_iva", "ID", $TipoIVA);
        $TipoIVA=$DatosTipoImpuesto["Valor"];
        if($IVAIncluido==1){
            if(is_numeric($TipoIVA)){
                $CostoUnitario=round($CostoUnitario/(1+$TipoIVA),2);
            }            
        }
        $Subtotal= round($CostoUnitario*$Cantidad,2);
        if(is_numeric($TipoIVA)){
            $Impuestos=round($Subtotal*$TipoIVA,2);
        }else{
            $Impuestos=0;
        }
        $Total=round($Subtotal+$Impuestos,2);
        //////Agrego el registro           
        $tab="factura_compra_insumos";
        $NumRegistros=8;

        $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
        $Columnas[1]="idProducto";          $Valores[1]=$idProducto;
        $Columnas[2]="Cantidad";            $Valores[2]=$Cantidad;
        $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$CostoUnitario;
        $Columnas[4]="SubtotalCompra";      $Valores[4]=$Subtotal;
        $Columnas[5]="ImpuestoCompra";      $Valores[5]=$Impuestos;
        $Columnas[6]="TotalCompra";         $Valores[6]=$Total;
        $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$TipoIVA;
                    
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Agregar Un Servicio
    public function AgregueServicioCompra($idCompra,$CuentaPUC,$Concepto,$CostoUnitario,$TipoIVA,$IVAIncluido,$Vector) {
        $DatosTipoImpuesto= $this->DevuelveValores("porcentajes_iva", "ID", $TipoIVA);
        $TipoIVA=$DatosTipoImpuesto["Valor"];
        $DatosCuenta= $this->DevuelveValores("subcuentas", "PUC", $CuentaPUC);
        if($IVAIncluido==1){
            if(is_numeric($TipoIVA)){
                $CostoUnitario=round($CostoUnitario/(1+$TipoIVA),2);
            }            
        }
        $Subtotal= round($CostoUnitario,2);
        if(is_numeric($TipoIVA)){
            $Impuestos=round($Subtotal*$TipoIVA,2);
        }else{
            $Impuestos=0;
        }
        $Total=round($Subtotal+$Impuestos,2);
        
        //////Agrego el registro           
        $tab="factura_compra_servicios";
        $NumRegistros=8;

        $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
        $Columnas[1]="CuentaPUC_Servicio";  $Valores[1]=$CuentaPUC;
        $Columnas[2]="Nombre_Cuenta";       $Valores[2]=$DatosCuenta["Nombre"];
        $Columnas[3]="Concepto_Servicio";   $Valores[3]=$Concepto;
        $Columnas[4]="Subtotal_Servicio";   $Valores[4]=$Subtotal;
        $Columnas[5]="Impuesto_Servicio";   $Valores[5]=$Impuestos;
        $Columnas[6]="Total_Servicio";      $Valores[6]=$Total;
        $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$TipoIVA;
                    
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Clase para devolver un item a una compra
    public function DevolverProductoCompra($Cantidad,$idFacturaItems,$Vector) {
        //Proceso la informacion
        $DatosFacturaItems= $this->DevuelveValores("factura_compra_items", "ID", $idFacturaItems);
        $idCompra=$DatosFacturaItems["idFacturaCompra"];
        $idProducto=$DatosFacturaItems["idProducto"];
        $CostoUnitario=$DatosFacturaItems["CostoUnitarioCompra"];
        $TipoIVA=$DatosFacturaItems["Tipo_Impuesto"];
        $Subtotal=round($CostoUnitario*$Cantidad);
        $Impuestos= round($Subtotal*$TipoIVA);
        $Total=$Subtotal+$Impuestos;
        //////Agrego el registro           
        $tab="factura_compra_items_devoluciones";
        $NumRegistros=8;

        $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
        $Columnas[1]="idProducto";          $Valores[1]=$idProducto;
        $Columnas[2]="Cantidad";            $Valores[2]=$Cantidad;
        $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$CostoUnitario;
        $Columnas[4]="SubtotalCompra";      $Valores[4]=$Subtotal;
        $Columnas[5]="ImpuestoCompra";      $Valores[5]=$Impuestos;
        $Columnas[6]="TotalCompra";         $Valores[6]=$Total;
        $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$TipoIVA;
                    
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    
    //Calcule totales de la compra
    
    public function CalculeTotalesCompra($idCompra) {
        $sql="SELECT SUM(SubtotalDescuento) as SubtotalDescuento,SUM(SubtotalCompra) as Subtotal, sum(ImpuestoCompra) as IVA, SUM(TotalCompra) AS Total FROM factura_compra_items "
                    . " WHERE idFacturaCompra='$idCompra'";
        $consulta= $this->Query($sql);
        $TotalesCompraProductos=$this->FetchArray($consulta);
        
        $sql="SELECT SUM(SubtotalDescuento) as SubtotalDescuento,SUM(SubtotalCompra) as Subtotal, sum(ImpuestoCompra) as IVA, SUM(TotalCompra) AS Total FROM factura_compra_insumos "
                    . " WHERE idFacturaCompra='$idCompra'";
        $consulta= $this->Query($sql);
        $TotalesCompraInsumos=$this->FetchArray($consulta);
        
        $sql="SELECT SUM(SubtotalCompra) as Subtotal, sum(ImpuestoCompra) as IVA, SUM(TotalCompra) AS Total FROM factura_compra_items_devoluciones "
                    . " WHERE idFacturaCompra='$idCompra'";
        $consulta= $this->Query($sql);
        $TotalesItemsDevueltos=$this->FetchArray($consulta);
        
        $TotalRetenciones= $this->SumeColumna("factura_compra_retenciones", "ValorRetencion", "idCompra", $idCompra);
        $TotalDescuentosGlobales= $this->SumeColumna("factura_compra_descuentos", "ValorDescuento", "idCompra", $idCompra);
        $TotalImpuestosAdicionales= $this->SumeColumna("factura_compra_impuestos_adicionales", "Valor", "idCompra", $idCompra);
        $sql="SELECT SUM(Subtotal_Servicio) as Subtotal, sum(Impuesto_Servicio) as IVA, SUM(Total_Servicio) AS Total FROM factura_compra_servicios "
                    . " WHERE idFacturaCompra='$idCompra'";
        $consulta= $this->Query($sql);
        $TotalesServicios=$this->FetchArray($consulta);
        $TotalesCompra["Subtotal_Productos_Add"]=$TotalesCompraProductos["Subtotal"];
        $TotalesCompra["Impuestos_Productos_Add"]=$TotalesCompraProductos["IVA"];
        $TotalesCompra["Total_Productos_Add"]=$TotalesCompraProductos["Total"];
        $TotalesCompra["Subtotal_Descuentos_Productos_Add"]=$TotalesCompraProductos["SubtotalDescuento"];
        
        $TotalesCompra["Subtotal_Insumos"]=$TotalesCompraInsumos["Subtotal"];
        $TotalesCompra["Impuestos_Insumos"]=$TotalesCompraInsumos["IVA"];
        $TotalesCompra["Total_Insumos"]=$TotalesCompraInsumos["Total"];
        $TotalesCompra["Subtotal_Descuentos_Insumos"]=$TotalesCompraInsumos["SubtotalDescuento"];
        
        $TotalesCompra["ImpuestosAdicionales"]=$TotalImpuestosAdicionales;
        $TotalesCompra["DescuentosGlobales"]=$TotalDescuentosGlobales;
        
        $TotalesCompra["Subtotal_Servicios"]=$TotalesServicios["Subtotal"];
        $TotalesCompra["Impuestos_Servicios"]=$TotalesServicios["IVA"];
        $TotalesCompra["Total_Servicios"]=$TotalesServicios["Total"];
        $TotalesCompra["Total_Retenciones"]=$TotalRetenciones;
        $TotalesCompra["Subtotal_Productos_Dev"]=$TotalesItemsDevueltos["Subtotal"];
        $TotalesCompra["Impuestos_Productos_Dev"]=$TotalesItemsDevueltos["IVA"];
        $TotalesCompra["Total_Productos_Dev"]=$TotalesItemsDevueltos["Total"];
        $TotalesCompra["Subtotal_Productos"]=$TotalesCompra["Subtotal_Productos_Add"]-$TotalesCompra["Subtotal_Productos_Dev"];
        $TotalesCompra["Impuestos_Productos"]=$TotalesCompra["Impuestos_Productos_Add"]-$TotalesCompra["Impuestos_Productos_Dev"];
        $TotalesCompra["Total_Productos"]=$TotalesCompra["Total_Productos_Add"]-$TotalesCompra["Total_Productos_Dev"];
        $TotalesCompra["Gran_Subtotal"]=$TotalesCompra["Subtotal_Productos"]+$TotalesCompra["Subtotal_Servicios"]+$TotalesCompra["Subtotal_Insumos"]-$TotalesCompra["DescuentosGlobales"];
        $TotalesCompra["Gran_Impuestos"]=$TotalesCompra["Impuestos_Productos"]+$TotalesCompra["Impuestos_Servicios"]+$TotalesCompra["Impuestos_Insumos"]+$TotalesCompra["ImpuestosAdicionales"];
        $TotalesCompra["Gran_Total"]=$TotalesCompra["Total_Productos"]+$TotalesCompra["Total_Servicios"]+$TotalesCompra["Total_Insumos"]+$TotalesCompra["ImpuestosAdicionales"]-$TotalesCompra["DescuentosGlobales"];
        $TotalesCompra["Total_Pago"]=$TotalesCompra["Gran_Total"]-$TotalesCompra["Total_Retenciones"];
        return($TotalesCompra);
    }
    
    //Clase para agregar un item a una compra
    public function AgregueRetencionCompra($idCompra,$Cuenta,$Valor,$Porcentaje,$Vector) {
        //Proceso la informacion
        $DatosCuentas= $this->DevuelveValores("subcuentas", "PUC", $Cuenta);
        //////Agrego el registro           
        $tab="factura_compra_retenciones";
        $NumRegistros=5;

        $Columnas[0]="idCompra";            $Valores[0]=$idCompra;
        $Columnas[1]="CuentaPUC";           $Valores[1]=$Cuenta;
        $Columnas[2]="NombreCuenta";        $Valores[2]=$DatosCuentas["Nombre"];
        $Columnas[3]="ValorRetencion";      $Valores[3]=$Valor;
        $Columnas[4]="PorcentajeRetenido";  $Valores[4]=$Porcentaje;       
                            
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Clase para agregar un item a una compra
    public function AgregueDescuentoCompra($idCompra,$Cuenta,$Valor,$Porcentaje,$Vector) {
        //Proceso la informacion
        $DatosCuentas= $this->DevuelveValores("subcuentas", "PUC", $Cuenta);
        //////Agrego el registro           
        $tab="factura_compra_descuentos";
        $NumRegistros=5;

        $Columnas[0]="idCompra";                $Valores[0]=$idCompra;
        $Columnas[1]="CuentaPUCDescuento";      $Valores[1]=$Cuenta;
        $Columnas[2]="NombreCuentaDescuento";   $Valores[2]=$DatosCuentas["Nombre"];
        $Columnas[3]="ValorDescuento";          $Valores[3]=$Valor;
        $Columnas[4]="PorcentajeDescuento";     $Valores[4]=$Porcentaje;       
                            
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Clase para agregar un item a una compra
    public function AgregueImpuestoAdicionalCompra($idCompra,$Cuenta,$Valor,$Porcentaje,$Vector) {
        //Proceso la informacion
        $DatosCuentas= $this->DevuelveValores("subcuentas", "PUC", $Cuenta);
        //////Agrego el registro           
        $tab="factura_compra_impuestos_adicionales";
        $NumRegistros=5;

        $Columnas[0]="idCompra";       $Valores[0]=$idCompra;
        $Columnas[1]="CuentaPUC";      $Valores[1]=$Cuenta;
        $Columnas[2]="NombreCuenta";   $Valores[2]=$DatosCuentas["Nombre"];
        $Columnas[3]="Valor";          $Valores[3]=$Valor;
        $Columnas[4]="Porcentaje";     $Valores[4]=$Porcentaje;       
                            
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    //Contabilizar Items de la compra
    public function ContabilizarProductosCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        $TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $ParametrosContables=$this->DevuelveValores("parametros_contables", "ID", 4);   //Cuenta de inventarios
        $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $ParametrosContables["CuentaPUC"], $ParametrosContables["NombreCuenta"], "Compras", "DB", $TotalesCompra["Subtotal_Productos_Add"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
        $sql="SELECT SUM(`ImpuestoCompra`) AS IVA, `Tipo_Impuesto` AS TipoImpuesto FROM `factura_compra_items` WHERE `idFacturaCompra`='$idCompra' GROUP BY `Tipo_Impuesto` ";
        $consulta= $this->Query($sql);
        while($DatosImpuestos= $this->FetchArray($consulta)){
            $DatosTipoIVA= $this->DevuelveValores("porcentajes_iva", "Valor", $DatosImpuestos["TipoImpuesto"]);
            if($DatosImpuestos["IVA"]>0){
                $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosTipoIVA["CuentaPUC"], $DatosTipoIVA["NombreCuenta"], "Compras", "DB", $DatosImpuestos["IVA"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            }
        }
    }
    
    //Contabilizar insumos de la compra
    public function ContabilizarInsumosCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        $TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $ParametrosContables=$this->DevuelveValores("parametros_contables", "ID", 22);   //Cuenta de inventarios
        $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $ParametrosContables["CuentaPUC"], $ParametrosContables["NombreCuenta"], "Compras", "DB", $TotalesCompra["Subtotal_Insumos"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
        $sql="SELECT SUM(`ImpuestoCompra`) AS IVA, `Tipo_Impuesto` AS TipoImpuesto FROM `factura_compra_insumos` WHERE `idFacturaCompra`='$idCompra' GROUP BY `Tipo_Impuesto` ";
        $consulta= $this->Query($sql);
        while($DatosImpuestos= $this->FetchArray($consulta)){
            $DatosTipoIVA= $this->DevuelveValores("porcentajes_iva", "Valor", $DatosImpuestos["TipoImpuesto"]);
            if($DatosImpuestos["IVA"]>0){
                $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosTipoIVA["CuentaPUC"], $DatosTipoIVA["NombreCuenta"], "Compras", "DB", $DatosImpuestos["IVA"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            }
        }
    }
    
    //Contabilizar Items de la compra
    public function ContabilizarServiciosCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        //$TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $sql="SELECT CuentaPUC_Servicio AS CuentaPUC,Nombre_Cuenta AS NombreCuenta, Concepto_Servicio AS Concepto,`Subtotal_Servicio` AS Subtotal,`Impuesto_Servicio` AS IVA, `Tipo_Impuesto` AS TipoImpuesto FROM `factura_compra_servicios` WHERE `idFacturaCompra`='$idCompra' ";
        $consulta= $this->Query($sql);
        while($DatosServicios= $this->FetchArray($consulta)){
            $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosServicios["CuentaPUC"], $DatosServicios["NombreCuenta"], "Servicios", "DB", $DatosServicios["Subtotal"], $DatosServicios["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            $DatosTipoIVA= $this->DevuelveValores("porcentajes_iva", "Valor", $DatosServicios["TipoImpuesto"]);
            if($DatosServicios["IVA"]>0){
                $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosTipoIVA["CuentaPUC"], $DatosTipoIVA["NombreCuenta"], "Servicios", "DB", $DatosServicios["IVA"], $DatosServicios["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            }
        }
    }
    //Contabilice Retenciones
    public function ContabilizarRetencionesCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        //$TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $sql="SELECT SUM(`ValorRetencion`) AS Retencion, `CuentaPUC` AS CuentaPUC,`NombreCuenta` AS NombreCuenta FROM `factura_compra_retenciones` WHERE `idCompra`='$idCompra' GROUP BY `CuentaPUC` ";
        $consulta= $this->Query($sql);
        while($DatosRetencion= $this->FetchArray($consulta)){
            
            $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosRetencion["CuentaPUC"], $DatosRetencion["NombreCuenta"], "Retenciones", "CR", $DatosRetencion["Retencion"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            
        }
    }
    
    //Contabilice impuestos adicionales
    public function ContabilizarImpuestosAdicionalesCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        //$TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $sql="SELECT SUM(`Valor`) AS Valor, `CuentaPUC` AS CuentaPUC,`NombreCuenta` AS NombreCuenta FROM `factura_compra_impuestos_adicionales` WHERE `idCompra`='$idCompra' GROUP BY `CuentaPUC` ";
        $consulta= $this->Query($sql);
        while($DatosRetencion= $this->FetchArray($consulta)){
            
            $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosRetencion["CuentaPUC"], $DatosRetencion["NombreCuenta"], "Impuestos Adicionales", "DB", $DatosRetencion["Valor"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            //$this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $CuentaDestino, $NombreCuenta, "Impuestos Adicionales", "CR", $DatosRetencion["Valor"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
        }
    }
    
    //Contabilice impuestos adicionales
    public function ContabilizarDescuentosGeneralesCompra($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        //$TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $sql="SELECT SUM(`ValorDescuento`) AS Valor, `CuentaPUCDescuento` AS CuentaPUC,`NombreCuentaDescuento` AS NombreCuenta FROM `factura_compra_descuentos` WHERE `idCompra`='$idCompra' GROUP BY `CuentaPUCDescuento` ";
        $consulta= $this->Query($sql);
        while($DatosRetencion= $this->FetchArray($consulta)){
            
            $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosRetencion["CuentaPUC"], $DatosRetencion["NombreCuenta"], "Descuentos Generales", "CR", $DatosRetencion["Valor"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            //$this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $CuentaDestino, $NombreCuenta, "Descuentos Generales", "DB", $DatosRetencion["Valor"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
        }
    }
    
    //Guarde una Compra
    public function GuardarFacturaCompra($idCompra,$TipoPago,$CuentaOrigen,$CuentaPUCCXP,$FechaProgramada,$Vector) {
        
        
        $DatosEmpresa=$this->ValorActual("empresapro", "CXPAutomaticas", "idEmpresaPro='1'");
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        $TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        $TotalRetenciones= $TotalesCompra["Total_Retenciones"];
        $this->ContabilizarProductosCompra($idCompra);     //Contabilizo los productos agregados
        $this->ContabilizarServiciosCompra($idCompra);     //Contabilizo los Servicios agregados
        $this->ContabilizarRetencionesCompra($idCompra);   //Contabilizo las Retenciones
        $this->ContabilizarInsumosCompra($idCompra);     //Contabilizo los productos agregados
        //Contabilizo salida de dinero o cuenta X Pagar
        if($TipoPago=="Credito"){            
            $ParametrosContables=$this->DevuelveValores("subcuentas", "PUC", $CuentaPUCCXP);
            $CuentaDestino=$CuentaPUCCXP;
            $NombreCuenta=$ParametrosContables["Nombre"];
        }else{
            $DatosSubcuentas= $this->DevuelveValores("subcuentas", "PUC", $CuentaOrigen);
            $CuentaDestino=$CuentaOrigen;
            $NombreCuenta=$DatosSubcuentas["Nombre"];
        }
        $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $CuentaDestino, $NombreCuenta, "Compras", "CR", $TotalesCompra["Total_Pago"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
        //if($TotalesCompra["Total_Productos_Dev"]>0){  //Si hay devoluciones en compras se debita la cuenta de proveedores
          //$this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $CuentaDestino, $NombreCuenta, "DevolucionCompras", "DB", $TotalesCompra["Total_Productos_Dev"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            
        //}
        $this->ContabilizarDescuentosGeneralesCompra($idCompra);
        $this->ContabilizarImpuestosAdicionalesCompra($idCompra);
        $this->ContabiliceProductosDevueltos($idCompra);
        $this->IngreseRetireProductosInventarioCompra($idCompra,"ENTRADA");  //Ingreso los productos al inventario
        $this->IngreseRetireProductosInventarioCompra($idCompra,"SALIDA");   //Retiro los productos del inventario
        //Si es credito se ingresa a cuentas X Pagar
        
        if($TipoPago=="Credito" AND $DatosEmpresa["CXPAutomaticas"]=="SI"){
            $SubtotalCuentaXPagar=$TotalesCompra["Gran_Subtotal"];
            $TotalIVACXP=$TotalesCompra["Gran_Impuestos"];
            $TotalCompraCXP=$TotalesCompra["Gran_Total"];
            $VectorCuentas["CuentaPUC"]=$CuentaPUCCXP;
            $this->RegistrarCuentaXPagar($DatosFacturaCompra["Fecha"], $DatosFacturaCompra["NumeroFactura"], $FechaProgramada, "factura_compra", $idCompra, $SubtotalCuentaXPagar, $TotalIVACXP, $TotalCompraCXP, $TotalesCompra["Total_Retenciones"], 0, 0, $DatosFacturaCompra["Tercero"], $DatosFacturaCompra["idSucursal"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["Soporte"], $VectorCuentas);
        }
        $this->ActualizaRegistro("factura_compra", "Estado", "CERRADA", "ID", $idCompra);
        $this->ActualizaRegistro("factura_compra", "TipoPago", "$TipoPago", "ID", $idCompra);
    }
    
    
    /**
     * Funcion para crear un traslado desde una compra
     * @param type $idCompra -> id de la compra de donde se va a realizar el traslado
     * @param type $Vector ->Futuro
     */
    public function CrearTrasladoDesdeCompra($idCompra,$idSede,$Vector) {
        $DatosCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        $VectorTraslado["idBodega"]=1;
        $fecha=date("Y-m-d");
        $hora=date("H:i:s");
        $Concepto="FC_$idCompra";
        $Destino=$idSede;        
        $Consulta=$this->ConsultarTabla("factura_compra_items", "WHERE idFacturaCompra='$idCompra'");
        if($this->NumRows($Consulta)){
            $idTraslado=$this->CrearTraslado($fecha, $hora, $Concepto, $Destino, $VectorTraslado);
            while($ItemsCompra=$this->FetchArray($Consulta)){
                $this->AgregarItemTraslado($idTraslado, $ItemsCompra["idProducto"], $ItemsCompra["Cantidad"], "");
            }
            return($idTraslado);
        }else{
            return("ENI"); //No Items en la factura de compra
        }
        
    }
    
    //Ingrese los items al inventario o retire items del inventario
    public function IngreseRetireProductosInventarioCompra($idCompra,$Movimiento,$idTabla='idFacturaCompra') {
        $obInsumos=new Recetas(1);
        $Detalle="FacturaCompra";
            if($idTabla=='idNotaDevolucion'){
                $Detalle="NotaDevolucion";
            }
        if($Movimiento=="ENTRADA" AND $idTabla=='idNotaDevolucion'){
            $consulta= $this->ConsultarTabla("factura_compra_items_devoluciones", "WHERE $idTabla='$idCompra'");
        }    
        if($Movimiento=="ENTRADA" AND $idTabla=='idFacturaCompra'){
            $consulta= $this->ConsultarTabla("factura_compra_items", "WHERE $idTabla='$idCompra'");
            $DatosKardex["CalcularCostoPromedio"]=1;
        }
        if($Movimiento=="SALIDA"){
            $consulta= $this->ConsultarTabla("factura_compra_items_devoluciones", "WHERE $idTabla='$idCompra'");
        } 
        while($DatosProductos= $this->FetchArray($consulta)){
            $DatosProductoGeneral= $this->DevuelveValores("productosventa", "idProductosVenta", $DatosProductos["idProducto"]);
            $DatosKardex["Cantidad"]=$DatosProductos["Cantidad"];
            $DatosKardex["idProductosVenta"]=$DatosProductos["idProducto"];
            $DatosKardex["CostoUnitario"]=$DatosProductos['CostoUnitarioCompra'];
            $DatosKardex["Existencias"]=$DatosProductoGeneral['Existencias'];
            
            $DatosKardex["Detalle"]=$Detalle;   
            $DatosKardex["idDocumento"]=$idCompra;
            $DatosKardex["TotalCosto"]=$DatosProductos["Cantidad"]*$DatosProductos['CostoUnitarioCompra'];
            $DatosKardex["Movimiento"]=$Movimiento;
            $DatosKardex["CostoUnitarioPromedio"]=$DatosProductoGeneral["CostoUnitarioPromedio"];
            $DatosKardex["CostoTotalPromedio"]=$DatosProductoGeneral["CostoUnitarioPromedio"]*$DatosKardex["Cantidad"];
            $this->InserteKardex($DatosKardex);
        }
        
        if($Movimiento=="ENTRADA" AND $idTabla=='idFacturaCompra'){
            $consulta= $this->ConsultarTabla("factura_compra_insumos", "WHERE $idTabla='$idCompra'");
            while($DatosProductos= $this->FetchArray($consulta)){
                $DatosProductoGeneral= $this->DevuelveValores("insumos", "ID", $DatosProductos["idProducto"]);
            
                $obInsumos->KardexInsumo($Movimiento, $Detalle, $idCompra, $DatosProductoGeneral["Referencia"], $DatosProductos["Cantidad"], $DatosProductos["CostoUnitarioCompra"], "");
            }
        }    
        
    }
    
    //Revise si hay productos devueltos y contabilice
    public function ContabiliceProductosDevueltos($idCompra) {
        $DatosFacturaCompra= $this->DevuelveValores("factura_compra", "ID", $idCompra);
        $TotalesCompra=$this->CalculeTotalesCompra($idCompra);
        
        if($TotalesCompra["Total_Productos_Dev"]>0){
            
            $ParametrosContables=$this->DevuelveValores("parametros_contables", "ID", 4); //Cuenta de inventarios
            $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $ParametrosContables["CuentaPUC"], $ParametrosContables["NombreCuenta"], "DevolucionCompras", "CR", $TotalesCompra["Subtotal_Productos_Dev"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
            $sql="SELECT SUM(`ImpuestoCompra`) AS IVA, `Tipo_Impuesto` AS TipoImpuesto FROM `factura_compra_items_devoluciones` WHERE `idFacturaCompra`='$idCompra' GROUP BY `Tipo_Impuesto` ";
            $consulta= $this->Query($sql);
            while($DatosImpuestos= $this->FetchArray($consulta)){
                $DatosTipoIVA= $this->DevuelveValores("porcentajes_iva", "Valor", $DatosImpuestos["TipoImpuesto"]);
                if($DatosImpuestos["IVA"]>0){
                    $this->IngreseMovimientoLibroDiario($DatosFacturaCompra["Fecha"], "FacturaCompra", $idCompra, $DatosFacturaCompra["NumeroFactura"], $DatosFacturaCompra["Tercero"], $DatosTipoIVA["CuentaPUC"], $DatosTipoIVA["NombreCuenta"], "DevolucionCompras", "CR", $DatosImpuestos["IVA"], $DatosFacturaCompra["Concepto"], $DatosFacturaCompra["idCentroCostos"], $DatosFacturaCompra["idSucursal"], "");
                }
            }
                       
        }
    }
    /**
     * Actualiza los precios de venta de los productos que están en una factura de compra 
     * @param type $idCompra
     */
    function ActualicePreciosVentaFacturaCompra($idCompra) {
        $sql="UPDATE productosventa pv INNER JOIN factura_compra_items fc SET pv.PrecioVenta=fc.PrecioVenta
        WHERE fc.idFacturaCompra='$idCompra' AND fc.PrecioVenta>=fc.CostoUnitarioCompra AND fc.idProducto=pv.idProductosVenta;";
        $this->Query($sql);
    }
    /**
     * Crear una orden de compra
     * @param type $Fecha
     * @param type $Tercero
     * @param type $Descripcion
     * @param type $PlazoEntrega
     * @param type $NoCotizacion
     * @param type $Condiciones
     * @param type $Solicitante
     * @param type $idCentroCostos
     * @param type $idSucursal
     * @param type $Vector
     * @return type
     */
    function CrearOrdenDeCompra($Fecha,$Tercero,$Descripcion,$PlazoEntrega,$NoCotizacion,$Condiciones,$Solicitante,$idCentroCostos,$idSucursal,$Vector){
        
       $Datos["Fecha"]=$Fecha; 
       $Datos["Tercero"]=$Tercero; 
       $Datos["Descripcion"]=$Descripcion; 
       $Datos["PlazoEntrega"]=$PlazoEntrega; 
       $Datos["NoCotizacion"]=$NoCotizacion; 
       $Datos["Condiciones"]=$Condiciones;
       $Datos["Solicitante"]=$Solicitante; 
       $Datos["UsuarioCreador"]=$_SESSION["idUser"]; 
       $Datos["Estado"]="ABIERTA"; 
       $Datos["idCentroCostos"]=$idCentroCostos; 
       $Datos["idSucursal"]=$idSucursal; 
       
       $sql=$this->getSQLInsert("ordenesdecompra", $Datos);
       $this->Query($sql);
       $idCompra=$this->ObtenerMAX("ordenesdecompra","ID", 1,"");
       return($idCompra);
    }
    /**
     * Ingresa un item a una orden de compra
     * @param type $idOrden
     * @param type $idProducto
     * @param type $Referencia
     * @param type $Nombre
     * @param type $Cantidad
     * @param type $CostoUnitario
     * @param type $TipoIVA
     * @param type $IVAIncluido
     * @param type $Vector
     */
    public function IngresaItemOrdenCompra($idOrden,$idProducto,$Cantidad,$CostoUnitario,$TipoIVA,$IVAIncluido,$Vector) {
        $DatosTipoImpuesto= $this->DevuelveValores("porcentajes_iva", "ID", $TipoIVA);
        $TipoIVA=$DatosTipoImpuesto["Valor"];
        $DatosProducto= $this->DevuelveValores("productosventa", "idProductosVenta", $idProducto);
        $Referencia=$DatosProducto["Referencia"];
        $Nombre=$DatosProducto["Nombre"];
        if($IVAIncluido=="SI"){
            if(is_numeric($TipoIVA)){
                $CostoUnitario=round($CostoUnitario/(1+$TipoIVA),2);
            }            
        }
        $Subtotal=round($CostoUnitario*$Cantidad,2);
        if(is_numeric($TipoIVA)){
            $Impuestos=round($Subtotal*$TipoIVA);
        }else{
            $Impuestos=0;
        }
        
        $Impuestos= round($Subtotal*$TipoIVA,2);
        $Total=round($Subtotal+$Impuestos,2);
        $sql="SELECT * FROM ordenesdecompra_items WHERE NumOrden='$idOrden' AND idProducto='$idProducto'";
        $Consulta=$this->Query($sql);
        $DatosItem=$this->FetchAssoc($Consulta);
        if($DatosItem==''){
            //////Agrego el registro           
            $tab="ordenesdecompra_items";
            $NumRegistros=10;

            $Columnas[0]="NumOrden";        $Valores[0]=$idOrden;
            $Columnas[1]="idProducto";      $Valores[1]=$idProducto;
            $Columnas[2]="Cantidad";        $Valores[2]=$Cantidad;
            $Columnas[3]="ValorUnitario";   $Valores[3]=$CostoUnitario;
            $Columnas[4]="Subtotal";        $Valores[4]=$Subtotal;
            $Columnas[5]="IVA";             $Valores[5]=$Impuestos;
            $Columnas[6]="Total";           $Valores[6]=$Total;
            $Columnas[7]="Tipo_Impuesto";   $Valores[7]=$TipoIVA;
            $Columnas[8]="Referencia";      $Valores[8]=$Referencia;  
            $Columnas[9]="Descripcion";     $Valores[9]=$Nombre;   
            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }else{
            $idItem=$DatosItem["ID"];
            $sql="UPDATE ordenesdecompra_items SET Cantidad=Cantidad+$Cantidad, Subtotal=round(ValorUnitario*Cantidad,2),"
                    . "IVA=round(Subtotal*Tipo_Impuesto,2),Total=round(Subtotal+IVA,2) WHERE ID='$idItem'";
            $this->Query($sql);
        }
    }
    /**
     * Agrega items desde una orden de compra
     * @param type $idCompra
     * @param type $idOrdenCompra
     * @param type $Vector
     */
    public function AgregueItemDesdeOrdenCompra($idCompra,$idOrdenCompra,$Vector) {
        $sql="SELECT * FROM ordenesdecompra_items WHERE NumOrden='$idOrdenCompra'";
        $Consulta=$this->Query($sql);
        //////Agrego el registro           
        $tab="factura_compra_items";
        $NumRegistros=8;
        while($DatosOC=$this->FetchAssoc($Consulta)){
            $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
            $Columnas[1]="idProducto";          $Valores[1]=$DatosOC["idProducto"];
            $Columnas[2]="Cantidad";            $Valores[2]=$DatosOC["Cantidad"];
            $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$DatosOC["ValorUnitario"];
            $Columnas[4]="SubtotalCompra";      $Valores[4]=$DatosOC["Subtotal"];
            $Columnas[5]="ImpuestoCompra";      $Valores[5]=$DatosOC["IVA"];
            $Columnas[6]="TotalCompra";         $Valores[6]=$DatosOC["Total"];
            $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$DatosOC["Tipo_Impuesto"];

            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
            
        }
    }
      
    /**
     * Copiar una factura de compra
     * @param type $idCompra->Numero de la factura que se copiará
     * @param type $idCompraNew -> Numero de la nueva factura
     * @param type $idUser->id del usuario creador
     * @param type $Vector->Futuro
     */
    public function CopiarFacturaCompra($idCompra,$idCompraNew,$idUser, $Vector) {
        $Datos= $this->ConsultarTabla("factura_compra_items", " WHERE idFacturaCompra='$idCompra'");
        while($DatosItems=$this->FetchArray($Datos)){
            $tab="factura_compra_items";
            $NumRegistros=12;

            $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompraNew;
            $Columnas[1]="idProducto";          $Valores[1]=$DatosItems["idProducto"];
            $Columnas[2]="Cantidad";            $Valores[2]=$DatosItems["Cantidad"];
            $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$DatosItems["CostoUnitarioCompra"];
            $Columnas[4]="SubtotalCompra";      $Valores[4]=$DatosItems["SubtotalCompra"];
            $Columnas[5]="ImpuestoCompra";      $Valores[5]=$DatosItems["ImpuestoCompra"];
            $Columnas[6]="TotalCompra";         $Valores[6]=$DatosItems["TotalCompra"];
            $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$DatosItems["Tipo_Impuesto"];
            $Columnas[8]="ProcentajeDescuento";       $Valores[8]=$DatosItems["ProcentajeDescuento"];
            $Columnas[9]="ValorDescuento";       $Valores[9]=$DatosItems["ValorDescuento"];
            $Columnas[10]="SubtotalDescuento";       $Valores[10]=$DatosItems["SubtotalDescuento"];
            $Columnas[11]="PrecioVenta";       $Valores[11]=$DatosItems["PrecioVenta"];
            
            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }
        
         //Copio los items de los insumos agregados
        $Datos= $this->ConsultarTabla("factura_compra_insumos", " WHERE idFacturaCompra='$idCompra'");
        while($DatosItems=$this->FetchArray($Datos)){
            $tab="factura_compra_insumos";
            $NumRegistros=8;

            $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompraNew;
            $Columnas[1]="idProducto";          $Valores[1]=$DatosItems["idProducto"];
            $Columnas[2]="Cantidad";            $Valores[2]=$DatosItems["Cantidad"];
            $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$DatosItems["CostoUnitarioCompra"];
            $Columnas[4]="SubtotalCompra";      $Valores[4]=$DatosItems["SubtotalCompra"];
            $Columnas[5]="ImpuestoCompra";      $Valores[5]=$DatosItems["ImpuestoCompra"];
            $Columnas[6]="TotalCompra";         $Valores[6]=$DatosItems["TotalCompra"];
            $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$DatosItems["Tipo_Impuesto"];

            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }
        /*
        //Copio los items de los productos devueltos
        $Datos= $this->ConsultarTabla("factura_compra_items_devoluciones", " WHERE idFacturaCompra='$idCompra'");
        while($DatosItems=$this->FetchArray($Datos)){
            $tab="factura_compra_items_devoluciones";
            $NumRegistros=8;

            $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompraNew;
            $Columnas[1]="idProducto";          $Valores[1]=$DatosItems["idProducto"];
            $Columnas[2]="Cantidad";            $Valores[2]=$DatosItems["Cantidad"];
            $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$DatosItems["CostoUnitarioCompra"];
            $Columnas[4]="SubtotalCompra";      $Valores[4]=$DatosItems["SubtotalCompra"];
            $Columnas[5]="ImpuestoCompra";      $Valores[5]=$DatosItems["ImpuestoCompra"];
            $Columnas[6]="TotalCompra";         $Valores[6]=$DatosItems["TotalCompra"];
            $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$DatosItems["Tipo_Impuesto"];

            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }
        */
        //Copio los items de los servicios
        $Datos= $this->ConsultarTabla("factura_compra_servicios", " WHERE idFacturaCompra='$idCompra'");
        while($DatosItems=$this->FetchArray($Datos)){
            //////Agrego el registro           
            $tab="factura_compra_servicios";
            $NumRegistros=8;

            $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompraNew;
            $Columnas[1]="CuentaPUC_Servicio";  $Valores[1]=$DatosItems["CuentaPUC_Servicio"];
            $Columnas[2]="Nombre_Cuenta";       $Valores[2]=$DatosItems["Nombre_Cuenta"];
            $Columnas[3]="Concepto_Servicio";   $Valores[3]=$DatosItems["Concepto_Servicio"];
            $Columnas[4]="Subtotal_Servicio";   $Valores[4]=$DatosItems["Subtotal_Servicio"];
            $Columnas[5]="Impuesto_Servicio";   $Valores[5]=$DatosItems["Impuesto_Servicio"];
            $Columnas[6]="Total_Servicio";      $Valores[6]=$DatosItems["Total_Servicio"];
            $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$DatosItems["Tipo_Impuesto"];

            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }
        /*
        //Copio los items de las retenciones
        $Datos= $this->ConsultarTabla("factura_compra_retenciones", " WHERE idCompra='$idCompra'");
        while($DatosItems=$this->FetchArray($Datos)){
            //////Agrego el registro           
            $tab="factura_compra_retenciones";
            $NumRegistros=5;

            $Columnas[0]="idCompra";            $Valores[0]=$idCompraNew;
            $Columnas[1]="CuentaPUC";           $Valores[1]=$DatosItems["CuentaPUC"];
            $Columnas[2]="NombreCuenta";        $Valores[2]=$DatosItems["NombreCuenta"];
            $Columnas[3]="ValorRetencion";      $Valores[3]=$DatosItems["ValorRetencion"];
            $Columnas[4]="PorcentajeRetenido";  $Valores[4]=$DatosItems["PorcentajeRetenido"];     

            $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        }
         * 
         */
        
        
        
    }
     
    /**
     * Copiar los items de una orden a otra
     * @param type $idOrdenACopiar
     * @param type $idOrdenDestino
     */
    public function CopiarItemsOrdenCompra($idOrdenACopiar,$idOrdenDestino) {
       
        $Consulta=$this->ConsultarTabla("ordenesdecompra_items", "WHERE NumOrden='$idOrdenACopiar'");
        while($DatosOrden=$this->FetchAssoc($Consulta)){
            $DatosTiposIVA=$this->DevuelveValores("porcentajes_iva", "Valor", $DatosOrden["Tipo_Impuesto"]);
            $this->IngresaItemOrdenCompra($idOrdenDestino, $DatosOrden["idProducto"], $DatosOrden["Cantidad"], $DatosOrden["ValorUnitario"], $DatosTiposIVA["ID"], "NO", "");
        
        }
        
    }
    
    
    public function AgregueItemDesdeOrdenCompraVerificada($idCompra,$idOrdenCompra,$Vector) {
        $sql="SELECT oc.idProducto,oc.Cantidad,oc.Recibido,(oc.Cantidad-oc.Recibido) as Diferencia,oc.ValorUnitario,oc.Tipo_Impuesto,pv.PrecioVenta"
                . " FROM ordenesdecompra_items oc INNER JOIN productosventa pv ON pv.idProductosVenta=oc.idProducto "
                . "WHERE oc.NumOrden='$idOrdenCompra'";
        $Consulta=$this->Query($sql);
        //////Agrego los registros           
        
        while($DatosOC=$this->FetchAssoc($Consulta)){
            if($DatosOC["Cantidad"]>0){
                $this->AgregueProductoCompra($idCompra, $DatosOC["idProducto"], $DatosOC["Cantidad"], $DatosOC["ValorUnitario"], $DatosOC["PrecioVenta"], $DatosOC["Tipo_Impuesto"], "NO", "",1);
            }
            if($DatosOC["Diferencia"]>0){
                $this->DevolverProductoDesdeOrden($idCompra,$DatosOC["idProducto"],$DatosOC["Diferencia"],$DatosOC["ValorUnitario"],$DatosOC["Tipo_Impuesto"]);
            }
            
        }
    }
    
    //Clase para devolver un item a una compra
    public function DevolverProductoDesdeOrden($idCompra,$idProducto,$Cantidad,$CostoUnitario,$TipoIVA) {
        //Proceso la informacion
        
        $Subtotal=round($CostoUnitario*$Cantidad);
        $Impuestos= round($Subtotal*$TipoIVA);
        $Total=$Subtotal+$Impuestos;
        //////Agrego el registro           
        $tab="factura_compra_items_devoluciones";
        $NumRegistros=8;

        $Columnas[0]="idFacturaCompra";     $Valores[0]=$idCompra;
        $Columnas[1]="idProducto";          $Valores[1]=$idProducto;
        $Columnas[2]="Cantidad";            $Valores[2]=$Cantidad;
        $Columnas[3]="CostoUnitarioCompra"; $Valores[3]=$CostoUnitario;
        $Columnas[4]="SubtotalCompra";      $Valores[4]=$Subtotal;
        $Columnas[5]="ImpuestoCompra";      $Valores[5]=$Impuestos;
        $Columnas[6]="TotalCompra";         $Valores[6]=$Total;
        $Columnas[7]="Tipo_Impuesto";       $Valores[7]=$TipoIVA;
                    
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
    }
    
    /**
     * Fin Clase
     */
}
