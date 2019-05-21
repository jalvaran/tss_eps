<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Facturacion extends ProcesoVenta{
    /**
     * Crear una factura
     * @param type $idFactura -> identificador de la factura
     * @param type $Fecha
     * @param type $Hora
     * @param type $idResolucion
     * @param type $TipoFactura     -> Tipo de Factura según Resolucion
     * @param type $Prefijo         ->Prefijo de la Factura
     * @param type $NumeroFactura   ->Numero de la factura
     * @param type $OrdenCompra
     * @param type $OrdenSalida
     * @param type $FormaPago       ->Si es de contado, a credito, sistecredito, 
     * @param type $Subtotal
     * @param type $IVA
     * @param type $Total
     * @param type $Descuentos
     * @param type $SaldoFactura    
     * @param type $idCotizacion    ->Si viene de una cotizacion
     * @param type $idEmpresa       ->Empresa a la que pertecene la factura
     * @param type $idCentroCostos  ->Centro de costos
     * @param type $idSucursal      ->Sucursal de la factura    
     * @param type $idUsuario       ->id del Usuario
     * @param type $idCliente
     * @param type $TotalCostos     ->Total costos de la factura
     * @param type $Observaciones   ->Observaciones de la factura
     * @param type $Efectivo        ->Con cuanto en efectivo se pagó
     * @param type $Devuelta        ->Cuanto devuelve
     * @param type $Cheques         ->Cuanto se pagó en cheques
     * @param type $Otros           ->En bonos u otros
     * @param type $Tarjetas        ->Cuanto en tarjeta de credito
     * @param type $idTarjetas      ->El tipo de tarjeta proveniente de tarjetas tipo
     * @param type $ReportarFacturaElectronica -> 0 para reportar a factura electronica 1 para no
     * @param type $Vector
     */
    public function CrearFactura($idFactura,$Fecha,$Hora,$idResolucion,$OrdenCompra,$OrdenSalida,$FormaPago,$Subtotal,$IVA,$Total,$Descuentos,$SaldoFactura,$idCotizacion,$idEmpresa,$idCentroCostos,$idSucursal,$idUsuario,$idCliente,$TotalCostos,$Observaciones,$Efectivo,$Devuelta,$Cheques,$Otros,$Tarjetas,$idTarjetas,$ReportarFacturaElectronica,$Vector) {
        if($idEmpresa==0 or $idEmpresa==''){
            $idEmpresa=1;
        }
        $DatosResolucion=$this->ValorActual("empresapro_resoluciones_facturacion", "Estado,Completada,Prefijo,Tipo,Desde,Hasta", " ID='$idResolucion'");        
        $Disponibilidad=$DatosResolucion["Estado"];
        if($DatosResolucion["Completada"]=="SI"){
            return("E1"); //Error 1, la resolucion ya fue completada
        }
        if($DatosResolucion["Estado"]=="OC"){
            return("E2"); //Error 2, resolucion ocupada
        }
        $this->ActualizaRegistro("empresapro_resoluciones_facturacion", "Estado", "OC", "ID", $idResolucion);
        $NumeroFactura=$this->ObtenerMAX("facturas", "NumeroFactura", "idResolucion", $idResolucion);
        $NumeroFactura++;
        if($NumeroFactura==1){ //Se verifica si es la primer factura
            $NumeroFactura=$DatosResolucion["Desde"];
        }
        if($NumeroFactura>$DatosResolucion["Hasta"]){
            $this->ActualizaRegistro("empresapro_resoluciones_facturacion", "Estado", "", "ID", $idResolucion);
            $this->ActualizaRegistro("empresapro_resoluciones_facturacion", "Completada", "SI", "ID", $idResolucion);
            return("E1"); //Error 1, resolucion Completa
           
        }
        if($NumeroFactura==$DatosResolucion["Hasta"]){
            $this->ActualizaRegistro("empresapro_resoluciones_facturacion", "Completada", "SI", "ID", $idResolucion);
        }
        
        $Prefijo=$DatosResolucion["Prefijo"];
        $TipoFactura=$DatosResolucion["Tipo"];
        
        $Datos["idFacturas"]=$idFactura;
        $Datos["idResolucion"]=$idResolucion;
        $Datos["TipoFactura"]=$TipoFactura;
        $Datos["Prefijo"]=$Prefijo;
        $Datos["NumeroFactura"]=$NumeroFactura;
        $Datos["Fecha"]=$Fecha;
        $Datos["Hora"]=$Hora;
        $Datos["OCompra"]=$OrdenCompra;
        $Datos["OSalida"]=$OrdenSalida;
        $Datos["FormaPago"]=$FormaPago;
        $Datos["Subtotal"]=$Subtotal;
        $Datos["IVA"]=$IVA;
        $Datos["Descuentos"]=$Descuentos;
        $Datos["Total"]=$Total;
        $Datos["SaldoFact"]=$SaldoFactura;
        $Datos["Cotizaciones_idCotizaciones"]=$idCotizacion;
        $Datos["EmpresaPro_idEmpresaPro"]=$idEmpresa;
        $Datos["CentroCosto"]=$idCentroCostos;
        $Datos["idSucursal"]=$idSucursal;
        $Datos["Usuarios_idUsuarios"]=$idUsuario;
        $Datos["Clientes_idClientes"]=$idCliente;
        $Datos["TotalCostos"]=$TotalCostos;
        $Datos["ObservacionesFact"]=$Observaciones;
        $Datos["Efectivo"]=$Efectivo;
        $Datos["Devuelve"]=$Devuelta;        
        $Datos["Cheques"]=$Cheques;
        $Datos["Otros"]=$Otros;
        $Datos["Tarjetas"]=$Tarjetas;
        $Datos["ReporteFacturaElectronica"]=$ReportarFacturaElectronica;
        
        $sql= $this->getSQLInsert("facturas", $Datos);
        $this->Query($sql);
        $this->ActualizaRegistro("empresapro_resoluciones_facturacion", "Estado", "", "ID", $idResolucion);
        return($NumeroFactura);
    }
    /**
     * Crea un id único para una factura
     * @return type
     */
    public function idFactura() {
        $ID=date("YmdHis").microtime(false);
        $ID= str_replace(" ", "_", $ID);
        $ID= str_replace(".", "_", $ID);
        return($ID);
    }
    
    /**
     * Copia los items de una cotizacion a una factura
     * @param type $idCotizacion
     * @param type $idFactura
     * @param type $FechaFactura
     * @param type $Vector
     */
    
    public function CopiarItemsCotizacionAItemsFactura($idCotizacion,$idFactura,$FechaFactura,$idUsuario,$Vector) {
        
        $sql="INSERT INTO facturas_items (FechaFactura,idFactura, TablaItems,Referencia,Nombre,ValorUnitarioItem,Cantidad,Dias,SubtotalItem,IVAItem,TotalItem,Departamento,SubGrupo1,SubGrupo2,SubGrupo3,SubGrupo4,SubGrupo5,PorcentajeIVA,idPorcentajeIVA,PrecioCostoUnitario,SubtotalCosto,TipoItem,CuentaPUC,GeneradoDesde,NumeroIdentificador,idUsuarios) 
            SELECT '$FechaFactura','$idFactura', TablaOrigen,Referencia,Descripcion,ValorUnitario,Cantidad,Multiplicador,Subtotal,IVA,Total,Departamento,SubGrupo1,SubGrupo2,SubGrupo3,SubGrupo4,SubGrupo5,PorcentajeIVA,idPorcentajeIVA,PrecioCosto,SubtotalCosto,TipoItem,CuentaPUC,'cotizacionesv5','$idCotizacion','$idUsuario'
            FROM cot_itemscotizaciones WHERE NumCotizacion='$idCotizacion'";
        $this->Query($sql);
        
    }
    
    /**
     * Ingresa una factura a la cartera
     * @param type $idFactura
     * @param type $Fecha
     * @param type $idCliente
     * @param type $CmbFormaPago -> trae el numero de dias que tiene de plazo la factura
     * @param type $SaldoFactura -> El saldo con el que ingresará a cartera
     * @param type $Vector
     */    
    public function IngreseCartera($idFactura,$Fecha,$idCliente,$CmbFormaPago,$SaldoFactura,$Vector) {
        
        $SumaDias=$CmbFormaPago;        
        if($CmbFormaPago=="SisteCredito" or $CmbFormaPago=="KUPY"){
            return;
        }
           
        $Datos["Fecha"]=$Fecha; 
        $Datos["Dias"]=$SumaDias;
        $FechaVencimiento=$this->SumeDiasFecha($Datos);
        $Datos["idFactura"]=$idFactura; 
        $Datos["FechaFactura"]=$Fecha; 
        $Datos["FechaVencimiento"]=$FechaVencimiento;
        $Datos["idCliente"]=$idCliente;
        $Datos["SaldoFactura"]=$SaldoFactura;
        if($SaldoFactura>0){
            $this->InsertarFacturaEnCartera($Datos);///Inserto La factura en la cartera
        }
        
            
    }
    
    /**
     * Cruza un anticipo en una factura
     * @param type $idFactura
     * @param type $Fecha
     * @param type $ValorAnticipo
     * @param type $CuentaDestino        ->Podrá ser la cuenta de clientes (Si es a credito) o la que cuenta donde ingrese el dinero recibido para el caso de ser de contado
     * @param type $NombreCuentaDestino
     * @param type $vector
     */
    public function CruzarAnticipoAFactura($idFactura,$Fecha,$ValorAnticipo,$CuentaDestino,$NombreCuentaDestino,$vector) {
        
        $DatosFactura=$this->DevuelveValores("facturas", "idFacturas", $idFactura);
        $DatosCliente=$this->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
        $ParametrosAnticipos=$this->DevuelveValores("parametros_contables", "ID", 20);
        $this->IngreseMovimientoLibroDiario($Fecha, "FACTURA", $idFactura, "", $DatosCliente["Num_Identificacion"], $CuentaDestino, $NombreCuentaDestino, "Cruce de Anticipos", "CR", $ValorAnticipo, "Cruce Anticipos Relaizados por Clientes", $DatosFactura["CentroCosto"], $DatosFactura["idSucursal"], "");
        $this->IngreseMovimientoLibroDiario($Fecha, "FACTURA", $idFactura, "", $DatosCliente["Num_Identificacion"], $ParametrosAnticipos["CuentaPUC"], $ParametrosAnticipos["NombreCuenta"], "Cruce de Anticipos", "DB", $ValorAnticipo, "Cruce Anticipos Relaizados por Clientes", $DatosFactura["CentroCosto"], $DatosFactura["idSucursal"], "");
        
        $NuevoSaldo=$DatosFactura["SaldoFact"]-$ValorAnticipo;
        $AbonosTotales=$DatosFactura["Total"]-$NuevoSaldo;
        $this->ActualizaRegistro("facturas", "SaldoFact", $NuevoSaldo, "idFacturas", $idFactura);
        $this->ActualizaRegistro("cartera", "Saldo", $NuevoSaldo, "Facturas_idFacturas", $idFactura);
        $this->ActualizaRegistro("cartera", "TotalAbonos", $AbonosTotales, "Facturas_idFacturas", $idFactura);
        
    }
    /**
     * Crear una preventa
     * @param type $idUser
     * @param type $TextPreventa
     * @return type
     */
    public function CrearPreventaPOS($idUser,$TextPreventa) {
        
        $Datos["Nombre"]=$TextPreventa;
        $Datos["Usuario_idUsuario"]=$idUser;
        $Datos["Clientes_idClientes"]=1;
        $sql=$this->getSQLInsert("vestasactivas", $Datos);
        $this->Query($sql);
        $idPreventa=$this->ObtenerMAX("vestasactivas", "idVestasActivas", "Usuario_idUsuario", $idUser);
        return($idPreventa);
    }
    /**
     * Obtiene el id de un producto revisando primero los codigos de barras
     * @param type $CodigoBarras
     * @return type
     */
    public function ObtenerIdProducto($CodigoBarras) {
        $sql="SELECT ProductosVenta_idProductosVenta as idProductosVenta FROM prod_codbarras WHERE CodigoBarras='$CodigoBarras' AND TablaOrigen='productosventa'";
        $consulta=$this->Query($sql);
        $DatosProducto=$this->FetchAssoc($consulta);
        if($DatosProducto["idProductosVenta"]==''){
            $idProducto=ltrim($CodigoBarras, "0");
            $sql="SELECT idProductosVenta FROM productosventa WHERE idProductosVenta='$CodigoBarras'";
            $consulta=$this->Query($sql);
            $DatosProducto=$this->FetchArray($consulta);
        }
        $idProducto=$DatosProducto["idProductosVenta"];
        return($idProducto);
    }
    
    public function POS_AgregaItemPreventa($idProducto,$TablaItem,$Cantidad,$idPreventa,$ValorAcordado=0,$idSistema=0) {
        
        $DatosProductoGeneral=$this->DevuelveValores($TablaItem, "idProductosVenta", $idProducto);
        
        $CostoUnitario=0;
        $PrecioMayor=0;
        
        if(isset($DatosProductoGeneral["CostoUnitario"])){
            $CostoUnitario=$DatosProductoGeneral["CostoUnitario"];
        }
        
        if(isset($DatosProductoGeneral["PrecioMayorista"])){
            $PrecioMayor=$DatosProductoGeneral["PrecioMayorista"];
        }
        
        $DatosImpuestosAdicionales=$this->DevuelveValores("productos_impuestos_adicionales", "idProducto", $idProducto);
	
        $DatosDepartamento=$this->DevuelveValores("prod_departamentos", "idDepartamentos", $DatosProductoGeneral["Departamento"]);
        $DatosTablaItem=$this->DevuelveValores("tablas_ventas", "NombreTabla", $TablaItem);
        $TipoItem=$DatosDepartamento["TipoItem"];
        $consulta=$this->ConsultarTabla("preventa", "WHERE TablaItem='$TablaItem' AND ProductosVenta_idProductosVenta='$idProducto' AND VestasActivas_idVestasActivas='$idPreventa' AND idSistema='$idSistema' ORDER BY idPrecotizacion DESC");
        $DatosProduto=$this->FetchArray($consulta);
        
        if($DatosProduto["Cantidad"]>0){ //Si ya hay un producto agregado
            if($DatosProductoGeneral["IVA"]=="E"){
                $DatosProductoGeneral["IVA"]=0;
            }
            
            $Cantidad=$DatosProduto["Cantidad"]+$Cantidad;
            $Subtotal=round($DatosProduto["ValorAcordado"]*$Cantidad);
            $Impuestos=round($DatosProductoGeneral["IVA"]*$Subtotal+$DatosImpuestosAdicionales["ValorImpuesto"]);
            $TotalVenta=$Subtotal+$Impuestos;
            $sql="UPDATE preventa SET Subtotal='$Subtotal', Impuestos='$Impuestos', TotalVenta='$TotalVenta', Cantidad='$Cantidad' WHERE idPrecotizacion='$DatosProduto[idPrecotizacion]'";
            
            $this->Query($sql);
        }else{
            $reg=$this->Query("select * from fechas_descuentos where (Departamento = '$DatosProductoGeneral[Departamento]' OR Departamento ='0') AND (Sub1 = '$DatosProductoGeneral[Sub1]' OR Sub1 ='0') AND (Sub2 = '$DatosProductoGeneral[Sub2]' OR Sub2 ='0')  ORDER BY idFechaDescuentos DESC LIMIT 1");
            $reg=$this->FetchArray($reg);
            $Porcentaje=$reg["Porcentaje"];
            $Departamento=$reg["Departamento"];
            $FechaDescuento=$reg["Fecha"];
            if($DatosProductoGeneral["IVA"]=="E"){
                $DatosProductoGeneral["IVA"]=0;
            }
            $impuesto=$DatosProductoGeneral["IVA"];
            $PorcentajeIVA=$impuesto;
            $DatosImpuestosAdicionales["ValorImpuesto"];
            $impuesto=$impuesto+1;
            if($ValorAcordado>0){
                $DatosProductoGeneral["PrecioVenta"]=$ValorAcordado;
            }
            
            // buscar si tiene habilitado precio de descuento 
            
            $DatosFechasPreciosEspeciales= $this->DevuelveValores("ventas_fechas_especiales", "ID", 1);
            
            if($DatosFechasPreciosEspeciales["Habilitado"]==1){
                
                $fecha_inicio=$DatosFechasPreciosEspeciales["FechaInicial"];
                $fecha_fin=$DatosFechasPreciosEspeciales["FechaFinal"];
                $fecha_inicio = strtotime($fecha_inicio);
                $fecha_fin = strtotime($fecha_fin);
                $fecha = strtotime(date("Y-m-d"));
                if(($fecha >= $fecha_inicio) and ($fecha <= $fecha_fin)) {
                    $DatosPreciosEspeciales=$this->DevuelveValores("ventas_fechas_especiales_precios", "Referencia", $DatosProductoGeneral["Referencia"]);
                    
                    if($DatosPreciosEspeciales["PrecioVenta"]<>''){
                        $PrecioEspecial=$DatosPreciosEspeciales["PrecioVenta"];
                        $DatosProductoGeneral["PrecioVenta"]=$PrecioEspecial;
                    }
                }
                
              
            }
            
           
            if($DatosImpuestosAdicionales["Incluido"]=='SI'){
               $DatosProductoGeneral["PrecioVenta"]=$DatosProductoGeneral["PrecioVenta"] - $DatosImpuestosAdicionales["ValorImpuesto"];
            }
            
            if($DatosTablaItem["IVAIncluido"]=="SI"){
                
                $ValorUnitario=round($DatosProductoGeneral["PrecioVenta"]/$impuesto,2);
                
            }else{
                $ValorUnitario=$DatosProductoGeneral["PrecioVenta"];
                
            }
            
            if($Porcentaje>0 and $FechaDescuento==$fecha){

                    $Porcentaje=(100-$Porcentaje)/100;
                    $ValorUnitario=round($ValorUnitario*$Porcentaje,2);

            }
            
            
            
            $Subtotal=$ValorUnitario*$Cantidad;
            //Para colocarle el valor totoal al producto especial
            if(isset($DatosProductoGeneral["Especial"])){
                if($DatosProductoGeneral["Especial"]=="SI"){
                    $Subtotal=$ValorUnitario;
                }
            }
            
            $impuesto=round(($impuesto-1)*$Subtotal,2) + round(($DatosImpuestosAdicionales["ValorImpuesto"]*$Cantidad),2);
            
            
            $Total=$Subtotal+$impuesto;
            
            $Insert["Fecha"]=date("Y-m-d");
            $Insert["Cantidad"]=$Cantidad;
            $Insert["VestasActivas_idVestasActivas"]=$idPreventa;
            $Insert["ProductosVenta_idProductosVenta"]=$idProducto;
            $Insert["Nombre"]=$DatosProductoGeneral["Nombre"];
            $Insert["Referencia"]=$DatosProductoGeneral["Referencia"];
            $Insert["ValorUnitario"]=$ValorUnitario;
            $Insert["ValorAcordado"]=$ValorUnitario;
            $Insert["Subtotal"]=$Subtotal;
            $Insert["Impuestos"]=$impuesto;
            $Insert["TotalVenta"]=$Total;            
            $Insert["TablaItem"]=$TablaItem;
            $Insert["TipoItem"]=$TipoItem;
            $Insert["CostoUnitario"]=$CostoUnitario;
            $Insert["PrecioMayorista"]=$PrecioMayor;
            $Insert["PorcentajeIVA"]=$PorcentajeIVA;
            $Insert["idSistema"]=$idSistema;
            
            
            $sql=$this->getSQLInsert("preventa", $Insert);
            
            $this->Query($sql);	
	
        }
        
    }
    
    public function POS_EditarPrecio($idItem,$ValorAcordado,$Mayorista=0) {
        $DatosPreventa= $this->DevuelveValores("preventa", "idPrecotizacion", $idItem);
        $Cantidad=$DatosPreventa["Cantidad"];
        $idProducto=$DatosPreventa["ProductosVenta_idProductosVenta"];
        $Tabla=$DatosPreventa["TablaItem"];
        $DatosProductos=$this->DevuelveValores($Tabla,"idProductosVenta",$idProducto);
        $Descuento=($DatosProductos["PrecioVenta"]-$ValorAcordado)*$Cantidad;
        if($Mayorista==1){
            $ValorAcordado=$DatosProductos["PrecioMayorista"];
        }
        $DatosTablaItem=$this->DevuelveValores("tablas_ventas", "NombreTabla", $Tabla);
        if($DatosTablaItem["IVAIncluido"]=="SI"){

            $ValorAcordado=round($ValorAcordado/($DatosProductos["IVA"]+1),2);

        }
        $Subtotal=round($ValorAcordado*$Cantidad,2);
        $IVA=round($Subtotal*$DatosProductos["IVA"],2);
        $Total=$Subtotal+$IVA;
        $filtro="idPrecotizacion";

        $this->ActualizaRegistro("preventa","Subtotal", $Subtotal, $filtro, $idItem);
        $this->ActualizaRegistro("preventa","Impuestos", $IVA, $filtro, $idItem);
        $this->ActualizaRegistro("preventa","TotalVenta", $Total, $filtro, $idItem);
        $this->ActualizaRegistro("preventa","ValorAcordado", $ValorAcordado, $filtro, $idItem);
        $this->ActualizaRegistro("preventa","Descuento", $Descuento, $filtro, $idItem);

    }
    
    //Agregue un sistema a una preventa
    public function POS_AgregueSistemaPreventa($idPreventa,$idSistema,$Cantidad,$Vector) {
        $consulta=$this->ConsultarTabla("sistemas_relaciones", "WHERE idSistema='$idSistema'");
        while($ItemsSistema=$this->FetchArray($consulta)){
            
            $CantidadTotal=$Cantidad*$ItemsSistema["Cantidad"];
            $DatosProducto=$this->DevuelveValores($ItemsSistema["TablaOrigen"], "Referencia", $ItemsSistema["Referencia"]);
            
            $this->POS_AgregaItemPreventa($DatosProducto["idProductosVenta"], $ItemsSistema["TablaOrigen"], $CantidadTotal, $idPreventa,$ItemsSistema["ValorUnitario"],$idSistema);
            
        }
    }
    
    public function InsertarFacturaLibroDiarioV2($idFactura,$CuentaDestino,$idUser) {
        $sql= $this->getSqlFacturaLibroDiario($idFactura, $CuentaDestino, $idUser);
        $this->Query($sql);
    }
    
    public function getSqlFacturaLibroDiario($idFactura,$CuentaDestino,$idUser) {
        $sqlFactura="INSERT INTO `librodiario` ( `Fecha`, `Tipo_Documento_Intero`, `Num_Documento_Interno`, `Num_Documento_Externo`, `Tercero_Tipo_Documento`, `Tercero_Identificacion`, `Tercero_DV`, `Tercero_Primer_Apellido`, `Tercero_Segundo_Apellido`, `Tercero_Primer_Nombre`, `Tercero_Otros_Nombres`, `Tercero_Razon_Social`, `Tercero_Direccion`, `Tercero_Cod_Dpto`, `Tercero_Cod_Mcipio`, `Tercero_Pais_Domicilio`, `Concepto`, `CuentaPUC`, `NombreCuenta`, `Detalle`, `Debito`, `Credito`, `Neto`, `Mayor`, `Esp`, `idCentroCosto`, `idEmpresa`, `idSucursal`, `Estado`, `idUsuario`) VALUES ";
        
        $DatosFactura= $this->DevuelveValores("facturas", "idFacturas", $idFactura);
        $DatosTercero= $this->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
        $Fecha=$DatosFactura["Fecha"];
        $TerceroTipoDocumento=$DatosTercero["Tipo_Documento"];
        $NIT=$DatosTercero["Tipo_Documento"];
        $DV=$DatosTercero["DV"];
        $TerceroNombre1=$DatosTercero["Primer_Apellido"];
        $TerceroNombre2=$DatosTercero["Segundo_Apellido"];
        $TerceroNombre3=$DatosTercero["Primer_Nombre"];
        $TerceroNombre4=$DatosTercero["Otros_Nombres"];
        $RazonSocial=$DatosTercero["RazonSocial"];
        $Direccion=$DatosTercero["Direccion"];
        $CodDepartamento=$DatosTercero["Cod_Dpto"];
        $CodMunicipo=$DatosTercero["Cod_Mcipio"];
        $codPais=$DatosTercero["Pais_Domicilio"];
        $idCentroCostos=$DatosFactura["CentroCosto"];
        $idEmpresa=$DatosFactura["EmpresaPro_idEmpresaPro"];
        $idSucursal=$DatosFactura["idSucursal"];
        
        $sql="SELECT  sum(SubtotalItem) as SubtotalItem,sum(TotalItem) as TotalItem,sum(IVAItem) as IVAItem "
                    . "FROM facturas_items WHERE idFactura='$idFactura'";
        $Consulta=$this->Query($sql);
        $DatosTotales=$this->FetchAssoc($Consulta);
        
        $Subtotal=round($DatosTotales["SubtotalItem"],2);
        $Impuestos=round($DatosTotales["IVAItem"],2);
        $Total=round($Subtotal+$Impuestos,2);
        $AjusteFactura=$Total-$Impuestos-$Subtotal;
        
        //Registramos la partida inicial
        
        if($DatosFactura["FormaPago"]=="Contado"){
                    
            $TotalOtrasFormasPago=$DatosFactura["Tarjetas"]+$DatosFactura["Cheques"]+$DatosFactura["Otros"];
            $DiferenciaFormasPago=$Total-$TotalOtrasFormasPago;
            if($DiferenciaFormasPago<>0){
                $CuentaPUC=$CuentaDestino;
                $DatosCuenta= $this->DevuelveValores("subcuentas", "PUC", $CuentaDestino);
                $NombreCuenta=$DatosCuenta["Nombre"];
                if($Total>0){
                    $Debito=$DiferenciaFormasPago;
                    $Credito=0;
                    $Neto=$DiferenciaFormasPago;
                }else{
                    $Debito=0;
                    $Credito=$DiferenciaFormasPago;
                    $Neto=$DiferenciaFormasPago*(-1);
                }
                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
            }
            //Si se paga con tarjetas
            if($DatosFactura["Tarjetas"]>0){ 
                $Parametros=$this->DevuelveValores("parametros_contables", "ID", 17);
                $CuentaPUC=$Parametros["CuentaPUC"]; //cuenta para bancos
                $NombreCuenta=$Parametros["NombreCuenta"];

                $Debito=$DatosFactura["Tarjetas"];
                $Credito=0;
                $Neto=$DatosFactura["Tarjetas"];
                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";

            }
            //Si hay pagos con cheques
            if($DatosFactura["Cheques"]>0){ 
                $Parametros=$this->DevuelveValores("parametros_contables", "ID", 18);
                $CuentaPUC=$Parametros["CuentaPUC"]; //cuenta para bancos
                $NombreCuenta=$Parametros["NombreCuenta"];

                $Debito=$DatosFactura["Cheques"];
                $Credito=0;
                $Neto=$DatosFactura["Cheques"];
                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";

            }

            //Si hay pagos con otros medios
            if($DatosFactura["Otros"]>0){ 
                $Parametros=$this->DevuelveValores("parametros_contables", "ID", 30);
                $CuentaPUC=$Parametros["CuentaPUC"]; //cuenta para bancos
                $NombreCuenta=$Parametros["NombreCuenta"];

                $Debito=$DatosFactura["Otros"];
                $Credito=0;
                $Neto=$DatosFactura["Otros"];
                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";

            }

        }

        if($DatosFactura["FormaPago"]<>"Contado" and $DatosFactura['FormaPago']<>"Separado"){

            $Parametros=$this->DevuelveValores("parametros_contables", "ID", 6); //Cuenta para clientes
            $CuentaPUC=$Parametros["CuentaPUC"];
            $NombreCuenta=$Parametros["NombreCuenta"];

            $Debito=$Total;
            $Credito=0;
            $Neto=$Total;
            $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";


        }

        if($DatosFactura["FormaPago"]=="Separado"){

            $Parametros=$this->DevuelveValores("parametros_contables", "ID", 31); //Cuenta por pagar a los separados
            $CuentaPUC=$Parametros["CuentaPUC"];
            $NombreCuenta=$Parametros["NombreCuenta"];

            $Debito=$Total;
            $Credito=0;
            $Neto=$Total;
            $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";


        }
        
        
        
        $sql="SELECT PorcentajeIVA,CuentaPUC, TipoItem, sum(SubtotalItem) as SubtotalItem,sum(TotalItem) as TotalItem,sum(IVAItem) as IVAItem ,sum(SubtotalCosto) as SubtotalCosto  "
                    . "FROM facturas_items WHERE idFactura='$idFactura' GROUP BY CuentaPUC, PorcentajeIVA";
        $Consulta2=$this->Query($sql);
        
        while($DatosItems=$this->FetchArray($Consulta2)){

            $Subtotal=round($DatosItems["SubtotalItem"],2);
            $Impuestos=round($DatosItems["IVAItem"],2);
            $Total=$Subtotal+$Impuestos;
            $TotalCostosM=$DatosItems["SubtotalCosto"];
            
            

            ///////////////////////Registramos ingresos

            $CuentaPUC=$DatosItems["CuentaPUC"]; 
            $DatosCuenta=$this->DevuelveValores("subcuentas","PUC",$CuentaPUC);
            $NombreCuenta=$DatosCuenta["Nombre"];
            $Debito=0;
            $Credito=$Subtotal;
            $Neto=$Subtotal*(-1);
            
            $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
         
            ///////////////////////Registramos IVA Generado si aplica

            if($Impuestos<>0){
                $TipoIVA=str_replace("%", "", $DatosItems["PorcentajeIVA"]);
                $TipoIVA=str_pad($TipoIVA, 2, "0", STR_PAD_LEFT);
                $TipoIVA="0.".$TipoIVA;
                $DatosIVA=$this->DevuelveValores("porcentajes_iva", "Valor", $TipoIVA);
                $CuentaPUC=$DatosIVA["CuentaPUCIVAGenerado"]; //   IVA Generado
                $NombreCuenta=$DatosIVA["NombreCuenta"];
                $Debito=0;
                $Credito=$Impuestos;
                $Neto=$Impuestos*(-1);

                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
         
            }


            ///////////////////////Ajustamos el inventario

            if($DatosItems["TipoItem"]=="PR"){
                $Parametros=$this->DevuelveValores("parametros_contables", "ID", 2);
                $CuentaPUC=$Parametros["CuentaPUC"]; //6135   costo de mercancia vendida

                $NombreCuenta=$Parametros["NombreCuenta"];

                $Debito=$TotalCostosM;
                $Credito=0;
                $Neto=$TotalCostosM;

                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
         
                ///////////////////////Ajustamos el inventario
                $Parametros=$this->DevuelveValores("parametros_contables", "ID", 4);
                $CuentaPUC=$Parametros["CuentaPUC"]; //1435   Mercancias no fabricadas por la empresa

                //$DatosCuenta=$this->DevuelveValores('cuentas',"idPUC",$CuentaPUC);
                $NombreCuenta=$Parametros["NombreCuenta"];

                $Debito=0;
                $Credito=$TotalCostosM;
                $Neto=$TotalCostosM*(-1);

                $sqlFactura.="('$Fecha','FACTURA','$idFactura','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Ventas','$CuentaPUC','$NombreCuenta',	'Ventas',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
         

            }

        }
        $sqlFactura = substr($sqlFactura, 0, -1);       
        return($sqlFactura);
    }
    
    public function IngresoPlataformasPago($idPlataforma,$Fecha,$Hora,$Tercero,$Valor,$idComprobanteIngreso,$idUser) {
        
        $Datos["Fecha"]=$Fecha;
        $Datos["Hora"]=$Hora;
        $Datos["Tercero"]=$Tercero;
        $Datos["Valor"]=$Valor;
        $Datos["idComprobanteIngreso"]=$idComprobanteIngreso;
        $Datos["idUser"]=$idUser;
        $Datos["idPlataformaPago"]=$idPlataforma;
        
        $sql=$this->getSQLInsert("comercial_plataformas_pago_ingresos", $Datos);
        $this->Query($sql);
        
    }
    
    public function PlataformasPagoVentas($idPlataforma,$Fecha,$Hora,$Tercero,$idFactura,$Valor,$idUser) {
        
        $Datos["Fecha"]=$Fecha;
        $Datos["Hora"]=$Hora;
        $Datos["Tercero"]=$Tercero;
        $Datos["Valor"]=$Valor;
        $Datos["idFactura"]=$idFactura;
        $Datos["idUser"]=$idUser;
        $Datos["idPlataformaPago"]=$idPlataforma;
        
        $sql=$this->getSQLInsert("comercial_plataformas_pago_ventas", $Datos);
        $this->Query($sql);
        
    }
    
    public function CierreTurnoPos($idUser,$idCaja,$VectorCierre) {
        
        $fecha=date("Y-m-d");
        $Hora=date("H:i:s");
       
        //Calculo las ventas
        
        $sql="SELECT SUM(Total) as Total, SUM(Efectivo) as Efectivo, SUM(Devuelve) as Devuelve, SUM(Cheques) as Cheques, SUM(Otros) as Otros, SUM(Tarjetas) as Tarjetas FROM facturas "
                . "WHERE Usuarios_idUsuarios='$idUser' AND CerradoDiario = '' AND FormaPago='Contado'";
        
        $Consulta=$this->Query($sql);
        $DatosSumatorias=$this->FetchArray($Consulta);
        
        $TotalVentasContado=$DatosSumatorias["Total"];
        $TotalEfectivo=$DatosSumatorias["Efectivo"];
        $TotalDevueltas=$DatosSumatorias["Devuelve"];
        $TotalCheques=$DatosSumatorias["Cheques"];
        $TotalOtros=$DatosSumatorias["Otros"];
        $TotalTarjetas=$DatosSumatorias["Tarjetas"];
        
        
        //Calculo las ventas a credito
        //
        $sql="SELECT SUM(Total) as Total FROM facturas "
                . "WHERE Usuarios_idUsuarios='$idUser' AND CerradoDiario = '' AND FormaPago<>'ANULADA' AND FormaPago<>'Contado' AND FormaPago<>'SisteCredito' AND FormaPago<>'Separado'";
        
        $Consulta=$this->Query($sql);
        $DatosSumatorias=$this->FetchArray($Consulta);
        
        $TotalVentasCredito=$DatosSumatorias["Total"]; 
        
        
        //Calculo las ventas de SisteCredito
        //
        $sql="SELECT SUM(Total) as Total FROM facturas "
                . "WHERE Usuarios_idUsuarios='$idUser' AND CerradoDiario = '' AND FormaPago = 'SisteCredito'";
        
        $Consulta=$this->Query($sql);
        $DatosSumatorias=$this->FetchArray($Consulta);
        
        $TotalVentasSisteCredito=$DatosSumatorias["Total"]; 
        
        //Calculo los retiros de separados
        //
        $sql="SELECT SUM(Total) as Total FROM facturas "
                . "WHERE Usuarios_idUsuarios='$idUser' AND CerradoDiario = '' AND FormaPago = 'Separado'";
        
        $Consulta=$this->Query($sql);
        $DatosSumatorias=$this->FetchArray($Consulta);
        
        $TotalRetiroSeparados=$DatosSumatorias["Total"]; 
        
        //Calculo las devoluciones
        
        $sql="SELECT SUM(TotalItem) as TotalDevoluciones FROM facturas_items "
                . "WHERE idUsuarios='$idUser' AND idCierre = '' AND Cantidad < 0";
        
        $Consulta=$this->Query($sql);
        $DatosDevoluciones=$this->FetchArray($Consulta);
        
        $TotalDevoluciones=$DatosDevoluciones["TotalDevoluciones"];
        
        //Calculo los egresos
        
        $sql="SELECT SUM(Valor) as Valor, SUM(Retenciones) as Retenciones FROM egresos "
                . "WHERE Usuario_idUsuario='$idUser' AND CerradoDiario = '' AND PagoProg='Contado'";
        
        $Consulta=$this->Query($sql);
        $DatosEgresos=$this->FetchArray($Consulta);
        
        $TotalEgresos=$DatosEgresos["Valor"];
        $TotalRetenciones=$DatosEgresos["Retenciones"];
        $TotalEgresos=$TotalEgresos-$TotalRetenciones;
        
        
        //Calculo los abonos de separados
        
        $TotalAbonos=$this->Sume("separados_abonos", "Valor", "WHERE idUsuarios='$idUser' AND idCierre=''");
        //Calculo los abonos de Creditos
        
        $TotalAbonosCreditos=$this->Sume("facturas_abonos", "Valor", "WHERE Usuarios_idUsuarios='$idUser' AND idCierre='' AND FormaPago <> 'SisteCredito'");
        $TotalAbonosSisteCredito=$this->Sume("comercial_plataformas_pago_ingresos", "Valor", "WHERE idUser='$idUser' AND idCierre='0' AND idPlataformaPago=1");
        //$TotalAbonosKupy=$this->Sume("comercial_plataformas_pago_ingresos", "Valor", "WHERE idUser='$idUser' AND idCierre='0' AND idPlataformaPago=2");
        //Ingreso datos en tabla cierres
        
        $tab="cajas_aperturas_cierres";
        $NumRegistros=23;
        $Columnas[0]="ID";                  $Valores[0]="";
        $Columnas[1]="Fecha";               $Valores[1]=$fecha;
        $Columnas[2]="Hora";                $Valores[2]=$Hora;
        $Columnas[3]="Movimiento";           $Valores[3]="Cierre";
        $Columnas[4]="Usuario";               $Valores[4]=$idUser;
        $Columnas[5]="idCaja";            $Valores[5]=$idCaja;
        $Columnas[6]="TotalVentas";           $Valores[6]=$TotalVentasContado+$TotalVentasCredito+$TotalVentasSisteCredito-$TotalDevoluciones;
        $Columnas[7]="TotalVentasContado";    $Valores[7]=$TotalVentasContado;
        $Columnas[8]="TotalVentasCredito";    $Valores[8]=$TotalVentasCredito;
        $Columnas[9]="TotalAbonos";           $Valores[9]=$TotalAbonos;
        $Columnas[10]="TotalDevoluciones";    $Valores[10]=$TotalDevoluciones;
        $Columnas[11]="TotalEntrega";         $Valores[11]=$TotalVentasContado+$TotalAbonos+$TotalAbonosCreditos+$TotalAbonosSisteCredito-$TotalEgresos;
        $Columnas[12]="TotalEfectivo";        $Valores[12]=$TotalVentasContado-$TotalEgresos+$TotalAbonos+$TotalAbonosCreditos+$TotalAbonosSisteCredito-$TotalTarjetas-$TotalCheques-$TotalOtros;
        $Columnas[13]="TotalTarjetas";        $Valores[13]=$TotalTarjetas;
        $Columnas[14]="TotalCheques";         $Valores[14]=$TotalCheques;
        $Columnas[15]="TotalOtros";           $Valores[15]=$TotalOtros;
        $Columnas[16]="TotalEgresos";         $Valores[16]=$TotalEgresos;
        $Columnas[17]="Efectivo";             $Valores[17]=$TotalEfectivo;
        $Columnas[18]="Devueltas";            $Valores[18]=$TotalDevueltas;
        $Columnas[19]="AbonosCreditos";       $Valores[19]=$TotalAbonosCreditos;
        $Columnas[20]="AbonosSisteCredito";           $Valores[20]=$TotalAbonosSisteCredito;
        $Columnas[21]="TotalVentasSisteCredito";      $Valores[21]=$TotalVentasSisteCredito;
        $Columnas[22]="TotalRetiroSeparados";      $Valores[22]=$TotalRetiroSeparados;
        $this->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        $idCierre=$this->ObtenerMAX($tab, "ID", 1, "");
        
        //UPDATES
        
        $this->update("facturas", "CerradoDiario", $idCierre, "WHERE CerradoDiario='' AND Usuarios_idUsuarios='$idUser'");
        $this->update("egresos", "CerradoDiario", $idCierre, "WHERE CerradoDiario='' AND Usuario_idUsuario='$idUser'");
        $this->update("separados_abonos", "idCierre", $idCierre, "WHERE idCierre='' AND idUsuarios='$idUser'");
        $this->update("facturas_abonos", "idCierre", $idCierre, "WHERE idCierre='' AND Usuarios_idUsuarios='$idUser'");
        $this->update("facturas_items", "idCierre", $idCierre, "WHERE idCierre='' AND idUsuarios='$idUser'");
        $this->update("facturas_intereses_sistecredito", "idCierre", $idCierre, "WHERE idCierre='' AND idUsuario='$idUser'"); 
        $this->update("comprobantes_ingreso", "idCierre", $idCierre, "WHERE idCierre='' AND Usuarios_idUsuarios='$idUser'"); 
        $this->update("comercial_plataformas_pago_ingresos", "idCierre", $idCierre, "WHERE idCierre='0' AND idUser='$idUser'"); 
        $this->update("pos_registro_descuentos", "idCierre", $idCierre, "WHERE idCierre='0' AND idUsuario='$idUser'");  
        return ($idCierre);
        
    }
    
    
    public function pos_InsertarItemsPreventaAItemsFactura($Datos,$idUser){
        
        $idPreventa=$Datos["idPreventa"];
        $NumFactura=$Datos["ID"];
        $FechaFactura=$Datos["FechaFactura"];
                
        $sql="SELECT * FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa'";
        $Consulta=$this->Query($sql);
        $TotalSubtotal=0;
        $TotalIVA=0;
        $GranTotal=0;
        $TotalCostos=0;
        $DatosOtrosImpuestos["ValorImpuesto"]=0;
        $DatosOtrosImpuestos["ID"]=0;
        $Entra=0;
        while($DatosCotizacion=  $this->FetchArray($Consulta)){
            $TablaItem=$DatosCotizacion["TablaItem"];
            $idTabla='idProductosVenta';
            
            if($DatosCotizacion["idSistema"]>0){
                $idSistema=$DatosCotizacion["idSistema"];
                
                $consulta=$this->ConsultarTabla("facturas_items", " WHERE idFactura='$NumFactura' AND TablaItems='sistemas' AND Referencia='$idSistema'");
                $DatosItem=$this->FetchArray($consulta);
                if($DatosItem["ID"]==''){
                    $Entra=1;
                    $DatosSistema=$this->DevuelveValores("sistemas", "ID", $idSistema);
                    $DatosCotizacionLineaSistema["TablaItem"]='sistemas';
                    $DatosCotizacionLineaSistema["ValorAcordado"]=0;
                    $DatosCotizacionLineaSistema["CostoUnitario"]=0;
                    $DatosCotizacionLineaSistema["TipoItem"]='MO';
                    $DatosCotizacionLineaSistema["Cantidad"]=0;
                    $DatosProductoLineaSistema["Referencia"]=$idSistema;
                    $DatosProductoLineaSistema["Nombre"]=$DatosSistema["Nombre"];
                    $DatosProductoLineaSistema["Departamento"]=0;
                    $DatosProductoLineaSistema["Sub1"]=0;
                    $DatosProductoLineaSistema["Sub2"]=0;
                    $DatosProductoLineaSistema["Sub3"]=0;
                    $DatosProductoLineaSistema["Sub4"]=0;
                    $DatosProductoLineaSistema["Sub5"]=0;
                    $DatosProductoLineaSistema["CuentaPUC"]='';
                    
                    $this->ItemFacturaVenta($NumFactura, $DatosCotizacionLineaSistema, $DatosProductoLineaSistema, 0, 0, 0, 0, 0, '', $DatosOtrosImpuestos, "");
                    
                }
                 
                //$idTabla='ID';
                
            }else{
                if($Entra==1){
                    $Entra=0;
                    
                    $DatosCotizacionLineaSistema["TablaItem"]='ln';
                    $DatosCotizacionLineaSistema["ValorAcordado"]=0;
                    $DatosCotizacionLineaSistema["CostoUnitario"]=0;
                    $DatosCotizacionLineaSistema["TipoItem"]='MO';
                    $DatosCotizacionLineaSistema["Cantidad"]=0;
                    $DatosProductoLineaSistema["Referencia"]="";
                    $DatosProductoLineaSistema["Nombre"]="";
                    $DatosProductoLineaSistema["Departamento"]=0;
                    $DatosProductoLineaSistema["Sub1"]=0;
                    $DatosProductoLineaSistema["Sub2"]=0;
                    $DatosProductoLineaSistema["Sub3"]=0;
                    $DatosProductoLineaSistema["Sub4"]=0;
                    $DatosProductoLineaSistema["Sub5"]=0;
                    $DatosProductoLineaSistema["CuentaPUC"]='';
                    
                    $this->ItemFacturaVenta($NumFactura, $DatosCotizacionLineaSistema, $DatosProductoLineaSistema, 0, 0, 0, 0, 0, '', $DatosOtrosImpuestos, "");
                    
                }
            }
            $DatosProducto=$this->DevuelveValores($DatosCotizacion["TablaItem"], $idTabla, $DatosCotizacion["ProductosVenta_idProductosVenta"]);
            //Reviso si hay impuestos adicionales en los productos
            
            if($DatosCotizacion["TablaItem"]=='productosventa'){
                $DatosOtrosImpuestos=$this->DevuelveValores("productos_impuestos_adicionales","idProducto",$DatosCotizacion["ProductosVenta_idProductosVenta"]);
                
            }
            ////Empiezo a insertar en la tabla items facturas
            ///
            ///
            $SubtotalItem=$DatosCotizacion['ValorAcordado']*$DatosCotizacion['Cantidad'];
            if(isset($DatosProducto["Especial"])){
                if($DatosProducto["Especial"]=='SI'){
                    $SubtotalItem=$DatosCotizacion['ValorAcordado'];
                }
            }
            $TotalSubtotal=$TotalSubtotal+$SubtotalItem; //se realiza la sumatoria del subtotal
            
            $IVAItem=($SubtotalItem*$DatosProducto["IVA"]);
           
            $TotalIVA=$TotalIVA+$IVAItem; //se realiza la sumatoria del iva
            
            $TotalItem=$SubtotalItem+$IVAItem;
            $GranTotal=$GranTotal+$TotalItem;//se realiza la sumatoria del total
            
            $SubtotalCosto=$DatosCotizacion['Cantidad']*$DatosCotizacion["CostoUnitario"];
            $TotalCostos=$TotalCostos+$SubtotalCosto;//se realiza la sumatoria de los costos
            if($DatosProducto["IVA"]<>"E"){
                $PorcentajeIVA=($DatosProducto["IVA"]*100)."%";
            }else{
                $PorcentajeIVA="Exc";
            }
            $this->ItemFacturaVenta($NumFactura, $DatosCotizacion, $DatosProducto, $SubtotalItem, $IVAItem, $TotalItem, $PorcentajeIVA, $SubtotalCosto, $FechaFactura, $DatosOtrosImpuestos, "");
                         
        }
        /*
        $ID=$Datos["ID"]; 
        $TotalSubtotal=round($TotalSubtotal,2);
        $TotalIVA=round($TotalIVA,2);
        $GranTotal=round($GranTotal,2);
        $TotalCostos=round($TotalCostos,2);
        $sql="UPDATE facturas SET Subtotal='$TotalSubtotal', IVA='$TotalIVA', Total='$GranTotal', "
                . "SaldoFact='$GranTotal', TotalCostos='$TotalCostos' WHERE idFacturas='$ID'";
        $this->Query($sql);
        
        
         * 
         */
    } 
    
    /**
     * Fin Clase
     */
}
