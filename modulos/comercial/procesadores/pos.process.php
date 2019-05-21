<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$fecha=date("Y-m-d");

include_once("../clases/Facturacion.class.php");
include_once("../../../modelo/PrintPos.php");
include_once("../../../general/clases/contabilidad.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Facturacion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear una preventa
            //Verifico primero que no tenga mas de 3 preventas creadas
            $sql="SELECT COUNT(*) as Total FROM vestasactivas WHERE Usuario_idUsuario='$idUser'";
            $Consulta=$obCon->Query($sql);
            $CantidadPreventas=$obCon->FetchAssoc($Consulta);
            if($CantidadPreventas["Total"]>=3){
                print("E1;");//Preventas Máximas permitidas
                exit();
            }
            $DatosUsuario=$obCon->ValorActual("usuarios", "Nombre,Apellido", " idUsuarios='$idUser'");
            $TextoPreventa="Venta por: ".$DatosUsuario["Nombre"]." ".$DatosUsuario["Apellido"];
            $idPreventa=$obCon->CrearPreventaPOS($idUser,$TextoPreventa);
            print("OK;$idPreventa;$TextoPreventa");            
            
        break; 
        
        
        case 2://Agregar un producto para la venta
            
            $idPreventa=$obCon->normalizar($_REQUEST["idPreventa"]); 
            $CmbListado=$obCon->normalizar($_REQUEST["CmbListado"]); 
            $Codigo=$obCon->normalizar($_REQUEST["Codigo"]); 
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]); 
            
            if($CmbListado==1 or $CmbListado==5){
                $TablaItem="productosventa";
                $idProducto=$obCon->ObtenerIdProducto($Codigo);
                if($idProducto==''){
                    print("E1;El producto no existe en la base de datos, por favor no lo entregue"); //El producto no existe en la base de datos
                    exit();
                }
                $DatosProducto=$obCon->ValorActual("productosventa", "PrecioVenta", "idProductosVenta='$idProducto'");
                $DatosImpuestosAdicionales=$obCon->DevuelveValores("productos_impuestos_adicionales", "idProducto", $idProducto);
                if($DatosProducto["PrecioVenta"]<=0 and $DatosImpuestosAdicionales["ValorImpuesto"]==''){
                    print("E1;Este Producto no tiene precio por favor no lo entregue"); //El producto no tiene precio de venta
                    exit();
                }
                $obCon->POS_AgregaItemPreventa($idProducto, $TablaItem, $Cantidad, $idPreventa);
                print("OK;Item $idProducto Agregado");
                exit();
            }
            if($CmbListado==2){
                $TablaItem="servicios";
                $DatosProducto=$obCon->ValorActual($TablaItem, "PrecioVenta", "idProductosVenta='$Codigo'");
                if($DatosProducto["PrecioVenta"]==''){
                    print("E1;El Servicio $Codigo no Existe");
                    exit();
                }
                
                if($DatosProducto["PrecioVenta"]<=0){
                    print("E1;El precio de venta del Servicio $Codigo es menor o igual a Cero");
                    exit();
                }
                $obCon->POS_AgregaItemPreventa($Codigo, $TablaItem, $Cantidad, $idPreventa);
                print("OK;Servicio $Codigo Agregado");
                exit();
            }
            if($CmbListado==3){
                $TablaItem="productosalquiler";
                $DatosProducto=$obCon->ValorActual($TablaItem, "PrecioVenta", "idProductosVenta='$Codigo'");
                if($DatosProducto["PrecioVenta"]==''){
                    print("E1;El Producto para alquilar $Codigo no Existe");
                    exit();
                }
                
                if($DatosProducto["PrecioVenta"]<=0){
                    print("E1;El precio de venta del Producto para alquilar $Codigo es menor o igual a Cero");
                    exit();
                }
                $obCon->POS_AgregaItemPreventa($Codigo, $TablaItem, $Cantidad, $idPreventa);
                print("OK;Producto para alquilar $Codigo Agregado");
                exit();
                
            }
            if($CmbListado==4){
                $TablaItem="sistemas";
                $DatosProducto=$obCon->ValorActual($TablaItem, "Nombre", "ID='$Codigo'");
                if($DatosProducto["Nombre"]==''){
                    print("E1;El sistema $Codigo no Existe");
                    exit();
                }
                
                $obCon->POS_AgregueSistemaPreventa($idPreventa,$Codigo, $Cantidad, "");
                print("OK;Producto para alquilar $Codigo Agregado");
                exit();
            }
            
                       
        break;//Fin caso 2
        
        
        case 3://Se elimina un item
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $Tabla="preventa";
            $DatosPreventa=$obCon->DevuelveValores("preventa", "idPrecotizacion", $idItem);
            $DatoGenerales=$obCon->DevuelveValores("configuracion_general", "ID", 4); //Determina si se debe pedir autoirzacion para retornar un item
            if($DatoGenerales["Valor"]>0){ //Si está en 1 pedirá autorización
                if($DatosPreventa["Autorizado"]=='0'){
                    print("E1;Esta acción requiere autorización");
                    exit();
                }
            }
            
            $obCon->BorraReg($Tabla, "idPrecotizacion", $idItem);
            print("OK;Item Eliminado");
        break;//Fin caso 3
        
        
        case 4://Edita una cantidad
             
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);            
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]);
            if($Cantidad==0){
                print("E1;No se puede editar la cantidad en cero");
                exit();
            }
            
            $DatosPreventa=$obCon->DevuelveValores("preventa", "idPrecotizacion", $idItem);
            $DatoGenerales=$obCon->DevuelveValores("configuracion_general", "ID", 3); //Determina si se debe pedir autoirzacion para retornar un item
            if($DatoGenerales["Valor"]>0){ //Si está en 1 pedirá autorización
                if($DatosPreventa["Autorizado"]=='0' and $Cantidad<0){
                    print("E1;Esta acción requiere autorización");
                    exit();
                }
            }
            $ValorAcordado=$DatosPreventa["ValorAcordado"];
            $idProducto=$DatosPreventa["ProductosVenta_idProductosVenta"];
            $Tabla=$DatosPreventa["TablaItem"];
            $Subtotal=$ValorAcordado*$Cantidad;
            $DatosProductos=$obCon->DevuelveValores($Tabla,"idProductosVenta",$idProducto);
            $DatosImpuestosAdicionales=$obCon->DevuelveValores("productos_impuestos_adicionales", "idProducto", $idProducto);
            $IVA=$Subtotal*$DatosProductos["IVA"]+($DatosImpuestosAdicionales["ValorImpuesto"]*$Cantidad);
            $SubtotalCosto=$DatosProductos["CostoUnitario"]*$Cantidad;
            $Total=$Subtotal+$IVA;
            $filtro="idPrecotizacion";

            $obCon->ActualizaRegistro("preventa","Subtotal", $Subtotal, $filtro, $idItem);
            $obCon->ActualizaRegistro("preventa","Impuestos", $IVA, $filtro, $idItem);
            $obCon->ActualizaRegistro("preventa","TotalVenta", $Total, $filtro, $idItem);
            $obCon->ActualizaRegistro("preventa","Cantidad", $Cantidad, $filtro, $idItem);
           
            $Mensaje="Item Editado";
            print("OK;$Mensaje");
            
        break;//Fin caso 4
        
        case 5:// Editar el precio de venta de un item
            $idItem=$obCon->normalizar($_REQUEST['idItem']);            
            $ValorAcordado=$obCon->normalizar($_REQUEST["PrecioVenta"]);
            $Mayorista=$obCon->normalizar($_REQUEST["Mayorista"]);
            if($ValorAcordado<=0){
                print("OK;El Valor del producto debe ser mayor a cero");
                exit();
            }
            
            $DatosPreventa=$obCon->DevuelveValores("preventa", "idPrecotizacion", $idItem);
            $DatoGenerales=$obCon->DevuelveValores("configuracion_general", "ID", 5); //Determina si se debe pedir autoirzacion para retornar un item
            if($DatoGenerales["Valor"]>0){ //Si está en 1 pedirá autorización
                if($DatosPreventa["Autorizado"]=='0'){
                    print("E1;Esta acción requiere autorización");
                    exit();
                }
            }
            
            $obCon->POS_EditarPrecio($idItem,$ValorAcordado, $Mayorista);
            
            print("OK;Valor Editado");
        break;//Fin caso 5
        
                
        case 6://Obtiene los datos de la bascula
            
            $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
            $idBascula=$DatosCaja["idBascula"];
            if($idBascula==''){
                print("E1;Usted no tiene una Caja Asignada");//No tiene caja asignada
                exit();
            }
            if($idBascula==0){
                print("E1;Usted No tiene una báscula asignada");//No tiene bascula asignada
                exit();
            }
            $DatosBascula=$obCon->DevuelveValores("registro_basculas", "idBascula", $idBascula);
            if($DatosBascula["Gramos"]==''){
                print("E1;No hay registros de la bascula");//No tiene bascula asignada
                exit();
            }
            
            print("OK;".$DatosBascula["Gramos"]); //Devuelve el dato registrado por la bascula en la base de datos
            
        break;//Fin caso 13
        
        case 7: //Guarda la factura
            $obPrint = new PrintPos($idUser);
            $obFactura = new Facturacion($idUser);
            
            $idPreventa=$obCon->normalizar($_REQUEST["idPreventa"]);       
            $Fecha=date("Y-m-d");
            $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
            $idCentroCostos=$DatosCaja["CentroCostos"];
            $CmbResolucion=$DatosCaja["idResolucionDian"];
            $CmbFormaPago=$obCon->normalizar($_REQUEST["CmbFormaPago"]);
            $CmbFrecuente="NO";
            $CmbCuentaIngresoFactura=$DatosCaja["CuentaPUCEfectivo"];
            $CmbColaboradores=$obCon->normalizar($_REQUEST["CmbColaboradores"]);
            $Observaciones=$obCon->normalizar($_REQUEST["TxtObservacionesFactura"]);
            $AnticiposCruzados=$obCon->normalizar($_REQUEST["AnticiposCruzados"]);
            $idCliente=$obCon->normalizar($_REQUEST["idCliente"]);
            $idEmpresa=$DatosCaja["idEmpresa"];
            $idSucursal=$DatosCaja["idSucursal"];
            $Devuelta=$obCon->normalizar($_REQUEST["Devuelta"]);
            $Efectivo=$obCon->normalizar($_REQUEST["Efectivo"]);
            $Cheques=$obCon->normalizar($_REQUEST["Cheque"]);
            $Otros=$obCon->normalizar($_REQUEST["Otros"]);
            $Tarjetas=$obCon->normalizar($_REQUEST["Tarjetas"]);
            $CmbPrint=$obCon->normalizar($_REQUEST["CmbPrint"]);
            $idCajero=$obCon->normalizar($_REQUEST["idCajero"]);
            if($idCajero<>$idUser){
                print("Se ha iniciado sesion por un usario diferente al cajero actual, por favor inicie sesion nuevamente");
                exit();
            }
            $FormaPagoFactura=$CmbFormaPago;
            if(is_numeric($FormaPagoFactura)){
                $FormaPagoFactura="Credito a $CmbFormaPago dias";
            }
            
            
            $Hora=date("H:i:s");
            
            $sql="SELECT SUM(ValorAcordado) AS Subtotal, SUM(Impuestos) AS IVA, SUM(TotalVenta) as Total,SUM(CostoUnitario*Cantidad) AS TotalCostos "
                    . "FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa'";
            $Consulta=$obCon->Query($sql);
            $DatosTotalesCotizacion=$obCon->FetchAssoc($Consulta);
            $Subtotal=round($DatosTotalesCotizacion["Subtotal"],2);
            $IVA=round($DatosTotalesCotizacion["IVA"],2);
            $Total=round($DatosTotalesCotizacion["Total"],2);
            $TotalCostos=$DatosTotalesCotizacion["TotalCostos"];
            $SaldoFactura=$Total;
            $Descuentos=0;
            $DatosCliente=$obCon->DevuelveValores("clientes", "idClientes", $idCliente);
            if($AnticiposCruzados>0){
                            
                $NIT=$DatosCliente["Num_Identificacion"];
                $ParametrosAnticipos=$obCon->DevuelveValores("parametros_contables", "ID", 20);//Aqui se encuentra la cuenta para los anticipos
                $CuentaAnticipos=$ParametrosAnticipos["CuentaPUC"];
                $sql="SELECT SUM(Debito) as Debito, SUM(Credito) AS Credito FROM librodiario WHERE CuentaPUC='$CuentaAnticipos' AND Tercero_Identificacion='$NIT'";
                $Consulta=$obCon->Query($sql);
                $DatosAnticipos=$obCon->FetchAssoc($Consulta);
                $SaldoAnticiposTercero=$DatosAnticipos["Credito"]-$DatosAnticipos["Debito"];
                
                if($SaldoAnticiposTercero<$AnticiposCruzados){
                    $Mensaje="El Cliente no cuenta con el anticipo registrado";
                    print("E3;$Mensaje");
                    exit();
                }
                
            }
            
            if($FormaPagoFactura<>'Contado' and $idCliente<=1){
                print("E5;No se puede crear una factura tipo $FormaPagoFactura asignada al cliente $idCliente");
                    exit();
            }
            
            $idFactura=$obFactura->idFactura();
                        
            $NumFactura=$obFactura->CrearFactura($idFactura, $Fecha, $Hora, $CmbResolucion, "", "", $FormaPagoFactura, $Subtotal, $IVA, $Total, $Descuentos, $SaldoFactura, "", $idEmpresa, $idCentroCostos, $idSucursal, $idUser, $idCliente, $TotalCostos, $Observaciones, $Efectivo, $Devuelta, $Cheques, $Otros, $Tarjetas, 0, 0, "");
            if($NumFactura=="E1"){
                $Mensaje="La Resolucion está completa";
                print("E1;$Mensaje");
                exit();
            }
            if($NumFactura=="E2"){
                $Mensaje="La Resolucion está ocupada, intentelo nuevamente";
                print("E2;$Mensaje");
                exit();
            }
            
            $Datos["idPreventa"]=$idPreventa;
            $Datos["NumFactura"]=$NumFactura;
            $Datos["FechaFactura"]=$Fecha;
            $Datos["ID"]=$idFactura;
            $Datos["CuentaDestino"]=$CmbCuentaIngresoFactura;
            $Datos["EmpresaPro"]=$idEmpresa;
            $Datos["CentroCostos"]=$idCentroCostos;
            $obFactura->pos_InsertarItemsPreventaAItemsFactura($Datos,$idUser);
                
            //$obFactura->CopiarItemsCotizacionAItemsFactura($idCotizacion, $idFactura, $Fecha,$idUser, "");
            if($CmbFormaPago=='Contado'){
                $DatosCuenta=$obCon->DevuelveValores("subcuentas", "PUC", $CmbCuentaIngresoFactura);
                $CuentaDestino=$CmbCuentaIngresoFactura;
                $NombreCuentaDestino=$DatosCuenta["Nombre"];
            }else{
                $DatosCuenta=$obCon->DevuelveValores("parametros_contables", "ID", 6); //Cuenta Clientes
                $CuentaDestino=$DatosCuenta["CuentaPUC"];
                $NombreCuentaDestino=$DatosCuenta["NombreCuenta"];
            }
            
            $obFactura->InsertarFacturaLibroDiarioV2($idFactura,$CmbCuentaIngresoFactura,$idUser);
            $obCon->DescargueFacturaInventarios($idFactura, "");
            if($CmbFormaPago<>'Contado'){
                $obFactura->IngreseCartera($idFactura, $Fecha, $idCliente, $CmbFormaPago, $SaldoFactura, "");
            }
            if($CmbFormaPago=='SisteCredito' or $CmbFormaPago=='KUPY'){
                if($CmbFormaPago=='SisteCredito'){
                    $idPlataforma=1;
                }
                if($CmbFormaPago=='KUPY'){
                    $idPlataforma=2;
                }
                $obFactura->PlataformasPagoVentas($idPlataforma,$fecha,$Hora,$DatosCliente["Num_Identificacion"],$idFactura,$Total,$idUser);
            }
            if($AnticiposCruzados>0){
                
                $obFactura->CruzarAnticipoAFactura($idFactura,$Fecha,$AnticiposCruzados,$CuentaDestino,$NombreCuentaDestino,"");
            }
            
            if($CmbColaboradores>0){
                $obCon->AgregueVentaColaborador($idFactura,$CmbColaboradores);
            }
            
            
            $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
            if($DatosImpresora["Habilitado"]=="SI" AND $CmbPrint=='SI'){
                $obPrint->ImprimeFacturaPOS($idFactura,$DatosImpresora["Puerto"],1);
                $DatosTikete=$obCon->DevuelveValores("config_tiketes_promocion", "ID", 1);
                if($Total>=$DatosTikete["Tope"] AND $DatosTikete["Activo"]=="SI"){
                    $VectorTiket["F"]=0;
                    $Copias=1;
                    if($DatosTikete["Multiple"]=="SI"){
                        $Copias=floor($Total/$DatosTikete["Tope"]);
                    }
                    $obPrint->ImprimirTiketePromo($idFactura,$DatosTikete["NombreTiket"],$DatosImpresora["Puerto"],$Copias,$VectorTiket);
                }
            }
            
            $sql="INSERT INTO pos_registro_descuentos (Fecha,idFactura,TablaItem,idProducto,Cantidad,ValorDescuento,idUsuario) "
                    . "SELECT Fecha,'$idFactura',TablaItem,ProductosVenta_idProductosVenta,Cantidad,Descuento,'$idUser' "
                    . " FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa' AND Descuento <> 0;";
            $obCon->Query($sql);
            
            $obFactura->BorraReg("preventa", "VestasActivas_idVestasActivas", $idPreventa);
            
            $LinkFactura="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=2&ID=$idFactura";
            $Mensaje="<br><strong>Factura $NumFactura Creada Correctamente </strong><a href='$LinkFactura'  target='blank'> Imprimir</a>";
            $Mensaje.="<br><h3>Devuelta: ".number_format($Devuelta)."</h3>";
            
            
            print("OK;$Mensaje");
            
            
            
        break;//fin case 7
        
        case 8://Cotizar
            $obPrint = new PrintPos($idUser);            
            $fecha=date("Y-m-d");
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);
            $Observaciones="";
            $idCliente=$obCon->normalizar($_REQUEST['idCliente']);
            $idCotizacion=$obCon->CotizarDesdePreventa($idPreventa,$fecha,$idCliente,$Observaciones,"");
            $obCon->BorraReg("preventa","VestasActivas_idVestasActivas",$idPreventa);
            $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
            if($DatosImpresora["Habilitado"]=="SI"){
                $obPrint->ImprimeCotizacionPOS($idCotizacion,$DatosImpresora["Puerto"],1);
            }
            
            $RutaPrintCot="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=1&ID=".$idCotizacion;			
            print("Cotización almacenada Correctamente <a href='$RutaPrintCot' target='_blank'>Imprimir Cotización No. $idCotizacion</a>");
            
        break;//fin caso 8
        
        case 9://Autorizar una preventa
            
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $pw=$obCon->normalizar($_REQUEST['TxtAutorizaciones']);
            //$pw=md5($pw);
            $sql="SELECT Identificacion FROM usuarios WHERE Password='$pw' AND (Role='ADMINISTRADOR' or Role='SUPERVISOR') LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosAutorizacion=$obCon->FetchArray($Datos);

            if($DatosAutorizacion["Identificacion"]==''){
                print("E1;Clave incorrecta");
                exit();
            }
            $obCon->ActualizaRegistro("preventa", "Autorizado", $DatosAutorizacion["Identificacion"], "VestasActivas_idVestasActivas", $idPreventa);
        
            print("OK;Preventa $idPreventa Autorizada");
            
        break;//fin caso 9
        
        case 10:// Colocar todos los precios de venta como mayoristas
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $pw=$obCon->normalizar($_REQUEST['TxtAutorizaciones']);
            //$pw=md5($pw);
            $sql="SELECT Identificacion FROM usuarios WHERE Password='$pw' AND (Role='ADMINISTRADOR' or Role='SUPERVISOR') LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosAutorizacion=$obCon->FetchArray($Datos);

            if($DatosAutorizacion["Identificacion"]==''){
                print("E1;Clave incorrecta");
                exit();
            }
            
            $sql="SELECT idPrecotizacion FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa'";
            $Consulta=$obCon->Query($sql);
            while($DatosPreventa=$obCon->FetchAssoc($Consulta)){
                $obCon->POS_EditarPrecio($DatosPreventa["idPrecotizacion"],1, 1);
            }
            
            print("OK;Valores de Mayoristas Actualizados");
        break;//Fin caso 10
        
        case 11:// Descuento general por porcentaje
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $pw=$obCon->normalizar($_REQUEST['TxtAutorizaciones']);
            $Descuento=$obCon->normalizar($_REQUEST['TxtPorcentajeDescuento']);
            //$pw=md5($pw);
            $sql="SELECT Identificacion FROM usuarios WHERE Password='$pw' AND (Role='ADMINISTRADOR' or Role='SUPERVISOR') LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosAutorizacion=$obCon->FetchArray($Datos);

            if($DatosAutorizacion["Identificacion"]==''){
                print("E1;Clave incorrecta");
                exit();
            }
            $DatosGeneral=$obCon->DevuelveValores("configuracion_general", "ID", 6);
            $DescuentoMaximo=$DatosGeneral["Valor"];
            if($Descuento>$DescuentoMaximo){
                print("E1;El descuento máximo para esta acción es de: $DescuentoMaximo");
                exit();
            }
            $Descuento=(100-$Descuento)/100;
            
            $sql="UPDATE preventa SET Subtotal=round(Subtotal*$Descuento), Impuestos=round(Impuestos*$Descuento),"
                    . " TotalVenta=round(TotalVenta*$Descuento), ValorAcordado=round(ValorAcordado*$Descuento), Descuento=round((ValorUnitario-ValorAcordado)*Cantidad) "
                    . " WHERE VestasActivas_idVestasActivas='$idPreventa'";
            $obCon->Query($sql);
            print("OK;Descuento Aplicado");
        break;//Fin caso 11  
        
        case 12:// Colocar todos los precios segun una lista de precios
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $pw=$obCon->normalizar($_REQUEST['TxtAutorizaciones']);
            //$pw=md5($pw);
            $sql="SELECT Identificacion FROM usuarios WHERE Password='$pw' AND (Role='ADMINISTRADOR' or Role='SUPERVISOR') LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosAutorizacion=$obCon->FetchArray($Datos);

            if($DatosAutorizacion["Identificacion"]==''){
                print("E1;Clave incorrecta");
                exit();
            }
            
            $Listado=$obCon->normalizar($_REQUEST['CmbListaPrecio']);
            
            $consulta=$obCon->ConsultarTabla("preventa", " WHERE VestasActivas_idVestasActivas='$idPreventa'");
            while ($DatosPreventa=$obCon->FetchAssoc($consulta)){
                $Cantidad=$DatosPreventa["Cantidad"];
                $idProducto=$DatosPreventa["ProductosVenta_idProductosVenta"];
                $idItem=$DatosPreventa["idPrecotizacion"];
                $Tabla=$DatosPreventa["TablaItem"];
                $Datos=$obCon->ConsultarTabla("productos_precios_adicionales", " WHERE idProducto='$idProducto' AND idListaPrecios='$Listado' AND TablaVenta='$Tabla'");
                $DatosListado=$obCon->FetchArray($Datos);
                if($DatosListado["PrecioVenta"]>0){
                    $DatosProductos=$obCon->DevuelveValores($Tabla,"idProductosVenta",$idProducto);
                    $ValorAcordado=$DatosListado["PrecioVenta"];
                    $DatosTablaItem=$obCon->DevuelveValores("tablas_ventas", "NombreTabla", $Tabla);
                    if($DatosTablaItem["IVAIncluido"]=="SI"){

                        $ValorAcordado=round($ValorAcordado/($DatosProductos["IVA"]+1),2);

                    }
                    $Subtotal=round($ValorAcordado*$Cantidad,2);


                    $IVA=round($Subtotal*$DatosProductos["IVA"],2);
                    //$SubtotalCosto=$DatosProductos["CostoUnitario"]*$Cantidad;
                    $Total=$Subtotal+$IVA;
                    $filtro="idPrecotizacion";

                    $obCon->ActualizaRegistro("preventa","Subtotal", $Subtotal, $filtro, $idItem);
                    $obCon->ActualizaRegistro("preventa","Impuestos", $IVA, $filtro, $idItem);
                    $obCon->ActualizaRegistro("preventa","TotalVenta", $Total, $filtro, $idItem);
                    $obCon->ActualizaRegistro("preventa","ValorAcordado", $ValorAcordado, $filtro, $idItem);
                }
                
            }
            
            print("OK;Valores Actualizados");
        break;//Fin caso 12
        
        case 13:// Descuento general a precio costo
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $pw=$obCon->normalizar($_REQUEST['TxtAutorizaciones']);
            
            //$pw=md5($pw);
            $sql="SELECT Identificacion FROM usuarios WHERE Password='$pw' AND (Role='ADMINISTRADOR' or Role='SUPERVISOR') LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosAutorizacion=$obCon->FetchArray($Datos);

            if($DatosAutorizacion["Identificacion"]==''){
                print("E1;Clave incorrecta");
                exit();
            }
            $DatosGeneral=$obCon->DevuelveValores("configuracion_general", "ID", 7);
            $Habilitado=$DatosGeneral["Valor"];
            if($Habilitado==0 or $Habilitado==''){
                print("E1;Esta opción no está habilitada");
                exit();
            }
            $sql="UPDATE `preventa` "
                    . "SET `ValorAcordado`=round((`CostoUnitario`)/(`PorcentajeIVA`+1),2), "
                    . "`Impuestos`=round(`PorcentajeIVA`*(`ValorAcordado`*`Cantidad`),2),"
                    . "`Subtotal`=(`ValorAcordado`*`Cantidad`), `TotalVenta`=(`Subtotal`+`Impuestos`) "
                    . "WHERE `VestasActivas_idVestasActivas`='$idPreventa' ";
            
            $obCon->Query($sql);
            print("OK;Descuento Aplicado");
        break;//Fin caso 13
        
        case 14:// Cerrar turno
            $obPrint = new PrintPos($idUser);
            $idCaja=1;
            $idCierre=$obCon->CierreTurnoPos($idUser,$idCaja,"");
            $VectorCierre["idCierre"]=$idCierre;
            $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
                        
            if($DatosImpresora["Habilitado"]=="SI"){

                $obPrint->ImprimeCierre($idUser,$VectorCierre,$DatosImpresora["Puerto"],1);
            }
            
            print("OK;Se ha cerrado el turno de usuario $idUser");
        break;//Fin caso 14
        
        case 15:// Colocar todos los precios de venta como mayoristas si se digita el codigo de un cliente
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);            
            $CodigoTarjeta=$obCon->normalizar($_REQUEST['CodigoTarjeta']);
            
            $sql="SELECT idClientes, RazonSocial FROM clientes WHERE CodigoTarjeta='$CodigoTarjeta' LIMIT 1";
            $Datos=$obCon->Query($sql);
            $DatosCliente=$obCon->FetchArray($Datos);

            if($DatosCliente["idClientes"]==''){
                print("E1;Código inexistente");
                exit();
            }
            $idCliente=$DatosCliente["idClientes"];
            $RazonSocial=$DatosCliente["RazonSocial"];
            $sql="SELECT idPrecotizacion FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa'";
            $Consulta=$obCon->Query($sql);
            while($DatosPreventa=$obCon->FetchAssoc($Consulta)){
                $obCon->POS_EditarPrecio($DatosPreventa["idPrecotizacion"],1, 1);
            }
            
            print("OK;Valores de Mayoristas Actualizados;$idCliente;$RazonSocial");
        break;//Fin caso 15
        
        case 16://Crear un tercero
            $nit=$obCon->normalizar($_REQUEST['Num_Identificacion']);
            $idCiudad=$obCon->normalizar($_REQUEST['CodigoMunicipio']);
            $DatosCiudad=$obCon->DevuelveValores("cod_municipios_dptos", "ID", $idCiudad);
            $DV=$obCon->CalcularDV($nit);
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Nit Digitado ya existe");
                exit();
            }
            $Datos["Tipo_Documento"]=$obCon->normalizar($_REQUEST['TipoDocumento']);  
            $Datos["Num_Identificacion"]=$nit;    
            $Datos["DV"]=$DV;  
            $Datos["Primer_Apellido"]=$obCon->normalizar($_REQUEST['PrimerApellido']);    
            $Datos["Segundo_Apellido"]=$obCon->normalizar($_REQUEST['SegundoApellido']);    
            $Datos["Primer_Nombre"]=$obCon->normalizar($_REQUEST['PrimerNombre']);    
            $Datos["Otros_Nombres"]=$obCon->normalizar($_REQUEST['OtrosNombres']);    
            $Datos["RazonSocial"]=$obCon->normalizar($_REQUEST['RazonSocial']);    
            $Datos["Direccion"]=$obCon->normalizar($_REQUEST['Direccion']);    
            $Datos["Cod_Dpto"]=$DatosCiudad["Cod_Dpto"];    
            $Datos["Cod_Mcipio"]=$DatosCiudad["Cod_mcipio"];    
            $Datos["Pais_Domicilio"]=169;   
            $Datos["Telefono"]=$obCon->normalizar($_REQUEST['Telefono']);             
            $Datos["Ciudad"]=$DatosCiudad["Ciudad"];    
            $Datos["Email"]=$obCon->normalizar($_REQUEST['Email']); 
            $Datos["Cupo"]=$obCon->normalizar($_REQUEST['Cupo']);    
            $Datos["CodigoTarjeta"]=$obCon->normalizar($_REQUEST['CodigoTarjeta']); 
            
            $sqlClientes=$obCon->getSQLInsert("clientes", $Datos);
            $sqlProveedores=$obCon->getSQLInsert("proveedores", $Datos);
            $obCon->Query($sqlClientes);
            $obCon->Query($sqlProveedores);
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            
            print("OK;Se ha creado el tercero ".$Datos["RazonSocial"].", con Identificación: ".$nit.";".$DatosCliente["idClientes"].";".$Datos["RazonSocial"]);
            
        break;//FIn caso 16
        
        case 17://Verifica si ya existe un nit
            $nit=$obCon->normalizar($_REQUEST['Num_Identificacion']);
            
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Nit Digitado ya existe");
                exit();
            }
            print("OK;El cliente no existe aún");
        break;//Fin caso 17
        
        case 18://Verifica si ya existe el codigo de una tarjeta
            $Codigo=$obCon->normalizar($_REQUEST['CodigoTarjeta']);
            
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " CodigoTarjeta='$Codigo'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Código de la tarjeta Digitado ya existe");
                exit();
            }
            print("OK;Código disponible");
        break;//Fin caso 18
        
        case 19://Crea un separado
            $fecha=date("Y-m-d");
            $Hora=date("H:i:s");
            $obPrint = new PrintPos($idUser);
            $Abono=$obCon->normalizar($_REQUEST['TxtAbonoCrearSeparado']);
            $idCliente=$obCon->normalizar($_REQUEST['idCliente']);
            $idPreventa=$obCon->normalizar($_REQUEST['idPreventa']);
            if($idCliente<=1){
                print("E1;Debe seleccionar un cliente diferente a Clientes Varios");
                exit();
            }
            $TotalSeparado=$obCon->Sume("preventa", "TotalVenta", " WHERE VestasActivas_idVestasActivas='$idPreventa'");
            if($Abono>=$TotalSeparado){
                print("E1;El Abono no puede ser mayor o igual al total del separado");
                exit();
            }
            if($Abono<=0){
                print("E1;El Abono debe ser un número mayor a cero");
                exit();
            }
            if($TotalSeparado==0){
                print("E1;La Preventa no tiene items agregados");
                exit();
            }
            $consulta=$obCon->ConsultarTabla("preventa", " WHERE VestasActivas_idVestasActivas='$idPreventa'");
            if($obCon->NumRows($consulta)){
                $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
                $CentroCosto=$DatosCaja["CentroCostos"];
                $CuentaDestino=$DatosCaja["CuentaPUCEfectivo"];
                $Concepto="ANTICIPO POR SEPARADO";
                $VectorIngreso["Separado"]=1;

                $idComprobanteIngreso=$obCon->RegistreAnticipo2($fecha,$CuentaDestino,$idCliente,$Abono,$CentroCosto,$Concepto,$idUser,$VectorIngreso);

                $DatosSeparado["idCompIngreso"]=$idComprobanteIngreso;
                $idSeparado=$obCon->RegistreSeparado($fecha,$Hora,$idPreventa,$idCliente,$Abono,$DatosSeparado);
                $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
                if($DatosImpresora["Habilitado"]=="SI"){
                    $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 8);  //Trae el numero de copias que se debe imprimir al hacer el separado
                    $obPrint->ImprimeSeparado($idSeparado, $DatosImpresora["Puerto"], $Configuracion["Valor"]);
                }
            }
            print("OK;Separado $idSeparado Creado Exitósamente");
        break;//Fin caso 19
        
        case 20://Crear un egreso
            $fecha=date("Y-m-d");
            $Hora=date("H:i:s");
            $obPrint = new PrintPos($idUser);
            $idProveedor=$obCon->normalizar($_REQUEST['Tercero']);
            $CuentaDestino=$obCon->normalizar($_REQUEST['CuentaPUC']);
            $Subtotal=$obCon->normalizar($_REQUEST['SubtotalEgreso']);
            $IVA=$obCon->normalizar($_REQUEST['IVAEgreso']);
            $Total=$obCon->normalizar($_REQUEST['TotalEgreso']);
            $NumFact=$obCon->normalizar($_REQUEST['TxtNumeroSoporteEgreso']);
            $Concepto=$obCon->normalizar($_REQUEST['TxtConcepto']);
            
            $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
            
            $CuentaOrigen=$DatosCaja["CuentaPUCEfectivo"];
            $CentroCostos=$DatosCaja["CentroCostos"];
            $CuentaPUCIVA=$DatosCaja["CuentaPUCIVAEgresos"];
            $TipoEgreso="VentasRapidas";
            $TipoPago="Contado";
            $Sanciones=0;
            $Intereses=0;
            $Impuestos=0;
            $ReteFuente=0;
            $ReteIVA=0;
            $ReteICA=0;
            $VectorEgreso["Fut"]=0;  //Uso futuro
            ///                
            
            $idEgreso=$obCon->CrearEgreso($fecha,"",$idUser,$CentroCostos,$TipoPago,$CuentaOrigen,$CuentaDestino,$CuentaPUCIVA,$idProveedor, $Concepto,$NumFact,"",$TipoEgreso,$Subtotal,$IVA,$Total,$Sanciones,$Intereses,$Impuestos,$ReteFuente,$ReteIVA,$ReteICA,$VectorEgreso);
            
            $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
            $VectorEgresos["Fut"]=1;
            if($DatosImpresora["Habilitado"]=="SI"){
                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 9); //Copias del egreso
                $obPrint->ImprimeEgresoPOS($idEgreso,$VectorEgresos,$DatosImpresora["Puerto"],$Configuracion["Valor"]);
                    
            }
            print("OK;Egreso $idEgreso Realizado");
        break;//fin caso 20
        
        case 21://Abono a un separado
            $fecha=date("Y-m-d");
            $Hora=date("H:i:s");
            $obPrint = new PrintPos($idUser);
            $Valor=$obCon->normalizar($_REQUEST['Abono']);
            $idSeparado=$obCon->normalizar($_REQUEST['idSeparado']);
            $DatosSeparado=$obCon->DevuelveValores("separados", "ID", $idSeparado);
            
            if($Valor>$DatosSeparado["Saldo"]){
                print("E1;El abono no puede ser mayor a ". number_format($DatosSeparado["Saldo"]));
                exit();
            }
            
            $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
            $CuentaDestino=$DatosCaja["CuentaPUCEfectivo"];
            $CentroCosto=$DatosCaja["CentroCostos"];
            $Concepto="ABONO A SEPARADO No $idSeparado";
            $VectorIngreso["Separado"]=1;
            $idCliente=$DatosSeparado["idCliente"];
            $idIngreso=$obCon->RegistreAnticipo2($fecha,$CuentaDestino,$idCliente,$Valor,$CentroCosto,$Concepto,$idUser,$VectorIngreso);
            
            $VectorSeparados["idCompIngreso"]=$idIngreso;
            $Saldo=$obCon->RegistreAbonoSeparado($idSeparado,$Valor,$fecha,$Hora,$VectorSeparados);
            
            $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);
            $Impresiones=2;
            $NumFactura="";
            if($Saldo==0){
		$Impresiones=1;
                $VectorSeparados["Ft"]="";
                $CuentaDestino=$DatosCaja["CuentaPUCEfectivo"];
                $NumFactura=$obCon->CreaFacturaDesdeSeparado($idSeparado,$CuentaDestino,$VectorSeparados);
               if($DatosImpresora["Habilitado"]=="SI"){
                    $obPrint->ImprimeFacturaPOS($NumFactura,$DatosImpresora["Puerto"],1);
               }
            }
            
            if($DatosImpresora["Habilitado"]=="SI"){
                $obPrint->ImprimeSeparado($idSeparado, $DatosImpresora["Puerto"], $Impresiones);

            }
            $LinkComprobante="../../VAtencion/PDF_Documentos.php?idDocumento=4&idIngreso=$idIngreso";
            $MensajeComprobante="<br><strong>Comprobante de ingreso $idIngreso Creado Correctamente </strong><a href='$LinkComprobante'  target='blank'> Imprimir</a>";
            $MensajeFactura="";
            if($NumFactura<>''){
                $LinkFactura="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=2&ID=$NumFactura";
                $MensajeFactura="<br><strong>Factura Creada Correctamente </strong><a href='$LinkFactura'  target='blank'> Imprimir</a>";
           
            }
            print("OK;$MensajeComprobante;$MensajeFactura");
        break;//Fin caso 21
        
        case 22://Factura un item
            $obPrint=new PrintPos($idUser);
            $idItemSeparado=$obCon->normalizar($_REQUEST['idItemSeparado']);
            $Cantidad=$obCon->normalizar($_REQUEST['Cantidad']);
            $DatosItem=$obCon->DevuelveValores("separados_items", "ID", $idItemSeparado);
            if($Cantidad>$DatosItem["Cantidad"]){
                print("E1;La cantidad no puede ser mayor a ".$DatosItem["Cantidad"]);
                exit();
            }
            $NumFactura=$obCon->FacturarItemSeparado($idItemSeparado,$Cantidad,$idUser,"");
            $obPrint->ImprimeFacturaPOS($NumFactura, "", 1);
            $MensajeFactura="";
            if($NumFactura<>''){
                $LinkFactura="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=2&ID=$NumFactura";
                $MensajeFactura="<br><strong>Factura Creada Correctamente </strong><a href='$LinkFactura'  target='blank'> Imprimir</a>";
           
            }
            print("OK;$MensajeFactura");
        break;//Fin caso 22   
        
        case 23: //Abona a una factura a credito
            $obPrint=new PrintPos($idUser);
            $fecha=date("Y-m-d");
            $Hora=date("H:i:s");
            $idCartera=$obCon->normalizar($_REQUEST['idCredito']);
            $idFactura=$obCon->normalizar($_REQUEST['idFactura']);            
            $Valor=$obCon->normalizar($_REQUEST["Abono"]);
            $Intereses=$obCon->normalizar($_REQUEST["Intereses"]);
            $AbonoTarjetas=$obCon->normalizar($_REQUEST["Tarjetas"]);
            $AbonoCheques=$obCon->normalizar($_REQUEST["Cheques"]);
            $AbonoOtros=$obCon->normalizar($_REQUEST["Otros"]);
            $TotalAbono=$Valor+$AbonoTarjetas+$AbonoCheques+$AbonoOtros;
            $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
            if($TotalAbono+$Intereses==0){
                print("E1;Debe enviar algún valor");
                exit();
            }
            if($TotalAbono<=$DatosFactura["SaldoFact"]){
                $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
                $CuentaDestino=$DatosCaja["CuentaPUCEfectivo"];
                $CentroCosto=$DatosCaja["CentroCostos"];
                $idTerceroInteres=$DatosCaja["idTerceroIntereses"];
                $CentroCosto=$DatosCaja["CentroCostos"];
                $Concepto="ABONO A FACTURA No $DatosFactura[Prefijo] - $DatosFactura[NumeroFactura]";
                $VectorIngreso["fut"]="";
                $TipoPago="";
                $idComprobanteAbono=$obCon->RegistreAbonoCarteraCliente($fecha,$Hora,$CuentaDestino,$idFactura,$Valor,$TipoPago,$CentroCosto,$Concepto,$idUser,$VectorIngreso);
                if($AbonoTarjetas>0){
                    $TipoPago="Tarjetas";
                    $DatosParametros=$obCon->DevuelveValores("parametros_contables", "ID", 17);
                    $idComprobanteAbono=$obCon->RegistreAbonoCarteraCliente($fecha,$Hora,$DatosParametros["CuentaPUC"],$idFactura,$AbonoTarjetas,$TipoPago,$CentroCosto,$Concepto,$idUser,$VectorIngreso);
                }
                if($AbonoCheques>0){
                    $TipoPago="Cheques";
                    $DatosParametros=$obCon->DevuelveValores("parametros_contables", "ID", 18);
                    $idComprobanteAbono=$obCon->RegistreAbonoCarteraCliente($fecha,$Hora,$DatosParametros["CuentaPUC"],$idFactura,$AbonoCheques,$TipoPago,$CentroCosto,$Concepto,$idUser,$VectorIngreso);
                }
                if($AbonoOtros>0){
                    $TipoPago="Bonos";
                    $idComprobanteAbono=$obCon->RegistreAbonoCarteraCliente($fecha,$Hora,$DatosCaja["CuentaPUCEfectivo"],$idFactura,$AbonoCheques,$TipoPago,$CentroCosto,$Concepto,$idUser,$VectorIngreso);
                }
                if($Intereses>0){
                    $obCon->RegistrePagoInteresesSisteCredito($fecha,$Hora,$Intereses,$idFactura,$idUser,$idTerceroInteres,$DatosCaja["CuentaPUCEfectivo"],$CentroCosto,"");
                }
                $DatosImpresora=$obCon->DevuelveValores("config_puertos", "ID", 1);

                if($DatosImpresora["Habilitado"]=="SI"){
                    $obPrint->ImprimeComprobanteAbonoFactura($idComprobanteAbono, $DatosImpresora["Puerto"], 2);

                }
                $DatosAbono=$obCon->DevuelveValores("facturas_abonos", "ID", $idComprobanteAbono);
                $idComprobanteIngreso=$DatosAbono["idComprobanteIngreso"];
                print("OK;Abono Registrado Exitosamente <a href='../../VAtencion/PDF_Documentos.php?idDocumento=4&idIngreso=$idComprobanteIngreso' target='_blank'> Imprimir</a>");
           
            }else{
                print("E1;El Saldo de la Factura es inferior a los abonos digitados, vuelva a intentarlo");
                exit();
            }
            
        break;//Fin caso 23
        
        case 24://Fin caso 24
            $obContabilidad = new contabilidad($idUser);
            $Fecha=date("Y-m-d");
            $Hora=date("H:i:s");
            $Tercero=$obCon->normalizar($_REQUEST['Tercero']);
            $CmbPlataforma=$obCon->normalizar($_REQUEST['CmbPlataforma']);            
            $Abono=$obCon->normalizar($_REQUEST["Abono"]);
            
            $DatosPlataforma=$obCon->DevuelveValores("comercial_plataformas_pago", "ID", $CmbPlataforma);
            $DatosCaja=$obCon->DevuelveValores("cajas", "idUsuario", $idUser);
            $CuentaDestino=$DatosCaja["CuentaPUCEfectivo"];
            $CentroCosto=$DatosCaja["CentroCostos"];
            $idTerceroInteres=$DatosPlataforma["NIT"];
            
            $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 19);
            $idComprobante=$obContabilidad->CrearComprobanteIngreso($Fecha, "", $idTerceroInteres, $Abono, "PlataformasPago", "Ingreso por Plataforma de Pago $CmbPlataforma", "CERRADO");
            $obContabilidad->ContabilizarComprobanteIngreso($idComprobante, $idTerceroInteres, $CuentaDestino, $Parametros["CuentaPUC"], $DatosCaja["idEmpresa"], $DatosCaja["idSucursal"], $DatosCaja["CentroCostos"]);
            
            $obCon->IngresoPlataformasPago($CmbPlataforma,$Fecha, $Hora, $Tercero, $Abono, $idComprobante, $idUser);
            print("OK;Ingreso registrado en Comprobante $idComprobante");
            
        break;    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
