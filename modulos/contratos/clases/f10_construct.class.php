<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class F10_Construct extends conexion{
    
    function get_html_general_info($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='ID:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["ID"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["NumeroContrato"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Inicio Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["FechaInicioContrato"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Fin Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["FechaFinalContrato"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Modalidad:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["Modalidad"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["ValorContrato"]).'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='IPS:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<strong>'.$datos_f10["RazonSocial"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='NIT:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["NitIPSContratada"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='NumeroInterno:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="NumeroInterno" style="text-align:right" value="'.$datos_f10["NumeroInterno"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`NumeroInterno`)"></input>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Naturaleza:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<select class="form-control" id="Naturaleza" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`Naturaleza`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sql="SELECT * FROM contratos_naturaleza ";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["naturaleza"]==$datos_f10["Naturaleza"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["naturaleza"].'">'.$datos_consulta["naturaleza"].'</option>';
                        }
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Código de la Sucursal:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["CodigoSucursal"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Nombre de la Sucursal:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["NombreSucursal"].'</strong>';
                $html.='</td>';
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    function get_html_glosas_conciliar($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Glosa X Conciliar:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="ValorGlosaxConciliar" style="text-align:right" value="'.$datos_f10["ValorGlosaxConciliar"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ValorGlosaxConciliar`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Conciliación Glosa:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaConciliacionGlosa" style="text-align:right" value="'.$datos_f10["FechaConciliacionGlosa"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaConciliacionGlosa`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Cumplimiento Acta Glosas:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="CumplimientoActaGlosas" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`CumplimientoActaGlosas`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["CumplimientoActaGlosas"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["CumplimientoActaGlosas"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='ResponsableConciliacionGlosa:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="ResponsableConciliacionGlosa" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ResponsableConciliacionGlosa`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["ResponsableConciliacionGlosa"]=='CONTRATISTA'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="CONTRATISTA">CONTRATISTA</option>';
                        $sel="";
                        if($datos_f10["ResponsableConciliacionGlosa"]=='DEPARTAMENTAL'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="DEPARTAMENTAL">DEPARTAMENTAL</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    function get_html_cuentas_x_pagar($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Saldo Cuentas x Pagar (SEVEN):';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="SaldoCuentaXPagar" style="text-align:right" value="'.$datos_f10["SaldoCuentaXPagar"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`SaldoCuentaXPagar`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Conciliación Cartera:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["FechaConciliacionCartera"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Responsable Conciliacion Cartera:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<strong>'.$datos_f10["nombre_responsable_conciliacion"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Cumplimiento Conciliacion Cartera:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="CumplimientoConciliacionCartera" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`CumplimientoConciliacionCartera`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["CumplimientoConciliacionCartera"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["CumplimientoConciliacionCartera"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                
                $html.='<td colspan="2" style="text-align:left">';
                    $html.='Observaciónes:<br>';
                    $html.='<textarea id="ObservacionesCartera" class="form-control" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ObservacionesCartera`)">'.$datos_f10["ObservacionesCartera"].'</textarea>';
                    
                $html.='</td>';
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    function get_html_liquidacion_contratos($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha de Acta de liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["FechaActaLiquidacion"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Número Acta Liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["NumeroActaLiquidacion"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Acta Liquidación Firmada:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["ActaLiquidacionFirmada"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Firma Acta de Liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["FechaActaLiquidacionFirmada"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Favor Contra:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["ValorFavorContra"]).'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Registra Acta Liquidación Seven:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="RegistroActaLiquidacionSeven" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`RegistroActaLiquidacionSeven`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["RegistroActaLiquidacionSeven"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["RegistroActaLiquidacionSeven"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Acuerdo Pago:';
                $html.='</td>';

                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="AcuerdoPago" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`AcuerdoPago`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["AcuerdoPago"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["AcuerdoPago"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Numero Cuotas Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="NumeroCuotasAcuerdo" style="text-align:right" value="'.$datos_f10["NumeroCuotasAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`NumeroCuotasAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Total Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="ValorTotalAcuerdo" style="text-align:right" value="'.$datos_f10["ValorTotalAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ValorTotalAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Cuota Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="ValorCuotaAcuerdo" style="text-align:right" value="'.$datos_f10["ValorCuotaAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ValorCuotaAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Inicio Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input type="date" style="line-height: 15px;" class="form-control" id="FechaInicioAcuerdo" style="text-align:right" value="'.$datos_f10["FechaInicioAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaInicioAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Fin Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input type="date" style="line-height: 15px;" class="form-control" id="FechaFinAcuerdo" style="text-align:right" value="'.$datos_f10["FechaFinAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaFinAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Saldo Acuerdo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="ValorSaldoAcuerdo" style="text-align:right" value="'.$datos_f10["ValorSaldoAcuerdo"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ValorSaldoAcuerdo`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    function get_html_gestion_liquidacion_contratos($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Responsable de liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["nombre_responsable_liquidacion"].'</strong>';
                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Cargo Responsable de liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["nombre_cargo_responsable_liquidacion"].'</strong>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Lider a Cargo:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control ts-select2" id="LiderAcargoLiquidacion" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`LiderAcargoLiquidacion`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sql="SELECT concat(Nombre,' ',Apellido) as nombre_usuario,idUsuarios as ID FROM usuarios ";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["ID"]==$datos_f10["LiderAcargoLiquidacion"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["ID"].'">'.$datos_consulta["nombre_usuario"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Responsable de cargue de acta:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.= utf8_encode($datos_f10["nombre_responsable_cargue_acta"]);
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Pareto:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.$datos_f10["Pareto"].'</strong>';
                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Pareto Contraloria:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="ParetoContraloria" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ParetoContraloria`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["ParetoContraloria"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["ParetoContraloria"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Municipio:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<select class="form-control ts-select2" id="Municipio" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`Municipio`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $departamento_cod=substr($datos_f10["CodigoSucursal"],0,2);
                        $departamento_cod=str_pad($departamento_cod, 2, "0", STR_PAD_LEFT); 
                        $sql="SELECT * FROM municipios_dane WHERE CodigoDepartamento='$departamento_cod'";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["Ciudad"]==$datos_f10["Municipio"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["Ciudad"].'">'.$datos_consulta["Ciudad"].' '.$datos_consulta["CodigoDane"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Nivel Complejidad:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.utf8_encode($datos_f10["NivelComplejidad"]).'</strong>';                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Objeto del Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.utf8_encode($datos_f10["ObjetoContrato"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Año de Finalización Real:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="AnioFinalizacionContrato" style="text-align:right" value="'.$datos_f10["AnioFinalizacionContrato"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`AnioFinalizacionContrato`)"></input>';
                                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Observaciones Acta de Liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.utf8_encode($datos_f10["ObservacionesLiquidacion"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Saldo Inicial (SEVEN):';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="SaldoInicialSeven" style="text-align:right" value="'.$datos_f10["SaldoInicialSeven"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`SaldoInicialSeven`)"></input>';
                                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Glosa Inicial:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["GlosaInicial"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Glosa Favor:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["GlosaFavor"]).'</strong>';                 
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Glosa X Conciliar:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["GlosaConciliar"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Pendiente Auditoria:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input class="form-control" id="PendienteAuditoria" style="text-align:right" value="'.$datos_f10["PendienteAuditoria"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`PendienteAuditoria`)"></input>';
                                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Devoluciones:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["ValorDevoluciones"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Facturado:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["ValorFacturado"]).'</strong>';                 
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Pagado:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["ValorDevoluciones"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha de envío cruce cartera:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaEnvioCruceCartera" style="text-align:right" value="'.$datos_f10["FechaEnvioCruceCartera"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaEnvioCruceCartera`)"></input>';
                                
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Dias Transcurridos:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<strong>'.number_format($datos_f10["DiasTranscurridos"]).'</strong>';                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Conciliado Asmet:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="ConciliadoAsmet" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ConciliadoAsmet`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["ConciliadoAsmet"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["ConciliadoAsmet"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';               
                $html.='</td>';
            $html.='</tr>';
            
            //Causas
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Causas No Liquidación Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<select class="form-control ts-select2" id="CausaNoLiquidacion" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`CausaNoLiquidacion`)">';
                        $html.='<option value="">Seleccione...</option>';
                        
                        $sql="SELECT * FROM f10_causas";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["ID"]==$datos_f10["CausaNoLiquidacion"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["ID"].'">'.$datos_consulta["Causa"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Gestión de Liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<select class="form-control ts-select2" id="GestionLiquidacion" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`GestionLiquidacion`)">';
                        $html.='<option value="">Seleccione...</option>';
                        
                        $sql="SELECT * FROM f10_gestion";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["ID"]==$datos_f10["GestionLiquidacion"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["ID"].'">'.$datos_consulta["Gestion"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                
                $html.='<td style="text-align:left">';
                    $html.='Observaciones Adicionales:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<textarea id="ObservacionesAdicionales" class="form-control" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ObservacionesAdicionales`)">'.$datos_f10["ObservacionesAdicionales"].'</textarea>';
                    
                    
                $html.='</td>';
            
                $html.='<td style="text-align:left">';
                    $html.='Liquidación inmediata:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<select class="form-control ts-select2" id="ProcesoNoLiquidacion" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ProcesoNoLiquidacion`)">';
                        $html.='<option value="">Seleccione...</option>';
                        
                        $sql="SELECT * FROM f10_liquidacion_inmediata";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["ID"]==$datos_f10["ProcesoNoLiquidacion"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["ID"].'">'.$datos_consulta["Liquidacion"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
                
            $html.='</tr>';
            
            $html.='<tr>';
                
                $html.='<td style="text-align:left">';
                    $html.='Fecha Cruce:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    
                    $html.='<strong>'.$datos_f10["FechaCruce"].'</strong>';
                    
                $html.='</td>';
                
                $html.='<td style="text-align:left">';
                    $html.='Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="Contrato" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`Contrato`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["Contrato"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["Contrato"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';                      
                $html.='</td>';
                
                
                
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='HyL:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="HYL" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`HYL`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["HYL"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["HYL"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';                      
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='Liquidado a 31 de Marzo 2018:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="Liquidado31Marzo2018" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`Liquidado31Marzo2018`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["Liquidado31Marzo2018"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["Liquidado31Marzo2018"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';               
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                
                $html.='<td style="text-align:left">';
                    $html.='Recibe Cartera:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="RecibeCartera" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`RecibeCartera`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["RecibeCartera"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["RecibeCartera"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';               
                $html.='</td>';
                
                $html.='<td style="text-align:left">';
                    $html.='Fecha de Recibo de Cartera:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaRecibeCartera" style="text-align:right" value="'.$datos_f10["FechaRecibeCartera"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaRecibeCartera`)"></input>';
                    
                    
                $html.='</td>';
                
            $html.='</tr>';
            
            $html.='<tr>';
                
                $html.='<td style="text-align:left">';
                    $html.='Se envía oferta?:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                    $html.='<select class="form-control" id="EnviaOferta" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`EnviaOferta`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["EnviaOferta"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["EnviaOferta"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';               
                $html.='</td>';
                
                $html.='<td style="text-align:left">';
                    $html.='Fecha envío Oferta:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaEnviaOferta" style="text-align:right" value="'.$datos_f10["FechaEnviaOferta"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaEnviaOferta`)"></input>';
                    
                    
                $html.='</td>';
                
                
                
            $html.='</tr>';
            
            
            $html.='<tr>';
                
                $html.='<td style="text-align:left">';
                    $html.='Marca Gerencia:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<textarea id="MarcaGerencia" class="form-control" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`MarcaGerencia`)">'.$datos_f10["MarcaGerencia"].'</textarea>';
                    
                    
                $html.='</td>';
                
                $html.='<td style="text-align:left">';
                    $html.='Acta en Archivo:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="ActaArchivada" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ActaArchivada`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["ActaArchivada"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["ActaArchivada"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';                      
                $html.='</td>';
                
            $html.='</tr>';
            
            
            
            $html.='<tr>';
                
                $html.='<td colspan="3" style="text-align:right">';
                    $html.='Estado de Contrato:';
                $html.='</td>';
                $html.='<td style="text-align:left">';
                                       
                    $html.='<select class="form-control" id="estado" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`estado`)">';
                        $html.='<option value="">Seleccione...</option>';
                        
                        $sql="SELECT * FROM f10_estados";
                        $Consulta=$this->Query($sql);
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $sel="";
                            if($datos_consulta["ID"]==$datos_f10["estado"]){
                                $sel="selected";
                            }
                            $html.='<option '.$sel.' value="'.$datos_consulta["ID"].'">'.$datos_consulta["nombre_estado"].'</option>';
                        }
                    $html.='</select>';
                    
                $html.='</td>';
                         
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    function get_html_cargue_actas($datos_f10){
        
        $html='<table class="table table-hover table-striped">';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Responsable AGS o Departamental Cargue:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<select class="form-control" id="ResponsableCargue" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ResponsableCargue`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["ResponsableCargue"]=='CONTRATISTA'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="CONTRATISTA">CONTRATISTA</option>';
                        $sel="";
                        if($datos_f10["ResponsableCargue"]=='DEPARTAMENTAL'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="DEPARTAMENTAL">DEPARTAMENTAL</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Tipo de Operación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="text" class="form-control" style="line-height: 15px;" id="TipoOperacionCargue" style="text-align:right" value="'.$datos_f10["TipoOperacionCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`TipoOperacionCargue`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Número de Ajuste :';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    $html.='<input type="text" class="form-control" style="line-height: 15px;" id="NumeroAjusteCargue" style="text-align:right" value="'.$datos_f10["NumeroAjusteCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`NumeroAjusteCargue`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha Ajuste Cargue:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaAjusteCargue" style="text-align:right" value="'.$datos_f10["FechaAjusteCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaAjusteCargue`)"></input>';
                    
                
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Valor Ajuste Cargue:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="text" class="form-control" style="line-height: 15px;" id="ValorAjusteCargue" style="text-align:right" value="'.$datos_f10["ValorAjusteCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`ValorAjusteCargue`)"></input>';
                    
                
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Saldo Acta Liquidación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="text" class="form-control" style="line-height: 15px;" id="SaldoActaLiquidacionCargue" style="text-align:right" value="'.$datos_f10["SaldoActaLiquidacionCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`SaldoActaLiquidacionCargue`)"></input>';
                    
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Notificación de Cargue?:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<select class="form-control" id="NotificacionCargue" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`NotificacionCargue`)">';
                        $html.='<option value="">Seleccione...</option>';
                        $sel="";
                        if($datos_f10["NotificacionCargue"]=='SI'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="SI">SI</option>';
                        $sel="";
                        if($datos_f10["NotificacionCargue"]=='NO'){
                            $sel="selected";
                        }
                        $html.='<option '.$sel.' value="NO">NO</option>';
                    $html.='</select>';
                $html.='</td>';
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td style="text-align:left">';
                    $html.='Fecha de Notificación:';
                $html.='</td>';
                $html.='<td style="text-align:right">';
                    
                    $html.='<input type="date" class="form-control" style="line-height: 15px;" id="FechaNotificacionCargue" style="text-align:right" value="'.$datos_f10["FechaNotificacionCargue"].'" onchange="editar_campo_f10(`'.$datos_f10["ID"].'`,`FechaNotificacionCargue`)"></input>';
                    
                
                $html.='</td>';
            $html.='</tr>';
            
        $html.='</table>';
        
        return($html);
    }
    
    //Fin Clases
}
