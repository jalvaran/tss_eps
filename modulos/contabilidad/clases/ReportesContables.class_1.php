<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Contabilidad extends conexion{
    /**
     * Crea la vista para el balance x terceros
     * @param type $Tipo
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $vector
     * @return type
     */
    public function ConstruirVistaBalanceTercero($Tipo,$FechaInicial,$FechaFinal,$Empresa,$CentroCostos,$vector){
        
        $sql="DROP VIEW IF EXISTS `vista_balancextercero2`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE ";
        if($Tipo==1){
            $Condicion.="Fecha>='$FechaInicial' AND Fecha <='$FechaFinal'";
        }else{
            $Condicion.="Fecha <='$FechaFinal'";
        }
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_balancextercero2 AS
            SELECT '' as ID,`Tercero_Identificacion` as Identificacion,`Tercero_Razon_Social` AS Razon_Social,
            `CuentaPUC` as Cuenta, `NombreCuenta` as Nombre_Cuenta,            
            SUM(`Debito`) AS Debitos,SUM(`Credito`) AS Creditos,(SUM(`Debito`)-SUM(`Credito`)) AS Neto,
            idEmpresa,idCentroCosto
            FROM `librodiario`
            $Condicion $CondicionEmpresa $CondicionCentroCostos
            GROUP BY `Tercero_Identificacion` ORDER BY SUBSTRING(`CuentaPUC`,1,8),Tercero_Razon_Social;";         
        $this->Query($sql);
        
    }
    /**
     * Constuye una vista con la informacion de las retenciones practicadas a un tercero
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $CmbTercero
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $CmbCiudadRetencion
     * @param type $CmbCiudadPago
     * @param type $Vector
     */
    public function ConstruirVistaRetencionesXTercero($FechaInicial, $FechaFinal,$CmbTercero, $Empresa, $CentroCostos,$CmbCiudadRetencion,$CmbCiudadPago, $Vector) {
        $sql="DROP VIEW IF EXISTS `vista_retenciones_tercero`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' AND Tercero='$CmbTercero' ";
        
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_retenciones_tercero AS
            SELECT *
            FROM vista_retenciones $Condicion;";         
        $this->Query($sql);
    }
    /**
     * Construye la vista para el estado de resultados por año
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $CmbAnio
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $Vector
     */
     public function ConstruirVistaEstadoResultados($CmbAnio, $Empresa, $CentroCostos,$Vector) {
        $FechaInicial= $CmbAnio."-01-01";
        $FechaFinal = $CmbAnio."-12-31";
        $sql="DROP VIEW IF EXISTS `vista_estado_resultados_anio`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' ";
        
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_estado_resultados_anio AS
            SELECT *
            FROM librodiario $Condicion;";         
        $this->Query($sql);
    }
    
    /**
     * Fin Clase
     */
}
