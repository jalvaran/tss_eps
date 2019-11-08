<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class RevisionFacturas extends conexion{
    
    public function ConstruirVistaParaComprobacion1($db,$idUser) {
        $sql="DROP TABLE IF EXISTS $db.`vista_espejo_tibco`;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.vista_espejo_tibco AS
            SELECT * 
            FROM $db.historial_carteracargada_eps t1 WHERE t1.ValidaFactura > 0 ORDER BY ValidaFactura;";
        $this->Query($sql);
    }
    
    public function ConstruirVistaParaComprobacion2($db,$idUser) {
        $sql="DROP TABLE IF EXISTS $db.`vista_espejo_tibco`;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.vista_espejo_tibco AS
            SELECT * 
            FROM $db.historial_carteracargada_eps t1 WHERE t1.ValidaFactura > 0 AND t1.CantidadFacturasRepetidasConCerosIzquierda>1 ORDER BY ValidaFactura;";
        $this->Query($sql);
    }
    
    public function ConstruirTablaDeFacturasConCerosIzquierda($db,$idUser) {
        $sql="DROP TABLE IF EXISTS $db.`facturas_para_revision_por_ceros_izquierda`;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.facturas_para_revision_por_ceros_izquierda AS
            SELECT t1.ID,t1.TipoOperacion,t1.NumeroOperacion,t1.FechaFactura,t1.NumeroFactura,t1.MesServicio,t1.ValorOriginal,
            t1.Descripcion,t1.NumeroRadicado,t1.ValidaFactura,
            (SELECT IFNULL((SELECT SUM(ValorPago) FROM $db.notas_db_cr_2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.TipoOperacion2=t5.TipoOperacion AND Aplicacion='TotalPagos')  AND (t3.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,  
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,    
            (SELECT COUNT(DISTINCT NumeroFactura ) FROM $db.historial_carteracargada_eps t2 WHERE t2.ValidaFactura=t1.ValidaFactura ) AS TotalRepetidas 
            FROM $db.historial_carteracargada_eps t1 WHERE EXIST (SELECT 1 FROM vista_espejo_tibco t2 WHERE t1.ValidaFactura=t2.ValidaFactura)";
        $this->Query($sql);
    }
    
    public function ActualizarCantidadFacturasConCeros($db,$idUser) {
        $sql="update historial_carteracargada_eps set CantidadFacturasRepetidasConCerosIzquierda=0;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.facturas_para_revision_por_ceros_izquierda AS
            SELECT t1.ID,t1.TipoOperacion,t1.NumeroOperacion,t1.FechaFactura,t1.NumeroFactura,t1.MesServicio,t1.ValorOriginal,
            t1.Descripcion,t1.NumeroRadicado,t1.ValidaFactura,
            (SELECT IFNULL((SELECT SUM(ValorPago) FROM $db.notas_db_cr_2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.TipoOperacion2=t5.TipoOperacion AND Aplicacion='TotalPagos')  AND (t3.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,  
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,    
            (SELECT COUNT(DISTINCT NumeroFactura ) FROM $db.historial_carteracargada_eps t2 WHERE t2.ValidaFactura=t1.ValidaFactura ) AS TotalRepetidas 
            FROM $db.historial_carteracargada_eps t1 WHERE EXIST (SELECT 1 FROM vista_espejo_tibco t2 WHERE t1.ValidaFactura=t2.ValidaFactura)";
        $this->Query($sql);
    }
    
    
    
    public function ConstruirTablaDeFacturasConCerosIzquierdaold($db,$idUser) {
        $sql="DROP TABLE IF EXISTS $db.`facturas_para_revision_por_ceros_izquierda`;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.facturas_para_revision_por_ceros_izquierda AS
            SELECT t1.ID,t1.TipoOperacion,t1.NumeroOperacion,t1.FechaFactura,t1.NumeroFactura,t1.MesServicio,t1.ValorOriginal,
            t1.Descripcion,t1.NumeroRadicado,t1.ValidaFactura,
            (SELECT IFNULL((SELECT SUM(ValorPago) FROM $db.notas_db_cr_2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.TipoOperacion2=t5.TipoOperacion AND Aplicacion='TotalPagos')  AND (t3.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,  
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM $db.anticipos2 t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t5 WHERE Estado=1 AND t3.NumeroInterno=t5.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,    
            (SELECT COUNT(DISTINCT NumeroFactura ) FROM $db.historial_carteracargada_eps t2 WHERE t2.ValidaFactura=t1.ValidaFactura ) AS TotalRepetidas 
            FROM $db.historial_carteracargada_eps t1 WHERE t1.ValidaFactura > 0 ORDER BY ValidaFactura;";
        $this->Query($sql);
    }
    
    
    
    
    //Fin Clases
}
