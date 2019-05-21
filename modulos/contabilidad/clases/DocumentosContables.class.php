<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class DocumentosContables extends ProcesoVenta{
    /**
     * Crea un documento contable
     * @param type $TipoDocumento
     * @param type $Fecha
     * @param type $Descripcion
     * @param type $idEmpresa
     * @param type $idSucursal
     * @param type $idCentroCostos
     * @param type $Soporte
     * @param type $idUser
     * @return type
     */
    function CrearDocumentoContable($TipoDocumento,$Fecha,$Descripcion,$idEmpresa,$idSucursal,$idCentroCostos,$Soporte,$idUser){
        
        $Tabla="documentos_contables_control";
        $Consecutivo= $this->ObtenerMAX($Tabla, "Consecutivo", "idDocumento", $TipoDocumento);
        $Consecutivo++;
        $Datos["idDocumento"]=$TipoDocumento;
        $Datos["Consecutivo"]=$Consecutivo;
        $Datos["Fecha"]=$Fecha;
        $Datos["Descripcion"]=$Descripcion;
        $Datos["Estado"]="ABIERTO";
        $Datos["idUser"]=$idUser;
        $Datos["idEmpresa"]=$idEmpresa;
        $Datos["idSucursal"]=$idSucursal;
        $Datos["idCentroCostos"]=$idCentroCostos;
        $Datos["Soporte"]=$Soporte;
        $sql= $this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
        $idDocNew= $this->ObtenerMAX($Tabla, "ID", 1, "");
        return($idDocNew);
        
    }
    /**
     * Agrega un movimiento contable a un documento
     * @param type $idDocumento
     * @param type $Tercero
     * @param type $CuentaPUC
     * @param type $TipoMovimiento
     * @param type $Valor
     * @param type $Concepto
     * @param type $NumDocSoporte
     * @param type $Soporte
     */
    function AgregaMovimientoDocumentoContable($idDocumento,$Tercero,$CuentaPUC,$TipoMovimiento,$Valor,$Concepto,$NumDocSoporte,$Soporte){
        $DatosCuentas=$this->DevuelveValores("subcuentas", "PUC", $CuentaPUC);
        $NombreCuenta=$DatosCuentas["Nombre"];
        if($TipoMovimiento=="DB"){
            $Debito=$Valor;
            $Credito=0;
        }else{
            $Debito=0;
            $Credito=$Valor;
        }
        $Tabla="documentos_contables_items";
        
        $Datos["idDocumento"]=$idDocumento;
        $Datos["Tercero"]=$Tercero;
        $Datos["CuentaPUC"]=$CuentaPUC;
        $Datos["NombreCuenta"]=$NombreCuenta;
        $Datos["Debito"]=$Debito;
        $Datos["Credito"]=$Credito;
        $Datos["Concepto"]=$Concepto;
        $Datos["NumDocSoporte"]=$NumDocSoporte;
        $Datos["Soporte"]=$Soporte;
        
        $sql= $this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
        $id= $this->ObtenerMAX($Tabla, "ID", 1, "");
        return ($id);       
    }
    /**
     * Devuelve el sql para insertar los movimientos de un documento contable al librodiario
     * @param type $idDocumento
     * @return type
     */
    function getSQLDocumentoContableLibroDiario($idDocumento){
        $idUser=$_SESSION["idUser"];
        $sqlValores="INSERT INTO `librodiario` ( `Fecha`, `Tipo_Documento_Intero`, `Num_Documento_Interno`, `Num_Documento_Externo`, `Tercero_Tipo_Documento`, `Tercero_Identificacion`, `Tercero_DV`, `Tercero_Primer_Apellido`, `Tercero_Segundo_Apellido`, `Tercero_Primer_Nombre`, `Tercero_Otros_Nombres`, `Tercero_Razon_Social`, `Tercero_Direccion`, `Tercero_Cod_Dpto`, `Tercero_Cod_Mcipio`, `Tercero_Pais_Domicilio`, `Concepto`, `CuentaPUC`, `NombreCuenta`, `Detalle`, `Debito`, `Credito`, `Neto`, `Mayor`, `Esp`, `idCentroCosto`, `idEmpresa`, `idSucursal`, `Estado`, `idUsuario`) VALUES ";
        
        $DatosDocumento= $this->DevuelveValores("vista_documentos_contables", "ID", $idDocumento);
        
        $Fecha=$DatosDocumento["Fecha"];
        $idCentroCostos=$DatosDocumento["idCentroCostos"];
        $idEmpresa=$DatosDocumento["idEmpresa"];
        $idSucursal=$DatosDocumento["idSucursal"];
        $TipoDocumento=$DatosDocumento["Nombre"];
        $Consecutivo=$DatosDocumento["Consecutivo"];
        $Consulta=$this->ConsultarTabla("documentos_contables_items", "WHERE idDocumento='$idDocumento'");
        while($DatosItems= $this->FetchAssoc($Consulta)){
        
            $DatosTercero= $this->DevuelveValores("proveedores", "Num_Identificacion", $DatosItems["Tercero"]);
            if($DatosTercero["Num_Identificacion"]==''){
                $DatosTercero= $this->DevuelveValores("clientes", "Num_Identificacion", $DatosItems["Tercero"]);
            }
            $TerceroTipoDocumento=$DatosTercero["Tipo_Documento"];
            $NIT=$DatosTercero["Num_Identificacion"];
            $DV=$DatosTercero["DV"];
            $TerceroNombre1=$DatosTercero["Primer_Apellido"];
            $TerceroNombre2=$DatosTercero["Segundo_Apellido"];
            $TerceroNombre3=$DatosTercero["Primer_Nombre"];
            $TerceroNombre4=$DatosTercero["Otros_Nombres"];
            $RazonSocial=$DatosTercero["RazonSocial"];
            $Direccion=$DatosTercero["Direccion"];
            $CodDepartamento=$DatosTercero["Cod_Dpto"];
            $CodMunicipo=$DatosTercero["Cod_Mcipio"];
            $codPais=$DatosTercero["Pais_Domicilio"];
            $Concepto=$DatosItems["Concepto"];
            $CuentaPUC=$DatosItems["CuentaPUC"];
            $NombreCuenta=$DatosItems["NombreCuenta"];
            $Debito=$DatosItems["Debito"];
            $Credito=$DatosItems["Credito"];
            $Neto=0;
            if($Debito>0){
                $Neto=$Debito;
            }
            if($Credito>0){
                $Neto=$Credito*(-1);
            }
            $sqlValores.="('$Fecha','$TipoDocumento','$Consecutivo','','$TerceroTipoDocumento','$NIT','$DV','$TerceroNombre1','$TerceroNombre2','$TerceroNombre3','$TerceroNombre4','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Documentos Contables','$CuentaPUC','$NombreCuenta','$Concepto',$Debito,$Credito,$Neto,'NO','NO',$idCentroCostos,$idEmpresa,$idSucursal,'',$idUser),";
            
        }  
        
        $sqlValores = substr($sqlValores, 0, -1);
        return($sqlValores);
        
    }
    /**
     * Guarda un documento contable
     * @param type $idDocumento
     */
    function GuardarDocumentoContable($idDocumento) {
        $sql=$this->getSQLDocumentoContableLibroDiario($idDocumento);
        //print($sql);
        $this->Query($sql);
        $this->ActualizaRegistro("documentos_contables_control", "Estado", "CERRADO", "ID", $idDocumento);
    }
    
    public function CopiarItemsDocumento($TipoDocumento,$idDocumentoACopiar,$idDocumentoDestino) {
        $sql="SELECT ite.Tercero,ite.Credito,ite.Debito,ite.CuentaPUC,ite.Concepto,ite.NumDocSoporte,ite.Soporte FROM documentos_contables_items ite "
                . "INNER JOIN documentos_contables_control c ON ite.idDocumento=c.ID WHERE c.idDocumento='$TipoDocumento' AND c.Consecutivo='$idDocumentoACopiar'";    
        $Consulta=$this->Query($sql);
        while($DatosMovimiento= $this->FetchAssoc($Consulta)){
            $TipoMovimiento="CR";
            $Valor=$DatosMovimiento["Credito"];
            if($DatosMovimiento["Debito"]<>0){
                $TipoMovimiento="DB";
                $Valor=$DatosMovimiento["Debito"];
            }
            $this->AgregaMovimientoDocumentoContable($idDocumentoDestino, $DatosMovimiento["Tercero"], $DatosMovimiento["CuentaPUC"], $TipoMovimiento, $Valor, $DatosMovimiento["Concepto"], $DatosMovimiento["NumDocSoporte"], $DatosMovimiento["Soporte"]);
        }
            
        }
        
        function AgregueBaseAMovimientoContable($idDocumento,$Concepto,$Base,$Porcentaje,$Valor,$idUser,$idItem){
        
            $Tabla="documentos_contables_registro_bases";
            
            $Datos["idDocumentoContable"]=$idDocumento;
            
            $Datos["Concepto"]=$Concepto;
            $Datos["Base"]=$Base;
            $Datos["Porcentaje"]=$Porcentaje;
            $Datos["ValorPorcentaje"]=$Porcentaje/100;
            $Datos["Valor"]=$Valor;
            $Datos["idUser"]=$idUser;
            $Datos["idItemDocumentoContable"]=$idItem;
            $Datos["Estado"]="ABIERTO";
            $sql= $this->getSQLInsert($Tabla, $Datos);
            $this->Query($sql);


        }
        
        public function VerificaSiCuentaSolicitaBase($CuentaPUC) {
            $DatosCuenta=$this->DevuelveValores("subcuentas","PUC",$CuentaPUC);
            return($DatosCuenta["SolicitaBase"]);
        }
    /**
     * Fin Clase
     */
}
