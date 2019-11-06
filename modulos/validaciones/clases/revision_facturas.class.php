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
    
    public function ContruirTablaDeFacturasConCerosIzquierda($db,$idUser) {
        $sql="DROP TABLE IF EXISTS $db.`facturas_para_revision_por_ceros_izquierda`;";
        $this->Query($sql);
        $sql="CREATE TABLE $db.facturas_para_revision_por_ceros_izquierda AS
            SELECT t1.ID,t1.TipoOperacion,t1.NumeroOperacion,t1.FechaFactura,t1.NumeroFactura,t1.MesServicio,t1.ValorOriginal,t1.Descripcion,t1.NumeroRadicado,
            (SELECT COUNT(DISTINCT NumeroFactura ) FROM $db.historial_carteracargada_eps t2 WHERE t2.ValidaFactura=t1.ValidaFactura ) AS TotalRepetidas 
            FROM $db.historial_carteracargada_eps t1 WHERE t1.ValidaFactura > 0 ORDER BY ValidaFactura;";
        $this->Query($sql);
    }
    
    
    //Fin Clases
}
