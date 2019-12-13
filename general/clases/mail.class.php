<?php
if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}

class TSMail extends conexion{
    /**
     * 
     * @param type $para
     * @param type $asunto
     * @param type $mensaje
     * @param type $adjuntos
     * @return type
     */
    public function EnviarMailPorPHPNativo($para, $asunto, $mensaje,$adjuntos){
        unset($Columnas);
       
        $Columnas= $this->ShowColums($Tabla);
        
        $i=0;
        $z=0;
        $ColumnasSeleccionadas["Field"]=[];
        foreach ($Columnas["Field"] as $key => $value) {
            $Busqueda= explode(".", $Tabla);
            if(isset($Busqueda[1])){
                $key=$Busqueda[1];
            }else{
                $key=$Tabla;
            }
            
            $Consulta=$this->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla like '%$key' AND Campo='$value' AND (Visible=0 OR Habilitado=0)");
            $DatosExcluidas=$this->FetchAssoc($Consulta);
            if($DatosExcluidas["ID"]=='' AND $value<>'Updated' AND $value<>'Sync'){
                $DatosNombres=$this->DevuelveValores("configuraciones_nombres_campos", "NombreDB", $value);
                $sql="SELECT * FROM configuracion_campos_asociados WHERE TablaOrigen='$Tabla' AND CampoTablaOrigen='$value'";
                $consulta= $this->Query($sql);
                $DatosCamposAsociados=$this->FetchAssoc($consulta);
                $TablaAsociada=$DatosCamposAsociados["TablaAsociada"];
                $CampoAsociado=$DatosCamposAsociados["CampoAsociado"];
                $IDCampoAsociado=$DatosCamposAsociados["IDCampoAsociado"];
                $ColumnasSeleccionadas["Field"][$i]=$value;
                $ColumnasSeleccionadas["Visualiza"][$i]=$value;
                $ColumnasSeleccionadas["TablaAsociada"][$i]=$TablaAsociada;
                $ColumnasSeleccionadas["CampoAsociado"][$i]=$CampoAsociado;
                $ColumnasSeleccionadas["IDCampoAsociado"][$i]=$IDCampoAsociado;
                if($DatosNombres["Visualiza"]<>''){
                    $ColumnasSeleccionadas["Visualiza"][$i]=$DatosNombres["Visualiza"];
                }

                $ColumnasSeleccionadas["Type"][$i]=$Columnas["Type"][$z];
                $ColumnasSeleccionadas["SubQuery"][$i]="";
                if($IDCampoAsociado<>'' and $TablaAsociada<>'' and $CampoAsociado<>''){
                    $SubQuery="(SELECT $CampoAsociado FROM $TablaAsociada WHERE $TablaAsociada.$IDCampoAsociado=$Tabla.$value LIMIT 1) as $value";
                    $ColumnasSeleccionadas["SubQuery"][$i]=$SubQuery;
                }
                
            }
            $i++;
            $z++;
        }
        
        return($ColumnasSeleccionadas);
            
    }
    
    
    /**
     * Fin Clase
     */
}
