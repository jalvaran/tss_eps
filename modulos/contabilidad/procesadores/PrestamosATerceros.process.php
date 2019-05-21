<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/PrestamosATerceros.class.php");
include_once("../../../general/clases/contabilidad.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Prestamos($idUser);
    $obContabilidad = new contabilidad($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crea un prestamo a un tercero
            $Fecha=$obCon->normalizar($_REQUEST["TxtFecha"]); 
            $Tercero=$obCon->normalizar($_REQUEST["CmbTercero"]);
            $CuentaOrigen=$obCon->normalizar($_REQUEST["CmbCuentaOrigen"]);
            $CuentaDestino=$obCon->normalizar($_REQUEST["CmbCuentaDestino"]);
            $CmbEmpresa=$obCon->normalizar($_REQUEST["CmbEmpresa"]);
            $CmbSucursal=$obCon->normalizar($_REQUEST["CmbSucursal"]);
            $CmbCentroCosto=$obCon->normalizar($_REQUEST["CmbCentroCosto"]);
            $TxtObservaciones=$obCon->normalizar($_REQUEST["TxtObservaciones"]);
            $Valor=$obCon->normalizar($_REQUEST["TxtValor"]);   
            $idPrestamo=$obCon->CrearPrestamo($Fecha, $Tercero, $Valor,$CmbEmpresa,$CmbSucursal,$CmbCentroCosto,$TxtObservaciones, "");
            $obCon->ContabilizarPrestamo($Fecha, $idPrestamo, $CuentaOrigen, $CuentaDestino, $Valor, $TxtObservaciones, $CmbEmpresa, $CmbCentroCosto, $CmbSucursal);
            print("OK;Prestamo Creado");
        break; 
    
        case 2: //Registra un abono
            $idPrestamo=$obCon->normalizar($_REQUEST["idPrestamo"]);              
            $Fecha=$obCon->normalizar($_REQUEST["TxtFecha"]);             
            $CuentaDestino=$obCon->normalizar($_REQUEST["CmbCuentaDestino"]);            
            $Valor=$obCon->normalizar($_REQUEST["TxtValor"]);
            $DatosPrestamo=$obCon->DevuelveValores("prestamos_terceros", "ID", $idPrestamo);
            $Abonos=$DatosPrestamo["Abonos"]+$Valor;
            $Saldo=$DatosPrestamo["Valor"]-$Abonos;
            if($Saldo<0){
                print("Error el valor del abono no puede ser mayor a: ".$DatosPrestamo["Saldo"]);
                exit();
            }
            $sql="SELECT CuentaPUC, NombreCuenta FROM librodiario WHERE Tipo_Documento_Intero='Prestamos' AND Num_Documento_Interno='$idPrestamo' AND CuentaPUC LIKE '13%' LIMIT 1";
            $consulta=$obCon->Query($sql);
            $DatosLibro=$obCon->FetchAssoc($consulta);
            
            $idComprobante=$obContabilidad->CrearComprobanteIngreso($Fecha, "", $DatosPrestamo["Tercero"], $Valor, "AbonoPrestamo", "Abono a Prestamo $idPrestamo", "CERRADO");
            $obContabilidad->ContabilizarComprobanteIngreso($idComprobante, $DatosPrestamo["Tercero"], $CuentaDestino, $DatosLibro["CuentaPUC"], $DatosPrestamo["idEmpresa"], $DatosPrestamo["idSucursal"], $DatosPrestamo["idSucursal"]);
            
            $obCon->RegistreAbonoPrestamoTerceros($idPrestamo, $Fecha, $Valor, $idComprobante);
            $obCon->ActualizaRegistro("prestamos_terceros", "Abonos", $Abonos, "ID", $idPrestamo);
            $obCon->ActualizaRegistro("prestamos_terceros", "Saldo", $Saldo, "ID", $idPrestamo);
            $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=4&idIngreso=".$idComprobante;
            $Link="<a href='$Ruta' target='_blank'>Imprimir</a>";
            print("OK;Abono Registrado en el Comprobante de ingreso $idComprobante $Link");
        break; 
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>