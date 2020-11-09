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
            if(!isset($_REQUEST["CmbAsmet"])){
                exit("E1;No se recibió si el acta es para Mutual o SAS, intente limpiar los datos del navegador");
            }
            $Asmet=$obCon->normalizar($_REQUEST["CmbAsmet"]);
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
            
            $obCon->CrearActaLiquidacion($Asmet,$FechaInicial,$FechaFinal,$CmbIPS,$CmbEPS,$TipoActa,$TxtPrefijo, $TxtConsecutivo, $TxtAnio, $NombreRepresentanteEPS,$NombreRepresentanteIPS, $ApellidosRepresentanteEPS,$ApellidosRepresentanteIPS,$IdentificacionRepresentanteEPS, $IdentificacionRepresentanteIPS,$DomicilioRepresentanteEPS, $DomicilioRepresentanteIPS, $DireccionRepresentanteEPS,$DireccionRepresentanteIPS, $TelefonoRepresentanteEPS, $TelefonoRepresentanteIPS, $idUser);
            $obCon->ActualizaRegistro("ips", "CedulaRepresentante", $IdentificacionRepresentanteIPS, "NIT", $CmbIPS);
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
            if(!is_numeric($NuevoValor) and ($CampoAEditar=='ValorSegunActaCumplimientoMetas' or $CampoAEditar=='OtrosDescuentosConciliadosAfavor' or $CampoAEditar=='PagosPendientesPorLegalizar') ){
                
                exit("E1;Este campo debe ser un valor numerico;$idCampoTexto");
            }
            $obCon->ActualizaRegistro("actas_liquidaciones", $CampoAEditar, $NuevoValor, "ID", $idActaLiquidacion, 0);
            
            if($CampoAEditar=='IPS_Nombres_Representante_Legal'){
                $obCon->ActualizaRegistro("ips", "NombresRepresentante", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            if($CampoAEditar=='IPS_Apellidos_Representante_Legal'){
                $obCon->ActualizaRegistro("ips", "ApellidosRepresentante", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            if($CampoAEditar=='IPS_Identificacion_Representante_Legal'){
                $obCon->ActualizaRegistro("ips", "CedulaRepresentante", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            if($CampoAEditar=='IPS_Domicilio'){
                $obCon->ActualizaRegistro("ips", "Municipio", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            if($CampoAEditar=='IPS_Direccion'){
                $obCon->ActualizaRegistro("ips", "Direccion", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            if($CampoAEditar=='IPS_Telefono'){
                $obCon->ActualizaRegistro("ips", "Telefono", $NuevoValor, "NIT", $CmbIPS, 0);
            }
            
            if($CampoAEditar=='FechaInicial'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioInicial", $MesServicioInicial, "ID", $idActaLiquidacion, 0);
            }
            if($CampoAEditar=='FechaFinal'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_liquidaciones", "MesServicioFinal", $MesServicioFinal, "ID", $idActaLiquidacion, 0);
            }
            if($CampoAEditar=='PrefijoDepartamento' or $CampoAEditar=='ConsecutivoActa' or $CampoAEditar=='Anio'){
                $sql="UPDATE actas_liquidaciones SET IdentificadorActaEPS=CONCAT(PrefijoDepartamento,'-',ConsecutivoActa,'-',Anio) WHERE ID='$idActaLiquidacion'";
                $obCon->Query($sql);
            }
            print("OK;Campo $CampoAEditar del Acta de Liquidación se ha Editado con el valor: $NuevoValor");
        break; // Fin caso 2 
        
        case 3://Agregar contrato a acta liquidacion
            $idActaLiquidacion = $obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $idContrato = $obCon->normalizar($_REQUEST["Contrato"]);
            $NombreContrato = $obCon->normalizar($_REQUEST["NombreContrato"]);
            $FechaInicial = $obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal = $obCon->normalizar($_REQUEST["FechaFinal"]);
            $ValorContrato = $obCon->normalizar($_REQUEST["ValorContrato"]);
            $CmbIPS = $obCon->normalizar($_REQUEST["CmbIPS"]);
            
            if($idActaLiquidacion==''){
                exit("E1;No se recibió un acta de liquidación");
            }
            
            if($idContrato==''){
                exit("E1;No se recibió un número de contrato");
            }
            
            if($NombreContrato==''){
                exit("E1;No se recibió el nombre del contrato");
            }
            
            if($FechaInicial==''){
                exit("E1;No se recibió la fecha inicial del contrato");
            }
            
            if($FechaFinal==''){
                exit("E1;No se recibió la fecha final del contrato");
            }
            
            if(!is_numeric($ValorContrato) or $ValorContrato<0 ){
                exit("E1;El Valor del contrato debe ser un Número mayor o igual a Cero");
            }
            //$Validacion=$obCon->DevuelveValores("actas_liquidaciones_contratos", "idContrato", $idContrato);
            $sql="SELECT ID FROM actas_liquidaciones_contratos WHERE idContrato='$idContrato' AND idActaLiquidacion='$idActaLiquidacion'";
            $Validacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($Validacion["ID"]<>''){
                exit("E1;El contrato ya está agregado al acta");
            }
            $Datos["idActaLiquidacion"]=$idActaLiquidacion;
            $Datos["idContrato"]=$idContrato;
            $Datos["NIT_IPS"]=$CmbIPS;
            $Datos["FechaInicial"]=$FechaInicial;
            $Datos["FechaFinal"]=$FechaFinal;
            $Datos["Valor"]=$ValorContrato;
            $Datos["NombreContrato"]=$NombreContrato;
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
        
        case 8://Cerrar el acta de liquidacion copiar los registros en la tabla de anexo del acta de liquidacion por facturas
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            
            $destino='';
            $keyArchivo="Acta_Liquidacion_$idActaLiquidacion"."_";
            $Extension="";
            if(!empty($_FILES['UpSoporteActaLiquidacionCierre']['name'])){
                
                $info = new SplFileInfo($_FILES['UpSoporteActaLiquidacionCierre']['name']);
                $Extension=($info->getExtension());  
                if($Extension=='pdf'){
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/actas_liquidaciones/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destino=$carpeta.$keyArchivo.".".$Extension;
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['UpSoporteActaLiquidacionCierre']['tmp_name'],$destino);
                    $obCon->ActualizaRegistro("actas_liquidaciones", "Soporte", $destino, "ID", $idActaLiquidacion);
                }else{
                    exit("E1;Error el archivo debe ser tipo pdf;UpSoporteActaLiquidacionCierre");
                }
            }else{
                exit("E1;No se envió ningún archivo;UpSoporteActaLiquidacionCierre");
                
            }
            
            
            $FechaRegistra=date("Y-m-d H:i:s");
            $TablaDestino="actas_liquidaciones_items";
            $TablaOrigen="actas_conciliaciones_items";
            $sql="INSERT INTO $db.$TablaDestino 
                    (idActaLiquidacion,FechaFactura,MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    FechaRadicado,NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,TotalPagos,TotalAnticipos,
                    TotalCopagos,DescuentoPGP,DescuentoBDUA,OtrosDescuentos,AjustesCartera,TotalGlosaInicial,TotalGlosaFavor,
                    TotalGlosaContra,GlosaXConciliar,TotalDevoluciones,ValorSegunEPS,ValorSegunIPS,Diferencia,
                    NoRelacionada,idUser,FechaRegistro,NumeroDiasLMA,ValorAPagarLMA,CodigoSucursal)
                    SELECT '$idActaLiquidacion',FechaFactura,MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    FechaRadicado,NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,TotalPagos,TotalAnticipos,
                    TotalCopagos,DescuentoPGP,DescuentoBDUA,OtrosDescuentos,AjustesCartera,TotalGlosaInicial,TotalGlosaFavor,
                    TotalGlosaContra,GlosaXConciliar,TotalDevoluciones,ValorSegunEPS,ValorSegunIPS,Diferencia,
                    NoRelacionada,'$idUser','$FechaRegistra',NumeroDiasLMA,ValorAPagarLMA,CodigoSucursal
                    FROM $db.$TablaOrigen WHERE                    
                      ($TablaOrigen.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$TablaOrigen.NumeroContrato AND t2.idActaLiquidacion='$idActaLiquidacion') 
                    ";
            $obCon->Query($sql);
            
            print("OK;Anexo por facturas del acta de liquidación $idActaLiquidacion Guardado");
        break;//Fin caso 8  
    
        case 9://Copiar anexo por radicados
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $FechaRegistra=date("Y-m-d H:i:s");
            $TablaDestino="actas_liquidaciones_radicados_items";
            $TablaOrigen="actas_conciliaciones_items";
            $sql="INSERT INTO $db.$TablaDestino 
                    (idActaLiquidacion,MesServicio,DepartamentoRadicacion,NumeroRadicado,
                    FechaRadicado,NumeroContrato,ValorDocumento,Impuestos,TotalPagos,TotalAnticipos,
                    TotalCopagos,DescuentoPGP,DescuentoBDUA,OtrosDescuentos,AjustesCartera,TotalGlosaInicial,TotalGlosaFavor,
                    TotalGlosaContra,GlosaXConciliar,TotalDevoluciones,ValorSegunEPS,ValorSegunIPS,Diferencia,
                    NoRelacionada,idUser,FechaRegistro)
                    SELECT '$idActaLiquidacion',MesServicio,DepartamentoRadicacion,NumeroRadicado,FechaRadicado,NumeroContrato,SUM(ValorDocumento) AS ValorDocumento,
                                SUM(Impuestos) AS Impuestos,SUM(TotalPagos) AS TotalPagos,SUM(TotalAnticipos) AS TotalAnticipos,SUM(TotalCopagos) AS TotalCopagos,
                                SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos) AS OtrosDescuentos,SUM(AjustesCartera) AS AjustesCartera
                                ,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,SUM(TotalGlosaContra) AS TotalGlosaContra,
                                SUM(GlosaXConciliar) AS GlosaXConciliar,SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS ValorSegunEPS,
                                SUM(ValorSegunIPS) AS ValorSegunIPS,SUM(Diferencia) AS Diferencia,
                    NoRelacionada,'$idUser','$FechaRegistra' 
                    FROM $db.$TablaOrigen WHERE                    
                      ($TablaOrigen.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$TablaOrigen.NumeroContrato)
                    GROUP BY NumeroRadicado,MesServicio,NumeroContrato;      
                    ";
            $obCon->Query($sql);
            
            $obCon->ActualizaRegistro("actas_liquidaciones", "Estado", 1, "ID", $idActaLiquidacion);
            $sql="UPDATE $db.carteraeps t1 INNER JOIN $db.actas_liquidaciones_items t2 
                    ON t1.NumeroFactura = t2.Numerofactura SET t1.Estado=3 
                    WHERE t2.idActaLiquidacion='$idActaLiquidacion'";
            $obCon->Query($sql);
            $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=37&idActaLiquidacion=$idActaLiquidacion";
            $html=("<br><a href='$Ruta' target='_BLANK'><button class='btn btn-success'>Imprimir PDF</button></a>");
            $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=1&FacturaRadicado=0";
            $html.=(" <a href='$Ruta' target='_BLANK'><button class='btn btn-primary'>Anexo por Facturas del Acta</button></a>");
            $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=1&FacturaRadicado=1";
            $html.=(" <a href='$Ruta' target='_BLANK'><button class='btn btn-warning'>Anexo por Radicados del Acta</button></a>");
            print("OK;Acta de liquidación $idActaLiquidacion cerrada $html");
            
        break;//Fin caso 9
    
        case 10: //Editar un contrato
            $idContrato=$obCon->normalizar($_REQUEST["idContrato"]);
            $idCampoTexto=$obCon->normalizar($_REQUEST["idCampoTexto"]);
            $NuevoValor=$obCon->normalizar($_REQUEST["NuevoValor"]);
            $CampoAEditar=$obCon->normalizar($_REQUEST["CampoAEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
                        
            if($idContrato==''){
                exit("E1;No se recibió el id del Contrato a Editar");
                
            }
            if($NuevoValor==''){
                exit("E1;la caja de texto no puede estar vacía;$idCampoTexto");
                
            } 
            if($CampoAEditar=='ValorContrato' and ! is_numeric($NuevoValor) and $NuevoValor<0){
                exit("E1;El valor del contrato debe ser un Valor Númerico mayor o igual Cero;$idCampoTexto");
            }
            $obCon->ActualizaRegistro("contratos", $CampoAEditar, $NuevoValor, "ID", $idContrato, 0);
            
            print("OK;Campo $CampoAEditar del Contrato se ha Editado");
        break; // Fin caso 10
        
        case 11: // Renormar un contrato
            $ContratoNuevo=trim($obCon->normalizar($_REQUEST["ContratoNuevo"]));
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            $DatosMesServicio = explode("-", $FechaInicial);
            $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
            
            $DatosMesServicio = explode("-", $FechaFinal);
            $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            if($ContratoNuevo==''){
                exit("E1;La caja de texto Contrato Nuevo no puede estar vacío");
            }
            
            if($NumeroContrato==''){
                exit("E1;No se recibió un contrato a reemplazar");
            }
            $FechaRegistro=date("Y-m-d H:i:s");
            $sql="INSERT INTO registra_ediciones_contratos (NumeroFactura,ContratoAnterior,ContratoNuevo,idUser,FechaRegistro,NIT_IPS) 
                    SELECT NumeroFactura,NumeroContrato,'$ContratoNuevo','$idUser','$FechaRegistro', '$CmbIPS' 
                    FROM  $db.carteraeps WHERE NumeroContrato='$NumeroContrato' AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $obCon->Query($sql);
            
            $sql="UPDATE $db.carteraeps SET NumeroContrato='$ContratoNuevo' WHERE NumeroContrato='$NumeroContrato' AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $obCon->Query($sql);
            
            $sql="UPDATE $db.historial_carteracargada_eps SET NumeroContrato='$ContratoNuevo' WHERE NumeroContrato='$NumeroContrato' AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $obCon->Query($sql);
            
            $sql="UPDATE $db.hoja_de_trabajo SET NumeroContrato='$ContratoNuevo' WHERE NumeroContrato='$NumeroContrato' AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $obCon->Query($sql);
            
            $sql="UPDATE $db.actas_conciliaciones_items SET NumeroContrato='$ContratoNuevo' WHERE NumeroContrato='$NumeroContrato' AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $obCon->Query($sql);
            
            $sql="UPDATE actas_liquidaciones_contratos SET idContrato='$ContratoNuevo' WHERE idContrato='$NumeroContrato' AND idActaLiquidacion='$idActaLiquidacion'";
            $obCon->Query($sql);
            
            print("OK;Contrato Renombrado");
            
        break; // Fin caso 11    
        
        case 12:// Marcar un usuario como aprobador del acta de liquidacion
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $obCon->ActualizaRegistro("actas_liquidaciones_firmas","Aprueba",0,"idActaLiquidacion",$idActaLiquidacion);
            $obCon->ActualizaRegistro("actas_liquidaciones_firmas","Aprueba",1,"ID", $idItem);
            print("OK;Firma marcada como Aprueba");
        break;//Fin caso 12
        
        case 13:// Enviar la fecha y hora de Actualización
           
            
            $keyUpdate= uniqid();
            $sql="UPDATE actas_liquidaciones SET NuevoSaldo=Saldo-TotalPagosDespuesDeFirma ";
            $obCon->Query($sql);
            
            
            $sql="UPDATE actas_liquidaciones SET idUserActualizacionSaldo='$idUser',KeyUpdate='$keyUpdate' WHERE Saldo>0";
            $obCon->Query($sql);
            $sql="SELECT COUNT(ID) AS TotalRegistros FROM actas_liquidaciones WHERE KeyUpdate='$keyUpdate' AND idUserActualizacionSaldo='$idUser'";
            $DatosTotales=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosTotales["TotalRegistros"];
            print("OK;Inicio de actualización de $TotalRegistros Actas;$TotalRegistros;$keyUpdate");
            
        break;//Fin caso 13
        
        case 14:// Se Actualizan los saldos
            $KeyUpdate = $obCon->normalizar($_REQUEST["KeyUpdate"]);            
            $TotalRegistros=$obCon->normalizar($_REQUEST["TotalRegistros"]);
            $FechaActualizacion=date("Y-m-d H:i:s");
            $newKeyUpdate= uniqid();
            $sql="SELECT ID,NIT_IPS FROM actas_liquidaciones WHERE KeyUpdate='$KeyUpdate' LIMIT 1";
            $DatosActas=$obCon->FetchAssoc($obCon->Query($sql));
            if($DatosActas["NIT_IPS"]==''){
                print("OK;Proceso Terminado;0");
            }
            $idActa=$DatosActas["ID"];
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $DatosActas["NIT_IPS"]);
            $dbPagos=$DatosIPS["DataBase"];
            $sql="UPDATE actas_liquidaciones t1 SET t1.TotalPagosDespuesDeFirma=(SELECT IFNULL((SELECT SUM(ValorPago) FROM $dbPagos.notas_db_cr_2 t2 
                WHERE t2.FechaTransaccion>t1.FechaFirma 
                AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE Estado=1 AND t2.TipoOperacion2=t3.TipoOperacion AND Aplicacion='TotalPagos') 
                AND EXISTS (SELECT 1 FROM $dbPagos.actas_liquidaciones_items t4 WHERE t2.NumeroFactura=t4.NumeroFactura AND t4.idActaLiquidacion=t1.ID ) ),0)) 
                ,t1.keyUpdate='$newKeyUpdate',t1.FechaActualizacionSaldo='$FechaActualizacion',t1.NuevoSaldo=t1.Saldo-t1.TotalPagosDespuesDeFirma 

                WHERE  t1.ID='$idActa'";
            
            $obCon->Query($sql);
            
            $sql="SELECT COUNT(ID) AS TotalRegistros FROM actas_liquidaciones WHERE KeyUpdate='$KeyUpdate' AND idUserActualizacionSaldo='$idUser'";
            $DatosTotales=$obCon->FetchAssoc($obCon->Query($sql));
            $RegistrosFaltantes=$DatosTotales["TotalRegistros"];
            $ActualizacionesRealizadas=$TotalRegistros-$RegistrosFaltantes;
            print("OK;$ActualizacionesRealizadas Actualizaciones Relizadas de $TotalRegistros;$RegistrosFaltantes;$TotalRegistros;$KeyUpdate");
            
        break;//Fin caso 14
        
        case 15://Agregar contrato no ejecutado a un acta liquidacion
            $idActaLiquidacion = $obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $idContrato = $obCon->normalizar($_REQUEST["idContratoNoEjecutado"]);
            
            $CmbIPS = $obCon->normalizar($_REQUEST["CmbIPS"]);
            
            if($idActaLiquidacion==''){
                exit("E1;No se recibió un acta de liquidación");
            }
            
            if($idContrato==''){
                exit("E1;No se recibió un número de contrato");
            }
            
            $DatosContrato=$obCon->DevuelveValores("contratos", "ID", $idContrato);
            $FechaInicial=$DatosContrato["FechaInicioContrato"];
            $FechaFinal=$DatosContrato["FechaFinalContrato"];
            $Contrato=$DatosContrato["Contrato"];
            $ValorContrato=$DatosContrato["ValorContrato"];
            //$Validacion=$obCon->DevuelveValores("actas_liquidaciones_contratos", "idContrato", $idContrato);
            $sql="SELECT ID FROM actas_liquidaciones_contratos WHERE idContrato='$Contrato' AND idActaLiquidacion='$idActaLiquidacion'";
            $Validacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($Validacion["ID"]<>''){
                exit("E1;El contrato ya está agregado al acta");
            }
            $Datos["idActaLiquidacion"]=$idActaLiquidacion;
            $Datos["idContrato"]=$Contrato;
            $Datos["FechaInicial"]=$FechaInicial;
            $Datos["FechaFinal"]=$FechaFinal;
            $Datos["Valor"]=$ValorContrato;
            $Datos["NombreContrato"]=$Contrato;
            $sql=$obCon->getSQLInsert("actas_liquidaciones_contratos", $Datos);
            $obCon->Query($sql);
            print("OK;Se agregó el contrato satisfactoriamente");
        break;//Fin caso 15
        
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>