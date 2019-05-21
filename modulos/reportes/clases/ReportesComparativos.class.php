<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Reportes extends ProcesoVenta{
    /***
     * Obtengo la clasificacion del inventario agrupado por departamento
     */
    function ObtengaClasificacionXDepartamento() {
        $sql="SELECT Departamento,
            (SELECT Nombre FROM prod_departamentos WHERE productosventa.Departamento=prod_departamentos.idDepartamentos LIMIT 1) AS NombreDepartamento,
            ROUND(SUM(Existencias)) as Existencias, ROUND(SUM(CostoTotal)) as CostoTotal, ROUND(SUM(PrecioVenta*Existencias)) as PrecioVentaTotal 
              FROM productosventa GROUP BY Departamento";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartemento=$DatosConsulta["Departamento"];            
            $Clasificacion[$idDepartemento]["Existencias"]=$DatosConsulta["Existencias"];
            $Clasificacion[$idDepartemento]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            $Clasificacion[$idDepartemento]["NombreDeparmento"]=$DatosConsulta["NombreDepartamento"];
            $Clasificacion[$idDepartemento]["PrecioVentaTotal"]=$DatosConsulta["PrecioVentaTotal"];
        } 
        return($Clasificacion);
    }
    /**
     * Obtengo los datos del inventario agrupado por subgrupo 1
     * @return type
     */
    function ObtengaClasificacionXSub1() {
        $sql="SELECT Departamento,Sub1,
            (SELECT Nombre FROM prod_departamentos WHERE productosventa.Departamento=prod_departamentos.idDepartamentos LIMIT 1) AS NombreDepartamento,
            (SELECT NombreSub1 FROM prod_sub1 WHERE productosventa.Sub1=prod_sub1.idSub1 LIMIT 1) AS NombreSub1,
            ROUND(SUM(Existencias)) as Existencias, ROUND(SUM(CostoTotal)) as CostoTotal , ROUND(SUM(PrecioVenta*Existencias)) as PrecioVentaTotal 
              FROM productosventa GROUP BY Sub1";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idSub1=$DatosConsulta["Sub1"];            
            $Clasificacion[$idSub1]["Existencias"]=$DatosConsulta["Existencias"];
            $Clasificacion[$idSub1]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            $Clasificacion[$idSub1]["Departamento"]=$DatosConsulta["Departamento"];
            $Clasificacion[$idSub1]["NombreDeparmento"]=$DatosConsulta["NombreDepartamento"];
            $Clasificacion[$idSub1]["NombreSub1"]=$DatosConsulta["NombreSub1"];
            $Clasificacion[$idSub1]["Sub1"]=$DatosConsulta["Sub1"];
            $Clasificacion[$idSub1]["PrecioVentaTotal"]=$DatosConsulta["PrecioVentaTotal"];
        } 
        return($Clasificacion);
    }
    /**
     * Obtengo los datos del inventario agrupado por subgrupo 2
     * @return type
     */
    function ObtengaClasificacionXSub2() {
        $sql="SELECT Departamento,Sub1,Sub2,
            (SELECT Nombre FROM prod_departamentos WHERE productosventa.Departamento=prod_departamentos.idDepartamentos LIMIT 1) AS NombreDepartamento,
            (SELECT NombreSub1 FROM prod_sub1 WHERE productosventa.Sub1=prod_sub1.idSub1 LIMIT 1) AS NombreSub1,
            (SELECT NombreSub2 FROM prod_sub2 WHERE productosventa.Sub2=prod_sub2.idSub2 LIMIT 1) AS NombreSub2,
            ROUND(SUM(Existencias)) as Existencias, ROUND(SUM(CostoTotal)) as CostoTotal, ROUND(SUM(PrecioVenta*Existencias)) as PrecioVentaTotal  
              FROM productosventa GROUP BY Sub2";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idSub2=$DatosConsulta["Sub2"];            
            $Clasificacion[$idSub2]["Existencias"]=$DatosConsulta["Existencias"];
            $Clasificacion[$idSub2]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            $Clasificacion[$idSub2]["Departamento"]=$DatosConsulta["Departamento"];
            $Clasificacion[$idSub2]["NombreDeparmento"]=$DatosConsulta["NombreDepartamento"];
            $Clasificacion[$idSub2]["NombreSub1"]=$DatosConsulta["NombreSub1"];
            $Clasificacion[$idSub2]["Sub1"]=$DatosConsulta["Sub1"];
            $Clasificacion[$idSub2]["NombreSub2"]=$DatosConsulta["NombreSub2"];
            $Clasificacion[$idSub2]["Sub2"]=$DatosConsulta["Sub2"];
            $Clasificacion[$idSub2]["PrecioVentaTotal"]=$DatosConsulta["PrecioVentaTotal"];
        } 
        return($Clasificacion);
    }
    /**
     * Obtengo la informacion de compras agrupado por Departamentos
     * @return type
     */
    function ObtengaComprasXDepartamento($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha<='$FechaFinal' ";
        $sql="SELECT Departamento,ROUND(SUM(Cantidad)) as Cantidad,
            ROUND(SUM(Subtotal)) as Subtotal, ROUND(SUM(Impuestos)) as IVA, ROUND(SUM(Total)) as Total
              FROM vista_compras_productos $Condicion GROUP BY Departamento";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["Departamento"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
        } 
        return($Clasificacion);
    }
    
    /**
     * Obtengo la informacion de compras agrupado por Subgrupo1
     * @return type
     */
    function ObtengaComprasXSub1($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha<='$FechaFinal' ";
        $sql="SELECT Sub1,ROUND(SUM(Cantidad)) as Cantidad,
            ROUND(SUM(Subtotal)) as Subtotal, ROUND(SUM(Impuestos)) as IVA, ROUND(SUM(Total)) as Total
              FROM vista_compras_productos $Condicion GROUP BY Sub1";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["Sub1"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
        } 
        return($Clasificacion);
    }
    
    /**
     * Obtengo la informacion de compras agrupado por Subgrupo 2
     * @return type
     */
    function ObtengaComprasXSub2($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha<='$FechaFinal' ";
        $sql="SELECT Sub2,ROUND(SUM(Cantidad)) as Cantidad,
            ROUND(SUM(Subtotal)) as Subtotal, ROUND(SUM(Impuestos)) as IVA, ROUND(SUM(Total)) as Total
              FROM vista_compras_productos $Condicion GROUP BY Sub2";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["Sub2"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
        } 
        return($Clasificacion);
    }
    
    /**
     * Obtengo la informacion de ventas agrupado por Departamentos
     * @return type
     */
    function ObtengaVentasXDepartamento($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE FechaFactura>='$FechaInicial' AND FechaFactura<='$FechaFinal' ";
        $sql="SELECT Departamento,ROUND(SUM(Cantidad)) as Cantidad,ROUND(SUM(SubtotalCosto)) as CostoTotal,
            ROUND(SUM(SubtotalItem)) as Subtotal, ROUND(SUM(IVAItem)) as IVA, ROUND(SUM(TotalItem)) as Total
              FROM facturas_items $Condicion GROUP BY Departamento";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["Departamento"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
            $Clasificacion[$idDepartamento]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            
        } 
        return($Clasificacion);
    }
    
    /**
     * Obtengo la informacion de ventas agrupado por Departamentos
     * @return type
     */
    function ObtengaVentasXSub1($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE FechaFactura>='$FechaInicial' AND FechaFactura<='$FechaFinal' ";
        $sql="SELECT SubGrupo1,ROUND(SUM(Cantidad)) as Cantidad,ROUND(SUM(SubtotalCosto)) as CostoTotal,
            ROUND(SUM(SubtotalItem)) as Subtotal, ROUND(SUM(IVAItem)) as IVA, ROUND(SUM(TotalItem)) as Total
              FROM facturas_items  $Condicion GROUP BY SubGrupo1";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["SubGrupo1"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
            $Clasificacion[$idDepartamento]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            
        } 
        return($Clasificacion);
    }
    
    /**
     * Obtengo la informacion de ventas agrupado por Departamentos
     * @return type
     */
    function ObtengaVentasXSub2($FechaInicial,$FechaFinal) {
        $Condicion=" WHERE FechaFactura>='$FechaInicial' AND FechaFactura<='$FechaFinal' ";
        $sql="SELECT SubGrupo2,ROUND(SUM(Cantidad)) as Cantidad,ROUND(SUM(SubtotalCosto)) as CostoTotal,
            ROUND(SUM(SubtotalItem)) as Subtotal, ROUND(SUM(IVAItem)) as IVA, ROUND(SUM(TotalItem)) as Total
              FROM facturas_items $Condicion GROUP BY SubGrupo2";
        $Consulta=$this->Query($sql);
        $Clasificacion=[];
        
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $idDepartamento=$DatosConsulta["SubGrupo2"];            
            $Clasificacion[$idDepartamento]["Subtotal"]=$DatosConsulta["Subtotal"];
            $Clasificacion[$idDepartamento]["IVA"]=$DatosConsulta["IVA"];
            $Clasificacion[$idDepartamento]["Total"]=$DatosConsulta["Total"];
            $Clasificacion[$idDepartamento]["Cantidad"]=$DatosConsulta["Cantidad"];
            $Clasificacion[$idDepartamento]["CostoTotal"]=$DatosConsulta["CostoTotal"];
            
        } 
        return($Clasificacion);
    }
    /**
     * Obtengo el JSON Completo
     * @param type $Clasificacion
     * @param type $Compras
     * @param type $Ventas
     * @return type
     */
    function ObtengaDatosCompletos($Clasificacion,$Compras,$Ventas) {
        $DatosCompletos=[];
       
        foreach ($Clasificacion as $key => $value) {
            
            $Dato="";
            
            $DatosCompletos[$key]["NombreDepartamento"]=$Clasificacion[$key]["NombreDeparmento"];
            //$DatosCompletos[$key]["Departamento"]=$Clasificacion[$key]["Departamento"];
            $DatosCompletos[$key]["Existencias"]=$Clasificacion[$key]["Existencias"];
            $DatosCompletos[$key]["CostoTotal"]=$Clasificacion[$key]["CostoTotal"];
            $DatosCompletos[$key]["PrecioVentaTotal"]=$Clasificacion[$key]["PrecioVentaTotal"];
            if(isset($Clasificacion[$key]["NombreSub1"])){
                $Dato=$Clasificacion[$key]["NombreSub1"];
            }            
            $DatosCompletos[$key]["NombreSub1"]=$Dato;
            $Dato="";
            if(isset($Clasificacion[$key]["Sub1"])){
                $Dato=$Clasificacion[$key]["Sub1"];
            }   
            $DatosCompletos[$key]["Sub1"]=$Dato;
            $Dato="";
            if(isset($Clasificacion[$key]["NombreSub2"])){
                $Dato=$Clasificacion[$key]["NombreSub2"];
            }   
            $DatosCompletos[$key]["NombreSub2"]=$Dato;
            $Dato="";
            
            if(isset($Clasificacion[$key]["Sub2"])){
                $Dato=$Clasificacion[$key]["Sub2"];
            }   
            $DatosCompletos[$key]["Sub2"]=$Dato;
            $Dato="0";
            if(isset($Compras[$key]["Cantidad"])){
                $Dato=$Compras[$key]["Cantidad"];
            }   
            $DatosCompletos[$key]["CantidadComprada"]=$Dato;
            $Dato="0";
            
            if(isset($Ventas[$key]["Cantidad"])){
                $Dato=$Ventas[$key]["Cantidad"];
            }   
            $DatosCompletos[$key]["CantidadVendida"]=$Dato;
            $Dato="0";
            
            if(isset($Ventas[$key]["Subtotal"])){
                $Dato=$Ventas[$key]["Subtotal"];
            }   
            $DatosCompletos[$key]["SubtotalVentas"]=$Dato;
            $Dato="0";
            
            if(isset($Ventas[$key]["IVA"])){
                $Dato=$Ventas[$key]["IVA"];
            }   
            $DatosCompletos[$key]["IVAVentas"]=$Dato;
            $Dato="0";
            
            if(isset($Ventas[$key]["Total"])){
                $Dato=$Ventas[$key]["Total"];
            }   
            $DatosCompletos[$key]["TotalVentas"]=$Dato;
            $Dato="0";
            
            
            if(isset($Compras[$key]["Subtotal"])){
                $Dato=$Compras[$key]["Subtotal"];
            }   
            $DatosCompletos[$key]["SubtotalCompras"]=$Dato;
            $Dato="0";
            
            if(isset($Compras[$key]["IVA"])){
                $Dato=$Compras[$key]["IVA"];
            }   
            $DatosCompletos[$key]["IVACompras"]=$Dato;
            $Dato="0";
            
            if(isset($Compras[$key]["Total"])){
                $Dato=$Compras[$key]["Total"];
            }   
            $DatosCompletos[$key]["TotalCompras"]=$Dato;
            $Dato="0";
            
            if(isset($Ventas[$key]["CostoTotal"])){
                $Dato=$Ventas[$key]["CostoTotal"];
            }   
            $DatosCompletos[$key]["TotalCostoVentas"]=$Dato;
            $Dato="0";
            
            
        }
        
        return($DatosCompletos);
         
    }
    /**
     * Fin Clase
     */
}
