<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Prestamos extends ProcesoVenta{
    /**
     * Crear un prestamo
     * @param type $Fecha
     * @param type $Tercero
     * @param type $Valor
     * @param type $vector
     */
    public function CrearPrestamo($Fecha,$Tercero,$Valor,$CmbEmpresa,$CmbSucursal,$CmbCentroCosto,$TxtObservaciones,$vector){
        
        $Datos["Fecha"]=$Fecha;
        $Datos["Tercero"]=$Tercero;
        $Datos["Valor"]=$Valor;
        $Datos["Saldo"]=$Valor;
        $Datos["Estado"]="ABIERTO";
        $Datos["idEmpresa"]=$CmbEmpresa;
        $Datos["idCentroCostos"]=$CmbCentroCosto;
        $Datos["Observaciones"]=$TxtObservaciones;
        $Datos["idSucursal"]=$CmbSucursal;
        $Datos["idUser"]=$_SESSION["idUser"];
        $sql=$this->getSQLInsert("prestamos_terceros",$Datos);
        $this->Query($sql);
        $idPrestamo=$this->ObtenerMAX("prestamos_terceros", "ID", 1, "");
        return($idPrestamo);
    }
    /**
     * Contabilizar un prestamo
     * @param type $Fecha
     * @param type $idPrestamo
     * @param type $CuentaOrigen
     * @param type $CuentaDestino
     * @param type $Valor
     * @param type $Concepto
     * @param type $idEmpresa
     * @param type $idCentroCostos
     * @param type $idSede
     */
    public function ContabilizarPrestamo($Fecha,$idPrestamo,$CuentaOrigen,$CuentaDestino,$Valor,$Concepto,$idEmpresa,$idCentroCostos,$idSede) {
        $DatosPrestamo=$this->DevuelveValores("prestamos_terceros", "ID", $idPrestamo); 
        $DatosCuentaOrigen=$this->DevuelveValores("subcuentas", "PUC", $CuentaOrigen);
        $DatosCuentaDestino=$this->DevuelveValores("subcuentas", "PUC", $CuentaDestino);
        $this->IngreseMovimientoLibroDiario($Fecha, "Prestamos", $idPrestamo, "", $DatosPrestamo["Tercero"], $CuentaOrigen, $DatosCuentaOrigen["Nombre"], "Prestamos", "CR", $Valor, $Concepto, $idCentroCostos, $idSede, "");
        $this->IngreseMovimientoLibroDiario($Fecha, "Prestamos", $idPrestamo, "", $DatosPrestamo["Tercero"], $CuentaDestino, $DatosCuentaDestino["Nombre"], "Prestamos", "DB", $Valor, $Concepto, $idCentroCostos, $idSede, "");
    }
    
    public function RegistreAbonoPrestamoTerceros($idPrestamo,$Fecha,$Valor,$idComprobanteIngreso) {
        $Datos["Fecha"]=$Fecha;
        $Datos["idPrestamo"]=$idPrestamo;
        $Datos["Valor"]=$Valor;
        $Datos["idComprobanteIngreso"]=$idComprobanteIngreso;
        $Datos["idUser"]=$_SESSION["idUser"];
        $sql=$this->getSQLInsert("prestamos_terceros_abonos",$Datos);
        $this->Query($sql);
           
    }
    /**
     * Fin Clase
     */
}
