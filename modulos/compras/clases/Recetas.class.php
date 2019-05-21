<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos para construir recetas
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class Recetas extends ProcesoVenta{
    
    /**
     * Agregar un item a una receta
     * @param type $idProducto
     * @param type $TablaInsumo
     * @param type $idTablaInsumo
     * @param type $idInsumo
     * @param type $Cantidad
     * @param type $idUser
     * @param type $Vector
     */
    public function AgregarItemReceta($idProducto,$TablaInsumo,$idTablaInsumo,$idInsumo,$Cantidad,$idUser, $Vector) {
        $DatosProducto=$this->ValorActual("productosventa", "Referencia", " idProductosVenta='$idProducto'");
        $ReferenciaProducto=$DatosProducto["Referencia"];
        $DatosInsumo=$this->ValorActual($TablaInsumo, "Referencia", " $idTablaInsumo='$idInsumo'");
        $ReferenciaInsumo=$DatosInsumo["Referencia"];        
        $DatosReceta=$this->ValorActual("recetas_relaciones", "ID,Cantidad", " ReferenciaProducto='$ReferenciaProducto' AND ReferenciaIngrediente='$ReferenciaInsumo'");
        if($DatosReceta["ID"]==''){
            $Datos["ReferenciaProducto"]=$ReferenciaProducto;
            $Datos["ReferenciaIngrediente"]=$ReferenciaInsumo;
            $Datos["TablaIngrediente"]=$TablaInsumo;
            $Datos["Cantidad"]=$Cantidad;
            $Datos["idUser"]=$idUser;
            $sql=$this->getSQLInsert("recetas_relaciones", $Datos);
            $this->Query($sql);
            $this->ActualizaRegistro("productosventa", "Kit", 1, "Referencia", $ReferenciaProducto);
        
        }else{
            $idReceta=$DatosReceta["ID"];
            $NuevaCantidad=$DatosReceta["Cantidad"]+$Cantidad;
            $this->ActualizaRegistro("recetas_relaciones", "Cantidad", $NuevaCantidad, "ID", $idReceta);
            $this->ActualizaRegistro("productosventa", "Kit", 1, "Referencia", $ReferenciaProducto);
        }
        
        
    }
    /**
     * Calcula los costos de un producto que tiene receta
     * @param type $ReferenciaProducto
     */
    public function CalcularCostosProductoReceta($ReferenciaProducto,$Vector) {
        $DatosProducto= $this->ValorActual("productosventa", "Existencias", " Referencia='$ReferenciaProducto'");
        $Existencias=$DatosProducto["Existencias"];
        $sql="SELECT * FROM recetas_relaciones WHERE ReferenciaProducto='$ReferenciaProducto'";
        $Consulta=$this->Query($sql);
        $CostoTotalItems=0;
        while($DatosReceta = $this->FetchAssoc($Consulta)){
            $ReferenciaInsumo=$DatosReceta["ReferenciaIngrediente"];
            $DatosIngrediente=$this->ValorActual($DatosReceta["TablaIngrediente"], "CostoUnitario", " Referencia='$ReferenciaInsumo'");
        
            $CostoTotalItems=$CostoTotalItems+($DatosIngrediente["CostoUnitario"]*$DatosReceta["Cantidad"]);
        }
        $CostoTotalProducto=$Existencias*$CostoTotalItems;
        $this->ActualizaRegistro("productosventa", "CostoUnitario", $CostoTotalItems, "Referencia", $ReferenciaProducto);
        $this->ActualizaRegistro("productosventa", "CostoTotal", $CostoTotalProducto, "Referencia", $ReferenciaProducto);
        
    }
    /**
     * Fabricar un producto
     * @param type $idProducto
     * @param type $Cantidad
     * @param type $Vector
     */
    public function FabricarProducto($idProducto,$Cantidad,$Vector) {
        $DatosProducto=$this->DevuelveValores("productosventa", "idProductosVenta", $idProducto);
        $DatosKardex["Cantidad"]=$Cantidad;
        $DatosKardex["idProductosVenta"]=$idProducto;
        $DatosKardex["CostoUnitario"]=$DatosProducto['CostoUnitario'];
        $DatosKardex["Existencias"]=$DatosProducto['Existencias'];
        $DatosKardex["Detalle"]="Receta";
        $DatosKardex["idDocumento"]="";
        $DatosKardex["TotalCosto"]=$DatosProducto['CostoUnitario']*$Cantidad;
        $DatosKardex["Movimiento"]="ENTRADA";
        $DatosKardex["CostoUnitarioPromedio"]=$DatosProducto["CostoUnitarioPromedio"];
        $DatosKardex["CostoTotalPromedio"]=$DatosProducto["CostoUnitarioPromedio"]*$Cantidad;
        $this->InserteKardex($DatosKardex);
        $TotalCostoProduccion=$DatosProducto['CostoUnitario']*$Cantidad;
        $ReferenciaProducto=$DatosProducto["Referencia"];
        $Consulta= $this->ConsultarTabla("recetas_relaciones", " WHERE ReferenciaProducto='$ReferenciaProducto'");
        while($DatosInsumo= $this->FetchAssoc($Consulta)){
            
            if($DatosInsumo["Cantidad"]>0){
                if($DatosInsumo["TablaIngrediente"]=="productosventa"){
                    $DatosProductoInsumo=$this->DevuelveValores("productosventa", "Referencia", $DatosInsumo["ReferenciaIngrediente"]);
                    $CantidadInsumo=$DatosInsumo["Cantidad"]*$Cantidad;
                    //$TotalCostoProduccion=$TotalCostoProduccion+($DatosProductoInsumo['CostoUnitario']*$CantidadInsumo);
                    $DatosKardex["Cantidad"]=$CantidadInsumo;
                    $DatosKardex["idProductosVenta"]=$DatosProductoInsumo["idProductosVenta"];
                    $DatosKardex["CostoUnitario"]=$DatosProductoInsumo['CostoUnitario'];
                    $DatosKardex["Existencias"]=$DatosProductoInsumo['Existencias'];
                    $DatosKardex["Detalle"]="Receta";
                    $DatosKardex["idDocumento"]="";
                    $DatosKardex["TotalCosto"]=$DatosProductoInsumo['CostoUnitario']*$CantidadInsumo;
                    $DatosKardex["Movimiento"]="SALIDA";
                    $DatosKardex["CostoUnitarioPromedio"]=$DatosProductoInsumo["CostoUnitarioPromedio"];
                    $DatosKardex["CostoTotalPromedio"]=$DatosProductoInsumo["CostoUnitarioPromedio"]*$CantidadInsumo;
                    $this->InserteKardex($DatosKardex);
                }
                
                if($DatosInsumo["TablaIngrediente"]=="insumos"){
                    $DatosProductoInsumo=$this->DevuelveValores("insumos", "Referencia", $DatosInsumo["ReferenciaIngrediente"]);
                    
                    $CantidadInsumo=$DatosInsumo["Cantidad"]*$Cantidad;
                    //$TotalCostoProduccion=$TotalCostoProduccion+($DatosProductoInsumo['CostoUnitario']*$CantidadInsumo);
                    $this->KardexInsumo("SALIDA", "Fabricacion producto $idProducto", "", $DatosProductoInsumo["Referencia"], $CantidadInsumo, $DatosProductoInsumo["CostoUnitario"], "");
                }
                
            }
        }
        
        if($TotalCostoProduccion>0){
            $ParametroContable=$this->DevuelveValores("parametros_contables", "ID", 22);
            $this->IngreseMovimientoLibroDiario(date("Y-m-d"), "Fabricacion", "", "", "", $ParametroContable["CuentaPUC"],$ParametroContable["NombreCuenta"], "Fabricacion de Producto", "CR", $TotalCostoProduccion, "Fabricacion de Producto", 1, 1, "");
            $ParametroContable=$this->DevuelveValores("parametros_contables", "ID", 23);
            $this->IngreseMovimientoLibroDiario(date("Y-m-d"), "Fabricacion", "", "", "", $ParametroContable["CuentaPUC"],$ParametroContable["NombreCuenta"], "Fabricacion de Producto", "DB", $TotalCostoProduccion, "Fabricacion de Producto", 1, 1, "");
           
        }
        
    }
    
    
    public function KardexInsumo($Movimiento,$Detalle,$idDocumento,$ReferenciaInsumo,$Cantidad,$CostoUnitario,$Vector) {
        $DatosInsumo=$this->DevuelveValores("insumos", "Referencia", $ReferenciaInsumo);
        $Fecha=date("Y-m-d");
        $Saldo=0;
        if($Movimiento=="SALIDA"){
            $Saldo=$DatosInsumo["Existencia"]-$Cantidad;
        }else if($Movimiento=="ENTRADA"){
            $Saldo=$DatosInsumo["Existencia"]+$Cantidad;
        }
        
                
        $Datos["Fecha"]=$Fecha;
        $Datos["Movimiento"]=$Movimiento;
        $Datos["Detalle"]=$Detalle;
        $Datos["idDocumento"]=$idDocumento;
        $Datos["Cantidad"]=$Cantidad;
        $Datos["ValorUnitario"]=$CostoUnitario;
        $Datos["ValorTotal"]=$CostoUnitario*$Cantidad;
        $Datos["ReferenciaInsumo"]=$ReferenciaInsumo;
        
        $sql=$this->getSQLInsert("insumos_kardex", $Datos);
        $this->Query($sql);
        
        $Datos["Movimiento"]="SALDOS";
        $Datos["Cantidad"]=$Saldo;
        $Datos["ValorTotal"]=$CostoUnitario*$Saldo;
        $sql=$this->getSQLInsert("insumos_kardex", $Datos);
        $this->Query($sql);
        
        $this->ActualizaRegistro("insumos", "Existencia", $Saldo, "Referencia", $ReferenciaInsumo);
        $this->ActualizaRegistro("insumos", "CostoUnitario", $CostoUnitario, "Referencia", $ReferenciaInsumo);
        $this->ActualizaRegistro("insumos", "CostoTotal", $CostoUnitario*$Saldo, "Referencia", $ReferenciaInsumo);
        
        
        
    }
    //Fin Clases
}
