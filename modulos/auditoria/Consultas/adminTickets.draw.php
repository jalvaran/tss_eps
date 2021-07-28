<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //El informe de tickets
            
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            $CmbEstado=$obCon->normalizar($_REQUEST["CmbEstado"]);
            $CmbProyectosTicketsListado=$obCon->normalizar($_REQUEST["CmbProyectosTicketsListado"]);  
            $CmbModulosTicketsListado=$obCon->normalizar($_REQUEST["CmbModulosTicketsListado"]);
            $CmbTiposTicketsListado=$obCon->normalizar($_REQUEST["CmbTiposTicketsListado"]);
            if($FechaInicial==''){
                exit("E1;Debes seleccionar una Fecha Inicial;FechaInicial");
            }
            if($FechaFinal==''){
                exit("E1;Debes seleccionar una Fecha Final;FechaFinal");
            }
            if($CmbEstado==''){
                $CmbEstado=0;
            }
            if($CmbProyectosTicketsListado==''){
                $CmbProyectosTicketsListado=0;
            }
            if($CmbModulosTicketsListado==''){
                $CmbModulosTicketsListado=0;
            }
            if($CmbTiposTicketsListado==''){
                $CmbTiposTicketsListado=0;
            }
            $page="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=38&FechaInicial=$FechaInicial&FechaFinal=$FechaFinal"; 
            $page.="&CmbEstado=$CmbEstado&CmbProyectosTicketsListado=$CmbProyectosTicketsListado&CmbModulosTicketsListado=$CmbModulosTicketsListado&CmbTiposTicketsListado=$CmbTiposTicketsListado";
            $Target="FramePDF";
            //$Target="_blank";
            print("<a id='LinkPDF' target='$Target' href='$page'></a>");
                        
        break; //Fin caso 1
        
        case 2: //El informe de gestion
            
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            $CmbEstado=$obCon->normalizar($_REQUEST["CmbEstado"]);
            $CmbProyectosTicketsListado=$obCon->normalizar($_REQUEST["CmbProyectosTicketsListado"]);  
            $CmbModulosTicketsListado=$obCon->normalizar($_REQUEST["CmbModulosTicketsListado"]);
            $CmbTiposTicketsListado=$obCon->normalizar($_REQUEST["CmbTiposTicketsListado"]);
            $usuario_id=$obCon->normalizar($_REQUEST["usuario_id"]);
            if($FechaInicial==''){
                exit("E1;Debes seleccionar una Fecha Inicial;FechaInicial");
            }
            if($FechaFinal==''){
                exit("E1;Debes seleccionar una Fecha Final;FechaFinal");
            }
            if($CmbEstado==''){
                $CmbEstado=0;
            }
            if($CmbProyectosTicketsListado==''){
                $CmbProyectosTicketsListado=0;
            }
            if($CmbModulosTicketsListado==''){
                $CmbModulosTicketsListado=0;
            }
            if($CmbTiposTicketsListado==''){
                $CmbTiposTicketsListado=0;
            }
            $page="procesadores/informes_excel.process.php?Accion=1&FechaInicial=$FechaInicial&FechaFinal=$FechaFinal"; 
            $page.="&CmbEstado=$CmbEstado&CmbProyectosTicketsListado=$CmbProyectosTicketsListado&CmbModulosTicketsListado=$CmbModulosTicketsListado&CmbTiposTicketsListado=$CmbTiposTicketsListado";
            $page.="&usuario_id=$usuario_id"; 
            
            //$Target="FramePDF";
            $Target="_blank";
            print("<a id='LinkExcel' target='$Target' href='$page'></a>");
                        
        break; //Fin caso 2
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>