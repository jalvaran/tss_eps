<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/actas_liquidaciones.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ActasLiquidacion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear un Acta
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $TipoActa=$obCon->normalizar($_REQUEST["TipoActa"]);
            $TxtPrefijo=$obCon->normalizar($_REQUEST["TxtPrefijo"]);
            $TxtConsecutivo= ltrim(rtrim($obCon->normalizar($_REQUEST["TxtConsecutivo"])));
            $TxtAnio=$obCon->normalizar($_REQUEST["TxtAnio"]);
            $NombreRepresentanteEPS=$obCon->normalizar($_REQUEST["NombreRepresentanteEPS"]);
            $NombreRepresentanteIPS=$obCon->normalizar($_REQUEST["NombreRepresentanteIPS"]);
            $ApellidosRepresentanteEPS=$obCon->normalizar($_REQUEST["ApellidosRepresentanteEPS"]);
            $ApellidosRepresentanteIPS=$obCon->normalizar($_REQUEST["ApellidosRepresentanteIPS"]);
            $IdentificacionRepresentanteEPS=$obCon->normalizar($_REQUEST["IdentificacionRepresentanteEPS"]);
            $IdentificacionRepresentanteIPS=$obCon->normalizar($_REQUEST["IdentificacionRepresentanteIPS"]);
            $DomicilioRepresentanteEPS=$obCon->normalizar($_REQUEST["DomicilioRepresentanteEPS"]);
            $DomicilioRepresentanteIPS=$obCon->normalizar($_REQUEST["DomicilioRepresentanteIPS"]);
            $DireccionRepresentanteEPS=$obCon->normalizar($_REQUEST["DireccionRepresentanteEPS"]);
            $DireccionRepresentanteIPS=$obCon->normalizar($_REQUEST["DireccionRepresentanteIPS"]);
            $TelefonoRepresentanteEPS=$obCon->normalizar($_REQUEST["TelefonoRepresentanteEPS"]);
            $TelefonoRepresentanteIPS=$obCon->normalizar($_REQUEST["TelefonoRepresentanteIPS"]);
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            
            foreach ($_POST as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            if(!is_numeric($TxtConsecutivo)){
                exit("E1;El campo Consecutivo debe ser un valor númerico;TxtConsecutivo");
            }
            if(!is_numeric($TxtAnio) or strlen($TxtAnio)<>4){
                exit("E1;El campo Año no es válido;TxtAnio");
            }
            
            $obCon->CrearActaLiquidacion($FechaInicial,$FechaFinal,$CmbIPS,$CmbEPS,$TipoActa,$TxtPrefijo, $TxtConsecutivo, $TxtAnio, $NombreRepresentanteEPS,$NombreRepresentanteIPS, $ApellidosRepresentanteEPS,$ApellidosRepresentanteIPS,$IdentificacionRepresentanteEPS, $IdentificacionRepresentanteIPS,$DomicilioRepresentanteEPS, $DomicilioRepresentanteIPS, $DireccionRepresentanteEPS,$DireccionRepresentanteIPS, $TelefonoRepresentanteEPS, $TelefonoRepresentanteIPS, $idUser);
            print("OK;Acta de Liquidación Creada Correctamente");
            
        break; //fin caso 1
        
        case 2: //Editar Acta Liquidacion
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $idCampoTexto=$obCon->normalizar($_REQUEST["idCampoTexto"]);
            $NuevoValor=$obCon->normalizar($_REQUEST["NuevoValor"]);
            $CampoAEditar=$obCon->normalizar($_REQUEST["CampoAEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
                        
            if($idActaLiquidacion==''){
                exit("E1;No se recibió el id del Acta a Editar");
                
            }
            if($NuevoValor==''){
                exit("E1;la caja de texto no puede estar vacía;$idCampoTexto");
                
            }
                        
            $obCon->ActualizaRegistro("actas_liquidaciones", $CampoAEditar, $NuevoValor, "ID", $idActaLiquidacion, 0);
            
            if($CampoAEditar=='FechaInicial'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioInicial", $MesServicioInicial, "ID", $idActaConciliacion, 0);
            }
            if($CampoAEditar=='FechaFinal'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioFinal", $MesServicioFinal, "ID", $idActaConciliacion, 0);
            }
            print("OK;Campo $CampoAEditar del Acta de Liquidación se ha Editado");
        break; // Fin caso 2 
        
        case 3://Agregar contrato a acta liquidacion
            $idActaLiquidacion = $obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $idContrato = $obCon->normalizar($_REQUEST["Contrato"]);
            $CmbIPS = $obCon->normalizar($_REQUEST["CmbIPS"]);
            
            if($idActaLiquidacion==''){
                exit("E1;No se recibió un acta de liquidación");
            }
            
            if($idContrato==''){
                exit("E1;No se recibió un número de contrato");
            }
            $Validacion=$obCon->DevuelveValores("actas_liquidaciones_contratos", "idContrato", $idContrato);
            if($Validacion["ID"]<>''){
                exit("E1;El contrato ya está agregado al acta");
            }
            $Datos["idActaLiquidacion"]=$idActaLiquidacion;
            $Datos["idContrato"]=$idContrato;
            $sql=$obCon->getSQLInsert("actas_liquidaciones_contratos", $Datos);
            $obCon->Query($sql);
            print("OK;Se agregó el contrato satisfactoriamente");
        break;//Fin caso 3
        
        case 4://Elimina un contrato asociado a una acta de liquidacion
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $obCon->BorraReg("actas_liquidaciones_contratos", "ID", $idItem);
            print("OK;Contrato eliminado del acta");
            
        break;//Fin caso 4
    
        case 5://Guarda la firma
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $TipoFirma=$obCon->normalizar($_REQUEST["TipoFirma"]);
            $CmbFirmaUsual=$obCon->normalizar($_REQUEST["CmbFirmaUsual"]);
            $NombreRepresentanteIPS=$obCon->normalizar($_REQUEST["NombreRepresentanteIPS"]);
            $ApellidosRepresentanteIPS=$obCon->normalizar($_REQUEST["ApellidosRepresentanteIPS"]);
            $RepresentanteIPS=$NombreRepresentanteIPS." ".$ApellidosRepresentanteIPS;
            
            if($idActaLiquidacion==''){
                exit("E1;No se recibió el id del Acta");
                
            }
            
            if($TipoFirma==''){
                exit("E1;No se recibió el Tipo de Firma a agregar");
                
            }
            
            if($TipoFirma=='1'){
                if($CmbFirmaUsual==''){
                    exit("E1;Debe Seleccionar una Firma Usual;CmbFirmaUsual");
                }
                if($CmbFirmaUsual=='RI'){
                    if($NombreRepresentanteIPS==''){
                        exit("E1;No se ha escrito el nombre del representante de la IPS;NombreRepresentanteIPS");
                    }else if($ApellidosRepresentanteIPS==''){
                        exit("E1;No se ha escrito el apellido del representante de la IPS;ApellidosRepresentanteIPS");
                    }else{
                        $obCon->AgregueFirmaActa($idActaLiquidacion, $RepresentanteIPS, "REPRESENTANTE LEGAL", $DatosIPS["Nombre"]);
                    }
                }else{
                    $DatosFirmas=$obCon->DevuelveValores("actas_conciliaciones_firmas_usuales", "ID", $CmbFirmaUsual);
                    $obCon->AgregueFirmaActa($idActaLiquidacion, $DatosFirmas["Nombre"], $DatosFirmas["Cargo"], $DatosFirmas["Empresa"]);
                }
            }
            /*
            if($TipoFirma=='2'){
                if($TxtNombreFirmaActa==''){
                    exit("E1;Debe Seleccionar el Nombre para la Firma;TxtNombreFirmaActa");
                }
                if($TxtCargoFirmaActa==''){
                    exit("E1;Debe Digitar el Cargo de quien firma;TxtCargoFirmaActa");
                }
                if($TxtEmpresaFirmaActa==''){
                    exit("E1;Debe Digitar La Empresa;TxtEmpresaFirmaActa");
                }
                $obCon->AgregueFirmaActa($idActaConciliacion, $TxtNombreFirmaActa, $TxtCargoFirmaActa, $TxtEmpresaFirmaActa);
            }
            
             * 
             */
            print("OK;Firma Agregada");
        break;//Fin caso 5
        
        case 6://elimina una firma del acta de liquidacion
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $obCon->BorraReg("actas_liquidaciones_firmas", "ID", $idItem);
            print("OK;Firma eliminada del acta");
        break;//  fin caso 6  
    
    case 7://Editar las firmas de un acta
            $idFirma=$obCon->normalizar($_REQUEST["idFirma"]);
            $idCajaFirma=$obCon->normalizar($_REQUEST["idCajaFirma"]);
            $TxtValorNuevo=$obCon->normalizar($_REQUEST["TxtValorNuevo"]);
            $CampoEditar=$obCon->normalizar($_REQUEST["CampoEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);            
            if($idFirma==''){
                exit("E1;No se recibió el id de la Firma a Editar");
                
            }
            if($TxtValorNuevo==''){
                exit("E1;la caja de texto no puede estar vacía;$idCajaFirma");
                
            }
            if($CampoEditar==''){
                exit("E1;No se recibió el campo a Editar;$CampoEditar");
                
            }
                        
            $obCon->ActualizaRegistro("actas_liquidaciones_firmas", $CampoEditar, $TxtValorNuevo, "ID", $idFirma, 0);
            
            print("OK;Campo $CampoEditar de las firmas ha sido Editado");
        break;//Fin caso 7
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>