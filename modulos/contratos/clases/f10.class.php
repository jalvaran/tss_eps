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
        
class F10 extends conexion{
    
    function crear_contratos_f10($idUser){
        $sql="INSERT INTO f10 (contrato_id,NitIPSContratada,RazonSocial,Modalidad,NumeroContrato,llaveCargue,ValorContrato,FechaInicioContrato,FechaFinalContrato,NivelComplejidad,ObjetoContrato)

                    SELECT t1.ID,t1.NitIPSContratada,(SELECT t3.Nombre FROM ips t3 WHERE t3.NIT=t1.NitIPSContratada LIMIT 1),t1.TipoContrato,t1.ContratoEquivalente,concat(t1.NitIPSContratada,'_',t1.Contrato),t1.ValorContrato,t1.FechaInicioContrato,t1.FechaFinalContrato,t1.NivelComplejidad,t1.Objeto_Contrato FROM contratos t1 
                    WHERE exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t1.F10=0 AND t2.idUsuario='$idUser')";
        /*
        $sql="INSERT INTO f10 (contrato_id,NitIPSContratada,RazonSocial,Modalidad,NumeroContrato,llaveCargue,ValorContrato,FechaInicioContrato,FechaFinalContrato,NivelComplejidad,ObjetoContrato)

                    SELECT t1.ID,t1.NitIPSContratada,(SELECT t3.Nombre FROM ips t3 WHERE t3.NIT=t1.NitIPSContratada LIMIT 1),t1.TipoContrato,t1.ContratoEquivalente,concat(t1.NitIPSContratada,'_',t1.Contrato),t1.ValorContrato,t1.FechaInicioContrato,t1.FechaFinalContrato,t1.NivelComplejidad,t1.Objeto_Contrato FROM contratos t1 
                    ";
         * 
         */
        $this->Query($sql);
        
    }
    
    function actualizar_marca_f10_contratos($idUser) {
        $sql="UPDATE contratos t1 set t1.F10=1  
                WHERE exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t1.F10=0 AND t2.idUsuario='$idUser')";
        //$sql="UPDATE contratos t1 set t1.F10=1 ";
             
        $this->Query($sql);
    }
    
    function actualizar_control_cambios_f10($idUser) {
        
        
        $sql="INSERT INTO f10_control_cambios  
                SELECT * FROM f10 t1 
                WHERE t1.f10_control_cambios_marca=0 
                AND exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t2.idUsuario='$idUser')";
            
        $this->Query($sql);
        
        $sql="UPDATE f10 t1 set t1.f10_control_cambios_marca=1  
                WHERE exists(SELECT 1 FROM relacion_usuarios_ips t2 WHERE t1.NitIPSContratada=t2.idIPS AND t2.idUsuario='$idUser')";
            
        $this->Query($sql);
        
    }
    
    function actualizar_valores_ts_f10($datos_f10,$idUser){
        $nit_ips=$datos_f10["NitIPSContratada"];
        
        $datos_ips=$this->DevuelveValores("ips", "NIT", $datos_f10["NitIPSContratada"]);
        
        $datos_contrato=$this->DevuelveValores("contratos", "ID", $datos_f10["contrato_id"]);
        $contrato=$datos_f10["NumeroContrato"];
        $db_ips=$datos_ips["DataBase"];
        $f10_id=$datos_f10["ID"];
        if($db_ips==''){
            $Datos["f10_actualizacion_campos_automaticos"]=1;
            $sql=$this->getSQLUpdate("f10", $Datos);
            $sql.="WHERE ID='$f10_id'";
            $this->Query($sql);
            return(0);
        }
        
        $sql="SELECT (t1.ID) acta_id,(FechaFirma) as fecha_acta_conciliacion,SUBSTRING(FechaRegistro,1,10) as fecha_cruce,idUser AS ResponsableConciliacionCartera, 
                        t1.Soporte as soporte_acta_conciliacion 
                        FROM actas_conciliaciones t1  
                        inner join actas_conciliaciones_contratos t2
                    WHERE t1.NIT_IPS='$nit_ips' AND t2.NumeroContrato='$contrato' ORDER BY t1.ID DESC LIMIT 1   
                  ";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $acta_id=$datos_consulta["acta_id"];
        $responsable_conciliacion=$datos_consulta["ResponsableConciliacionCartera"];
        
        $soporte_acta_conciliacion=$datos_consulta["soporte_acta_conciliacion"];
        if($datos_consulta["fecha_acta_conciliacion"]==''){
            $fecha_acta_conciliacion="0000-00-00";
        }else{
            $fecha_acta_conciliacion=$datos_consulta["fecha_acta_conciliacion"];
        }
        if($datos_consulta["fecha_cruce"]==''){
            $fecha_cruce="0000-00-00";
        }else{
            $fecha_cruce= ($datos_consulta["fecha_cruce"]);
            
        }
        
        $datos_actualizacion="";
        
        if($acta_id<>''){
            $sql="SELECT SUM(ValorSegunEPS) AS ValorFavorContra,SUM(TotalGlosaInicial) AS GlosaInicial,SUM(TotalGlosaFavor) AS GlosaFavor,
                    SUM(GlosaXConciliar) AS GlosaConciliar,SUM(TotalDevoluciones) AS ValorDevoluciones,SUM(ValorDocumento) AS ValorFacturado
                    ,SUM(TotalPagos) AS ValorPagado 
                    FROM $db_ips.actas_conciliaciones_items WHERE NumeroContrato='$contrato' AND idActaConciliacion='$acta_id'";
            
            $datos_actualizacion=$this->FetchAssoc($this->Query($sql));
        }
        
        if($acta_id==''){
            $sql="SELECT COLUMN_NAME 
                    FROM information_schema.COLUMNS 
                    WHERE 
                        TABLE_SCHEMA = '$db_ips' 
                    AND TABLE_NAME = 'hoja_de_trabajo' 
                    AND COLUMN_NAME = 'TipoNegociacion' ";
            $Verificacion=$this->FetchAssoc($this->Query($sql));
            if($Verificacion["COLUMN_NAME"]<>""){
                $sql="SELECT SUM(ValorSegunEPS) AS ValorFavorContra,SUM(TotalGlosaInicial) AS GlosaInicial,SUM(TotalGlosaFavor) AS GlosaFavor,
                        SUM(GlosaXConciliar) AS GlosaConciliar,SUM(TotalDevoluciones) AS ValorDevoluciones,SUM(ValorDocumento) AS ValorFacturado
                        ,SUM(TotalPagos) AS ValorPagado 
                        FROM $db_ips.hoja_de_trabajo WHERE NumeroContrato='$contrato'";
                $datos_actualizacion=$this->FetchAssoc($this->Query($sql));
            }
        }
        
        $Datos=[];
        $realiza_cruce='NO';
        if(isset($datos_actualizacion["ValorFavorContra"])){
            if(!is_null($datos_actualizacion["ValorFavorContra"])){
                $realiza_cruce='SI';
                foreach ($datos_actualizacion as $key => $value) {
                    if($datos_actualizacion[$key]<>$datos_f10[$key]){                    
                        $Datos[$key]=$value;
                    }                
                }

            }
        }
        if($soporte_acta_conciliacion<>$datos_f10["soporte_acta_conciliacion"]){
            $Datos["soporte_acta_conciliacion"]=$datos_consulta["soporte_acta_conciliacion"];
        }
        if($datos_f10["CodigoSucursal"]==0){
            $sql="CREATE INDEX IF NOT EXISTS NumeroContrato ON $db_ips.historial_carteracargada_eps(NumeroContrato );";
            $this->Query($sql);
            $sql="SELECT CodigoSucursal,Sucursal,DepartamentoRadicacion FROM $db_ips.historial_carteracargada_eps WHERE NumeroContrato='$contrato' AND Nit_IPS='$nit_ips' LIMIT 1";
            
            $datos_sucursal=$this->FetchAssoc($this->Query($sql));
            if($datos_sucursal["CodigoSucursal"]>0){
                $Datos["CodigoSucursal"]=$datos_sucursal["CodigoSucursal"];
                $Datos["Municipio"]=$datos_sucursal["Sucursal"];
                $Datos["NombreSucursal"]=$datos_sucursal["DepartamentoRadicacion"];
            }
        }
        
        //Verificar si deben ser automaticos
        /*
        if(isset($Datos["GlosaConciliar"])){
            if($datos_f10["ValorGlosaxConciliar"]<>$Datos["GlosaConciliar"]){
                $Datos["ValorGlosaxConciliar"]=$Datos["GlosaConciliar"];
            }
        }
         * 
         */
        
        /*
        if(isset($Datos["ValorFavorContra"])){
            if($datos_f10["SaldoCuentaXPagar"]<>$Datos["ValorFavorContra"]){
                $Datos["SaldoCuentaXPagar"]=$Datos["ValorFavorContra"];
            }
        }
         * 
         */
        
        
        if($fecha_acta_conciliacion<>$datos_f10["FechaConciliacionCartera"]){
            $Datos["FechaConciliacionCartera"]=$fecha_acta_conciliacion;
        }
        if($fecha_cruce<>$datos_f10["FechaCruce"]){
            $Datos["FechaCruce"]=$fecha_cruce;
        }
        if($responsable_conciliacion<>$datos_f10["ResponsableConciliacionCartera"]){
            $Datos["ResponsableConciliacionCartera"]=$responsable_conciliacion;
        }
        
        $sql="SELECT IdentificadorActaEPS as NumeroActaLiquidacion,SUBSTRING(FechaRegistro,1,10) as FechaActaLiquidacion,(FechaFirma) as FechaActaLiquidacionFirmada,
                        idUser AS ResponsableLiquidacion,Observaciones as ObservacionesLiquidacion,
                        Soporte as soporte_acta_liquidacion 
                        FROM actas_liquidaciones t1  
                        inner join actas_liquidaciones_contratos t2
                    WHERE t1.Estado=1 and t1.NIT_IPS='$nit_ips' AND t2.idContrato='$contrato' ORDER BY t1.ID DESC LIMIT 1   
                  ";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $DatosActaLiquidacion=$datos_consulta;
        
        if($DatosActaLiquidacion["soporte_acta_liquidacion"]<>$datos_f10["soporte_acta_liquidacion"]){
            $Datos["soporte_acta_liquidacion"]=$DatosActaLiquidacion["soporte_acta_liquidacion"];
        }
        $ActaLiquidacionFirmada="NO";
        if($DatosActaLiquidacion["FechaActaLiquidacionFirmada"]<>'0000:00:00' AND $DatosActaLiquidacion["FechaActaLiquidacionFirmada"]<>''){
            $DatosActaLiquidacion["ActaLiquidacionFirmada"]="SI";
            
        }
        $CargoResponsableLiquidacion="";
        if($DatosActaLiquidacion["ResponsableLiquidacion"]>0){
            
            $ResponsableLiquidacion=$DatosActaLiquidacion["ResponsableLiquidacion"];
            $sql="SELECT (select ID FROM empresa_cargos t2 WHERE t2.ID=t1.Cargo LIMIT 1) AS cargo FROM usuarios t1 WHERE t1.idUsuarios='$ResponsableLiquidacion'";
            $datos_cargo=$this->FetchAssoc($this->Query($sql));
            $DatosActaLiquidacion["CargoResponsableLiquidacion"]=$datos_cargo["cargo"];
            

        }
        
        $DatosActaLiquidacion["RealizaCruceCartera"]=$realiza_cruce;
        
        foreach ($DatosActaLiquidacion as $key => $value) {
            if($DatosActaLiquidacion[$key]<>$datos_f10[$key]){
                $Datos[$key]=$value;
            }
        }
        
        if($idUser<>$datos_f10["idUser"]){
            $Datos["idUser"]=$idUser;
        }
        
        $keys_contrato=["NitIPSContratada","TipoContrato","ContratoEquivalente","ValorContrato","FechaInicioContrato","FechaFinalContrato","NivelComplejidad","Objeto_Contrato","NivelPrioridad"];
        $keys_f10=["NitIPSContratada","Modalidad","NumeroContrato","ValorContrato","FechaInicioContrato","FechaFinalContrato","NivelComplejidad","ObjetoContrato","Pareto"];
        
        foreach ($keys_f10 as $key => $value) {
            $key_contrato=$keys_contrato[$key];
            if($datos_f10[$value] <> $datos_contrato[$key_contrato]){
                $Datos[$value]=$datos_contrato[$key_contrato];
            }
        }
        
        $total_datos=0;
        if(count($Datos) > 0 ){
            $total_datos=count($Datos);
            $Datos["f10_control_cambios_marca"]=0;
        }
        
        $Datos["f10_actualizacion_campos_automaticos"]=1;
        $sql=$this->getSQLUpdate("f10", $Datos);
        $sql.="WHERE ID='$f10_id'";
        $this->Query($sql);
        
        return($total_datos);
    }
    
    public function RegistreAdjuntoF10($f10_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="f10_adjuntos";
        
        $Datos["f10_id"]=$f10_id;
        
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    //Fin Clases
}
