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
            if($Datos["ClasificacionContrato"]=='OTRO SI'){
                $Datos["Contrato"]=$obCon->normalizar($_REQUEST['CmbContratoPadre']);
                if($Datos["Contrato"]==''){
                    exit("E1;Debe Seleccionar un contrato padre;select2-CmbContratoPadre-container");
                }
                $Datos["NumeroContrato"]=($Datos["Contrato"]." ".$Datos["OtroSi"]);  
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
        
                
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
