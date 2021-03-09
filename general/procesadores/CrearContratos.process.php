<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$fecha=date("Y-m-d");

include_once("../clases/CrearContratos.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ContratosEPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1://Crear un contrato
            
            $Datos["NitEPSContratante"]=$obCon->normalizar($_REQUEST['CmbEPS']);  
            $Datos["NitIPSContratada"]=$obCon->normalizar($_REQUEST['CmbIPS']);  
            $Datos["ClasificacionContrato"]=$obCon->normalizar($_REQUEST['CmbClasificacionContrato']);  
            $Datos["NumeroContrato"]=$obCon->normalizar($_REQUEST['NumeroContrato']);    
            $Datos["Contrato"]=$obCon->normalizar($_REQUEST['NumeroContrato']);    
            $Datos["OtroSi"]=$obCon->normalizar($_REQUEST['CmbNumeroOtroSI']);
              
            $Datos["ContratoEquivalente"]=$obCon->normalizar($_REQUEST['ContratoEquivalente']);    
            $Datos["TipoContrato"]=$obCon->normalizar($_REQUEST['CmbTipoContrato']);    
            $Datos["Upc_contratada"]=$obCon->normalizar($_REQUEST['TxtUPC']);    
            $Datos["Objeto_Contrato"]=$obCon->normalizar($_REQUEST['TxtObjetoContrato']);  
            $Datos["ValorContrato"]=$obCon->normalizar($_REQUEST['ValorContrato']);   
            $Datos["FechaInicioContrato"]=$obCon->normalizar($_REQUEST['FechaInicial']);  
            $Datos["FechaFinalContrato"]=$obCon->normalizar($_REQUEST['FechaFinal']);             
            //$Datos["FechaRealFinalContrato"]=$obCon->normalizar($_REQUEST['FechaRealFinalContrato']);  
            $Datos["NivelComplejidad"]=$obCon->normalizar($_REQUEST['TxtNivelComplejidad']); 
            $Datos["FinalidadContrato"]=$obCon->normalizar($_REQUEST['FinalidadContrato']);    
            $Datos["EstadoContrato"]='ACTIVO'; 
            $Datos["TipoPlan"]=$obCon->normalizar($_REQUEST['CmbTipoPlan']); 
            $Datos["Cobertura"]=$obCon->normalizar($_REQUEST['CmbCobertura']);            
            $Datos["DepartamentoCobertura"]=$obCon->normalizar($_REQUEST['DepartamentoCobertura']); 
            $Datos["NumeroAfiliados"]=$obCon->normalizar($_REQUEST['TxtNumeroAfiliados']); 
            $Datos["NivelPrioridad"]=$obCon->normalizar($_REQUEST['CmbNivelPrioridad']);
            
            $Datos["idUser"]=$idUser; 
            $Datos["FechaRegistro"]=date("Y-m-d"); 
            $Datos["FechaActualizacion"]=date("Y-m-d"); 
            
            foreach ($_POST as $key => $value) {
                if($value=='' and $key<>'TxtUPC' AND $key<>'TxtNumeroAfiliados' AND $key<>'CmbContratoPadre'){
                    exit("E1;El campo $key No Puede estar vacío;$key");
                }
            }
            $NitIPS=$Datos["NitIPSContratada"];
            $Contrato=$Datos["Contrato"];
            $sql="SELECT * FROM contratos WHERE NitIPSContratada='$NitIPS' AND NumeroContrato='$Contrato'";
            $DatosContrato=$obCon->FetchAssoc($obCon->Query($sql));
            if($DatosContrato["ID"]>0){
                exit("E1;El contrato ya fue creado el ".$DatosContrato["FechaRegistro"]);
            }
            
            if($Datos["ClasificacionContrato"]=='OTRO SI'){
                $Datos["Contrato"]=$obCon->normalizar($_REQUEST['CmbContratoPadre']);
                if($Datos["Contrato"]==''){
                    exit("E1;Debe Seleccionar un contrato padre;select2-CmbContratoPadre-container");
                }
                $Datos["NumeroContrato"]=($Datos["Contrato"]." ".$Datos["OtroSi"]);  
                $Datos["Contrato"]=$Datos["NumeroContrato"];
            }else{
                $Datos["OtroSi"]="";
            }
            
            if($Datos["ClasificacionContrato"]=='SIN CONTRATO' or $Datos["ClasificacionContrato"]=='URGENCIAS'){
                $Datos["NumeroContrato"]=$Datos["ClasificacionContrato"]." ".trim($Datos["NumeroContrato"]);
            }
            
            if($Datos["TipoContrato"]=="CAPITA" or $Datos["TipoContrato"]=="CAPITA MORVILIDAD" or $Datos["TipoContrato"]=="CAPITA PDYDT" or $Datos["TipoContrato"]=="CAPITA ACTIVIDADES MINIMAS"){
                if(!is_numeric($Datos["Upc_contratada"])){
                    exit("E1;El campo UPC debe contener un valor númerico;TxtUPC");
                }
                if(!is_numeric($Datos["NumeroAfiliados"]) or $Datos["NumeroAfiliados"]<=0){
                    exit("E1;El campo Numero de Afiliados debe contener un valor númerico mayor a 0;TxtNumeroAfiliados");
                }
            }
            
            if(!is_numeric($Datos["ValorContrato"]) or $Datos["ValorContrato"]<0){
                exit("E1;El campo Valor Contrato debe contener un valor númerico mayor o igual a 0;ValorContrato");
            }
            $sql=$obCon->getSQLInsert("contratos", $Datos);
            
            $obCon->Query($sql);
            
            print("OK;Se ha creado el Contrato ".$Datos["Contrato"]);
            
        break;//FIn caso 1
        
        case 2://Asignar contrato como lo tiene la eps a un contrato existente
            $NIT_IPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $ContratoEquivalente=$obCon->normalizar($_REQUEST["ContratoEquivalente"]);
            $ContratoExistente=$obCon->normalizar($_REQUEST["ContratoExistente"]);
            
            $sql="UPDATE contratos SET ContratoEquivalente='$ContratoEquivalente' WHERE Contrato='$ContratoExistente' AND NitIPSContratada='$NIT_IPS';";
            print($sql);
            $obCon->Query($sql);
            
            print("Contrato editado");
        break;//fin caso 2  
    
        case 3://Crear un contrato percapita
            
            $idContratoPadre=$obCon->normalizar($_REQUEST["idContratoPadre"]);
            $FechaInicialPercapita=$obCon->normalizar($_REQUEST["FechaInicialPercapita"]);
            $FechaFinalPercapita=$obCon->normalizar($_REQUEST["FechaFinalPercapita"]);
            $CmbMunicipioPercapita=$obCon->normalizar($_REQUEST["CmbMunicipioPercapita"]);
            $TxtPorcentajePercapita=$obCon->normalizar($_REQUEST["TxtPorcentajePercapita"]);
            $TxtValorPercapita=$obCon->normalizar($_REQUEST["TxtValorPercapita"]);
            
            if($FechaInicialPercapita==''){
                exit("E1;La Fecha Inicial no puede estar vacía;FechaInicialPercapita");
            }
            if($FechaFinalPercapita==''){
                exit("E1;La Fecha Final no puede estar vacía;FechaFinalPercapita");
            }
            if($CmbMunicipioPercapita==''){
                exit("E1;Debe Seleccionar un municipio;select2-CmbMunicipioPercapita-container");
            }
            if(!is_numeric($TxtPorcentajePercapita) or $TxtPorcentajePercapita<1 or $TxtPorcentajePercapita>100 ){
                exit("E1;El porcentaje poblacional debe ser un numero entre 1 y 100;TxtPorcentajePercapita");
            }
            if(!is_numeric($TxtValorPercapita) or $TxtValorPercapita<1 ){
                exit("E1;El valor percapita debe un valor númerico mayor a 0;TxtValorPercapita");
            }
            
            $DatosContrato=$obCon->DevuelveValores("contratos", "ID", $idContratoPadre);
            
           
            $DatosMunicipios=$obCon->DevuelveValores("municipios_dane", "ID", $CmbMunicipioPercapita);
            
            $DatosMesServicio = explode("-", $FechaInicialPercapita);
            $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
            $DatosMesServicio = explode("-", $FechaFinalPercapita);
            $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
            
            $Datos["NIT_IPS"]=$DatosContrato["NitIPSContratada"];
            $Datos["Contrato"]=$DatosContrato["Contrato"];
            $Datos["Departamento"]=$DatosMunicipios["CodigoDepartamento"];
            $Datos["Municipio"]=$DatosMunicipios["CodigoMunicipio"];
            $Datos["CodigoDane"]=$DatosMunicipios["CodigoDane"];
            $Datos["PorcentajePoblacional"]=$TxtPorcentajePercapita;
            $Datos["ValorPercapitaXDia"]=$TxtValorPercapita;
            $Datos["FechaInicioPercapita"]=$FechaInicialPercapita;
            $Datos["FechaFinPercapita"]=$FechaFinalPercapita;
            $Datos["CodigoFechaInicioPercapita"]=$MesServicioInicial;
            $Datos["CodigoFechaFinPercapita"]=$MesServicioFinal;
            $Datos["idUser"]=$idUser;
            $Datos["FechaRegistro"]=$DatosContrato["NitIPSContratada"];
            $Datos["FechaActualizacion"]=$DatosContrato["NitIPSContratada"];
            
            $sql=$obCon->getSQLInsert("contrato_percapita", $Datos);
            $obCon->Query($sql);
                  
            print("OK;Percapita Creada");
        break;//fin caso 3
        
        case 4://Recibir un adjunto para un contrato
            
            $contrato_id=$obCon->normalizar($_REQUEST["contrato_id"]);
            $datos_contrato=$obCon->DevuelveValores("contratos", "ID", $contrato_id);
            $Extension="";
            if(!empty($_FILES['adjunto_contrato']['name'])){
                
                $info = new SplFileInfo($_FILES['adjunto_contrato']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['adjunto_contrato']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta="../../".$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$datos_contrato["NitIPSContratada"]."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="contratos/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$contrato_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ct_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['adjunto_contrato']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoContrato($contrato_id, $destino, $Tamano, $_FILES['adjunto_contrato']['name'], $Extension, $idUser);
            }else{
                exit("E1;No se recibió el archivo");
            }
            print("OK;Archivo adjuntado");
           
        break;//Fin caso 4
        
        case 5://eliminar un registro
            
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            if($tabla_id==''){
                exit("E1;No se envio tabla");
            }
            
            if($tabla_id==1){
                $tabla="contratos_adjuntos";
                $DatosAdjunto=$obCon->DevuelveValores("contratos_adjuntos", "ID", $item_id);
                if(file_exists($DatosAdjunto["Ruta"])){
                    unlink($DatosAdjunto["Ruta"]);
                }
            }
            
            $obCon->BorraReg($tabla, "ID", $item_id);
            print("OK;Registro Eliminado");
        break;//Fin caso 5
        
                
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
