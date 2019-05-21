<?php

if(file_exists("../../../general/clases/ClasesPDFDocumentos.class.php")){
    include_once("../../../general/clases/ClasesPDFDocumentos.class.php");
}

class PDF_ReportesContables extends Documento{
    
    public function EstadosResultadosAnio_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,$Vector ) {
        $TipoReporte="Rango";
        if($idEmpresa=="ALL"){
            $idEmpresaEncabezado=1;
        }
        
        $FechaReporte="Del $FechaInicial al $FechaFinal";
        
        
        $this->PDF_Ini("Estado de Resultados", 8, "",1,"../../../");
        $this->PDF_Encabezado($FechaFinal,$idEmpresaEncabezado, 26, "","","../../../");
        $TotalClases=$this->ArmeTemporalSubCuentas($TipoReporte,$FechaFinal,$FechaInicial,$CentroCosto,$idEmpresa,$Vector);
        
        $html= $this->HTMLEstadoResultadosDetallado($TotalClases,$FechaReporte);
        $this->PDF_Write($html);
             
        $this->PDF_Output("Estado_Resultados_$FechaFinal");
    }
    
    //Armar el html para el estado de resultados
    public function HTMLEstadoResultadosDetallado($TotalClases,$FechaCorte) {
        $Back="#CEE3F6";
        $html='<table id="EstadoResultados" class="table table-bordered table table-hover" cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>Estado del Resultado Integral <br> '.$FechaCorte.'</strong></td></tr>'; 
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>INGRESOS</strong></td></tr>';
        
        ///Se dibujan los ingresos
        $h=0;  
        $Back="white";
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=4");
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
        
        $html.='</tr>';   
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]*(-1));
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]*(-1));
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]*(-1));
           
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           //$html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        $TotalIngresos=0;
        if($TotalClases[4]<>""){
            $TotalIngresos=  number_format($TotalClases[4]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total de Ingresos:</strong></td><td><strong>'.$TotalIngresos.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
         ///Se dibujan los costos de venta y produccion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>COSTOS DE VENTA Y/O PRODUCCION</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=6 OR Clase=7");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        
        $TotalCostos=$TotalClases[6]+$TotalClases[7];
        $TotalCostosN=0;
        if($TotalCostos<>""){
            $TotalCostosN=  number_format($TotalCostos);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Costos de Venta y/o Produccion:</strong></td><td><strong>'.$TotalCostosN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        $UtilidadBruta=0;
        if($TotalClases["UB"]<>""){
            $UtilidadBruta=  number_format($TotalClases["UB"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Utilidad Bruta:</strong></td><td><strong>'.$UtilidadBruta.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        
        ///Se dibujan los gastos y utilidad de la operacion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>GASTOS</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=5");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>';  
        }
        
        
        if($TotalClases[5]<>""){
            $TotalGastos=  number_format($TotalClases[5]);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Gastos:</strong></td><td><strong>'.$TotalGastos.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        $UtilidadOperacional=0;
        if($TotalClases["UO"]<>""){
            $UtilidadOperacional=  number_format($TotalClases["UO"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Utilidad de la Operacion:</strong></td><td><strong>'.$UtilidadOperacional.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        $html.="</table>";
        return($html);
    }
    
    //Crear Estados Financieros en PDF
 
    public function ArmeTemporalSubCuentas($TipoReporte,$FechaFinal,$FechaInicial,$CentroCostos,$EmpresaPro,$Vector){
        
        $Condicion=" WHERE Fecha<='$FechaFinal'";
        if($CentroCostos<>"ALL"){
            $Condicion.=" AND idCentroCosto='$CentroCostos'";
        }
        if($EmpresaPro<>"ALL"){
            $Condicion.=" AND idEmpresa='$EmpresaPro'";
        }
        $Clase=0;
        $this->obCon->VaciarTabla("estadosfinancieros_mayor_temporal");
        $sql="SELECT `CuentaPUC` AS Cuenta,NombreCuenta AS NombreCuenta ,sum(`Neto`) as TotalCuenta FROM `vista_estado_resultados_anio` $Condicion  GROUP BY `CuentaPUC` ORDER BY `CuentaPUC`";
        $Consulta=$this->obCon->Query($sql);
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($DatosMayor["Cuenta"]>0){
                $Cuenta=$DatosMayor["Cuenta"];
                if($TipoReporte=="Corte"){
                    $SaldoAnterior=0;
                }else{
                    $SaldoAnterior=$this->obCon->Sume("vista_estado_resultados_anio", "Neto", "WHERE Fecha<'$FechaInicial' AND `CuentaPUC`='$Cuenta'");
                }
                $Clase=substr($DatosMayor["Cuenta"], 0, 1);
                //$DatosCuenta=$this->obCon->DevuelveValores("cuentas", "idPUC", $DatosMayor["Cuenta"]);
                $tab="estadosfinancieros_mayor_temporal";
                $NumRegistros=7;
                $Columnas[0]="FechaCorte";        $Valores[0]=$FechaFinal;
                $Columnas[1]="Clase";             $Valores[1]=$Clase;
                $Columnas[2]="CuentaPUC";         $Valores[2]=$DatosMayor["Cuenta"];
                $Columnas[3]="NombreCuenta";      $Valores[3]=$DatosMayor["NombreCuenta"];
                $Columnas[4]="Neto";              $Valores[4]=$DatosMayor["TotalCuenta"]-$SaldoAnterior;
                $Columnas[5]="SaldoAnterior";     $Valores[5]=$SaldoAnterior;
                $Columnas[6]="SaldoFinal";        $Valores[6]=$DatosMayor["TotalCuenta"];
                $this->obCon->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
            }
        }
        
        $Activos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='1'");
        $Pasivos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='2'");
        $Patrimonio=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='3'");
        $Ingresos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='4'");
        $GastosOperativos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='5'");
        $CostosVentas=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='6'");
        $CostosProduccion=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='7'");
        
        $TotalClases[1]=$Activos;
        $TotalClases[2]=$Pasivos*(-1);    //Es naturaleza credito por lo tanto debe multiplicarse por -1
        $TotalClases[3]=$Patrimonio*(-1);
        $TotalClases[4]=$Ingresos*(-1);
        $TotalClases[5]=$GastosOperativos;
        $TotalClases[6]=$CostosVentas;
        $TotalClases[7]=$CostosProduccion;
        $TotalClases["RE"]=($TotalClases[1]-$TotalClases[2]-$TotalClases[3])*(-1);//resultado del ejercicio
        $TotalClases["UB"]=$TotalClases[4]-$TotalClases[6]-$TotalClases[7];//Utilidad Bruta
        $TotalClases["UO"]=$TotalClases["UB"]-$TotalClases[5]; //Utilidad de la Operacion
             
        return($TotalClases);
    }
    
    /**
     * Fin Clase
     */
}
