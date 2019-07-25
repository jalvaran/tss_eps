<?php 
if(isset($_REQUEST["idDocumento"])){
    $myPage="GeneradorExcel.php";
    
    include_once("../clases/ClasesDocumentosExcel.class.php");
    session_start();    
    $idUser=$_SESSION['idUser'];
   
    $obCon=new TS_Excel($idUser);
    $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
    
    switch ($idDocumento){
        case 1: //Formato de conciliaciones masivas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $obCon->GenerarFormatoConciliacionesMasivas($db,$CmbIPS);
            
        break;//Fin caso 1
        
    }
}else{
    print("No se recibió parametro de documento");
}

?>