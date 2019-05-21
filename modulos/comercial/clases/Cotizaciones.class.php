<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Cotizaciones extends ProcesoVenta{
    /**
     * Crea una compra
     * @param type $Fecha
     * @param type $idTercero
     * @param type $Observaciones     
     * @param type $Vector
     * @return type
     */
    public function CrearCotizacion($Fecha, $idTercero, $Observaciones,$Vector ) {
        
        //////Creo la compra            
        $tab="cotizacionesv5";
        $Datos["Fecha"]=$Fecha;
        $Datos["Clientes_idClientes"]=$idTercero;
        $Datos["Usuarios_idUsuarios"]=$_SESSION['idUser'];
        $Datos["Observaciones"]=$Observaciones;
        $Datos["Estado"]="Abierta";
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
        $idCotizacion=$this->ObtenerMAX($tab,"ID", 1,"");       
        
        return $idCotizacion;
    }
    /**
     * Agrega un item a una cotizacion
     * @param type $idCotizacion
     * @param type $Cantidad
     * @param type $Multiplicador
     * @param type $idProducto
     * @param type $TablaItem
     * @param type $ValorUnitario
     * @param type $Vector
     */
    public function AgregaItemCotizacion($idCotizacion,$Cantidad,$Multiplicador,$idProducto,$TablaItem,$ValorUnitario,$Vector){
        
            $DatosProductoGeneral=$this->DevuelveValores($TablaItem, "idProductosVenta", $idProducto);
            $DatosDepartamento=$this->DevuelveValores("prod_departamentos", "idDepartamentos", $DatosProductoGeneral["Departamento"]);
            $DatosTablaItem=$this->DevuelveValores("tablas_ventas", "NombreTabla", $TablaItem);
            $TipoItem=$DatosDepartamento["TipoItem"];
            $sql="select * from fechas_descuentos where (Departamento = '$DatosProductoGeneral[Departamento]' OR Departamento ='0') AND (Sub1 = '$DatosProductoGeneral[Sub1]' OR Sub1 ='0') AND (Sub2 = '$DatosProductoGeneral[Sub2]' OR Sub2 ='0') ORDER BY idFechaDescuentos DESC LIMIT 1 ";
            $reg=$this->Query($sql);
            $reg=$this->FetchArray($reg);
            $Porcentaje=$reg["Porcentaje"];
            $Departamento=$reg["Departamento"];
            $FechaDescuento=$reg["Fecha"];

            $impuesto=$DatosProductoGeneral["IVA"];
            $impuesto=$impuesto+1;
            if($DatosTablaItem["IVAIncluido"]=="SI"){
                $ValorUnitario=round($ValorUnitario/$impuesto,2);

            }
            if($Porcentaje>0 and $FechaDescuento==$fecha){

                $Porcentaje=(100-$Porcentaje)/100;
                $ValorUnitario=round($ValorUnitario*$Porcentaje,2);

            }
            
            if($DatosProductoGeneral["IVA"]<>"E"){
                $PorcentajeIVA=($DatosProductoGeneral["IVA"]*100)."%";
            }else{
                $PorcentajeIVA="Exc";
            }
            $Subtotal=$ValorUnitario*$Cantidad*$Multiplicador;
            $IVA=round(($impuesto-1)*$Subtotal,2);
            $Total=$Subtotal+$IVA;
            
            $tab="cot_itemscotizaciones";
            $Datos["NumCotizacion"]=$idCotizacion;
            $Datos["Descripcion"]=$DatosProductoGeneral["Nombre"];
            $Datos["Referencia"]=$DatosProductoGeneral["Referencia"];
            $Datos["TablaOrigen"]=$TablaItem;
            $Datos["ValorUnitario"]=$ValorUnitario;
            $Datos["Cantidad"]=$Cantidad;
            $Datos["Multiplicador"]=$Multiplicador;
            $Datos["Subtotal"]=$Subtotal;
            $Datos["IVA"]=$IVA;
            $Datos["Total"]=$Total;
            $Datos["PorcentajeIVA"]=$PorcentajeIVA;
            $Datos["Descuento"]=0;
            $Datos["ValorDescuento"]=0;
            $Datos["PrecioCosto"]=$DatosProductoGeneral["CostoUnitario"];
            $Datos["SubtotalCosto"]=$DatosProductoGeneral["CostoUnitario"]*$Cantidad*$Multiplicador;
            $Datos["TipoItem"]=$DatosDepartamento["TipoItem"];
            $Datos["Devuelto"]="";
            $Datos["CuentaPUC"]=$DatosProductoGeneral["CuentaPUC"];            
            $Datos["Departamento"]=$DatosProductoGeneral["Departamento"];
            $Datos["SubGrupo1"]=$DatosProductoGeneral["Sub1"];
            $Datos["SubGrupo2"]=$DatosProductoGeneral["Sub2"];
            $Datos["SubGrupo3"]=$DatosProductoGeneral["Sub3"];
            $Datos["SubGrupo4"]=$DatosProductoGeneral["Sub4"];
            $Datos["SubGrupo5"]=$DatosProductoGeneral["Sub5"];
            
            $sql=$this->getSQLInsert($tab, $Datos);
            $this->Query($sql);
            
            
        }
        /**
         * Registra el anticipo o abono a una cotizacion
         * @param type $fecha
         * @param type $idCotizacion
         * @param type $CuentaDestino
         * @param type $Vector
         */
        public function AnticipoCotizacion($Fecha,$idCotizacion,$ValorAnticipo,$CuentaDestino,$CentroCosto,$Vector) {
            $DatosCotizacion=$this->DevuelveValores("cotizacionesv5", "ID", $idCotizacion);
            $DatosCliente=$this->DevuelveValores("clientes", "idClientes", $DatosCotizacion["Clientes_idClientes"]);
            $Concepto="Anticipo a la cotizacion $idCotizacion";
            $idComprobanteIngreso=$this->RegistreAnticipo($DatosCotizacion["Clientes_idClientes"], $ValorAnticipo, $CuentaDestino, $CentroCosto, $Concepto, $_SESSION["idUser"]);
            
            $Tabla="cotizaciones_anticipos";
            $Datos["Fecha"]=$Fecha;
            $Datos["Valor"]=$ValorAnticipo;
            $Datos["idCotizacion"]=$idCotizacion;
            $Datos["idComprobanteIngreso"]=$idComprobanteIngreso;
            $Datos["idUsuario"]=$_SESSION["idUser"];
            $Datos["Estado"]="Abierto";
            $sql=$this->getSQLInsert($Tabla, $Datos);
            $this->Query($sql);            
            return($idComprobanteIngreso);
            
        }
        
       /**
        * Editar item a una cotizacion
        * @param type $idItem
        * @param type $Cantidad
        * @param type $Multiplicador
        * @param type $ValorAcordado
        * @param type $Vector
        */
        public function EditarItemCotizacion($idItem,$Cantidad,$Multiplicador,$ValorAcordado,$Vector) {
            $DatosPreventa= $this->DevuelveValores('cot_itemscotizaciones',"ID",$idItem);
            $DatosProductos=$this->DevuelveValores($DatosPreventa["TablaOrigen"],"Referencia",$DatosPreventa["Referencia"]);
            //$ValorAcordado=round($ValorAcordado/(1+$DatosProductos["IVA"]),2);
            $Subtotal=$ValorAcordado*$Cantidad*$Multiplicador;
            $IVA=($Subtotal*$DatosProductos["IVA"]);
            $SubtotalCosto=$DatosProductos["CostoUnitario"]*$Cantidad;
            $Total=$Subtotal+$IVA;
            $filtro="ID";

            $this->ActualizaRegistro("cot_itemscotizaciones","SubTotal", $Subtotal, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","IVA", $IVA, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","SubtotalCosto", $SubtotalCosto, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","Total", $Total, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","ValorUnitario", $ValorAcordado, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","Cantidad", $Cantidad, $filtro, $idItem);
            $this->ActualizaRegistro("cot_itemscotizaciones","Multiplicador", $Multiplicador, $filtro, $idItem);

        }
        
        public function CopiarItemsCotizacion($idCotizacionOrigen,$idCotizacionDestino) {
            $sql="INSERT INTO `cot_itemscotizaciones`( `NumCotizacion`, `Descripcion`, `Referencia`, `TablaOrigen`, `ValorUnitario`, `Cantidad`, `Multiplicador`, `Subtotal`, `IVA`, `Total`, `Descuento`, `ValorDescuento`, `PrecioCosto`, `SubtotalCosto`, `TipoItem`, `PorcentajeIVA`, `idPorcentajeIVA`, `Departamento`, `SubGrupo1`, `SubGrupo2`, `SubGrupo3`, `SubGrupo4`, `SubGrupo5`, `Devuelto`, `CuentaPUC`) "
                    . "SELECT '$idCotizacionDestino', Descripcion, Referencia, TablaOrigen, ValorUnitario, Cantidad, Multiplicador, Subtotal, IVA, Total, Descuento, ValorDescuento, PrecioCosto, SubtotalCosto, TipoItem, PorcentajeIVA, idPorcentajeIVA, Departamento, SubGrupo1, SubGrupo2, SubGrupo3, SubGrupo4, SubGrupo5, Devuelto, CuentaPUC FROM `cot_itemscotizaciones` "
                    . "WHERE NumCotizacion='$idCotizacionOrigen' ";
            $this->Query($sql);
        }
    
    /**
     * Fin Clase
     */
}
