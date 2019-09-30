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
        
class ActasLiquidacion extends conexion{
    
    
       
    public function CrearActaLiquidacion($FechaInicial,$FechaFinal,$CmbIPS,$CmbEPS,$TipoActa,$TxtPrefijo, $TxtConsecutivo, $TxtAnio, $NombreRepresentanteEPS,
            $NombreRepresentanteIPS, $ApellidosRepresentanteEPS,$ApellidosRepresentanteIPS,$IdentificacionRepresentanteEPS, 
            $IdentificacionRepresentanteIPS,$DomicilioRepresentanteEPS, $DomicilioRepresentanteIPS, 
            $DireccionRepresentanteEPS,$DireccionRepresentanteIPS, $TelefonoRepresentanteEPS, 
            $TelefonoRepresentanteIPS, $idUser){
        
        
        $DatosMesServicio= explode("-", $FechaInicial);
        $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
        $DatosMesServicio= explode("-", $FechaFinal);
        $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
        
        $Fecha=date("Y-m-d H:i:s");
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $DatosEPS=$this->DevuelveValores("eps", "ID", $CmbEPS);
        $Datos["FechaInicial"]=$FechaInicial;
        $Datos["FechaFinal"]=$FechaFinal;
        $Datos["MesServicioInicial"]=$MesServicioInicial;
        $Datos["MesServicioFinal"]=$MesServicioFinal;
        $Datos["TipoActaLiquidacion"]=$TipoActa;
        $Datos["PrefijoDepartamento"]=$TxtPrefijo;        
        $Datos["ConsecutivoActa"]=$TxtConsecutivo;
        $Datos["Anio"]=$TxtAnio;
        $Datos["IdentificadorActaEPS"]=$TxtPrefijo."-".$TxtConsecutivo."-".$TxtAnio;
        $Datos["NIT_IPS"]=$CmbIPS;
        $Datos["RazonSocialIPS"]=$DatosIPS["Nombre"];
        $Datos["NIT_EPS"]=$DatosEPS["NIT"];
        $Datos["RazonSocialEPS"]=$DatosEPS["Nombre"];        
        $Datos["EPS_Nombres_Representante_Legal"]=$NombreRepresentanteEPS;
        $Datos["EPS_Apellidos_Representante_Legal"]=$ApellidosRepresentanteEPS;
        $Datos["EPS_Identificacion_Representante_Legal"]=$IdentificacionRepresentanteEPS;
        $Datos["EPS_Domicilio"]=$DomicilioRepresentanteEPS;
        $Datos["EPS_Direccion"]=$DireccionRepresentanteEPS;
        $Datos["EPS_Telefono"]=$TelefonoRepresentanteEPS;
        $Datos["IPS_Nombres_Representante_Legal"]=$NombreRepresentanteIPS;
        $Datos["IPS_Apellidos_Representante_Legal"]=$ApellidosRepresentanteIPS;
        $Datos["IPS_Identificacion_Representante_Legal"]=$IdentificacionRepresentanteIPS;
        $Datos["IPS_Domicilio"]=$DomicilioRepresentanteIPS;
        $Datos["IPS_Direccion"]=$DireccionRepresentanteIPS;
        $Datos["IPS_Telefono"]=$TelefonoRepresentanteIPS;
        $Datos["FechaFirma"]=$Fecha;
        $Datos["CiudadFirma"]='POPAYAN';
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        $sql=$this->getSQLInsert("actas_liquidaciones", $Datos);
        $this->Query($sql);
        //$this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    public function AgregueFirmaActa($idActa,$Nombre,$Cargo,$Empresa) {
        $Datos["idActaLiquidacion"]=$idActa;
        $Datos["Nombre"]=$Nombre;
        $Datos["Cargo"]=$Cargo;
        $Datos["Empresa"]=$Empresa;
        $sql=$this->getSQLInsert("actas_liquidaciones_firmas", $Datos);
        $this->Query($sql);
        
    }
       
    
    
    //Fin Clases
}
