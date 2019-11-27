<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/tickets.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Ticket($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear un ticket
            $CmbUsuarioDestino=$obCon->normalizar($_REQUEST["CmbUsuarioDestino"]); 
            $CmbTipoTicket=$obCon->normalizar($_REQUEST["CmbTipoTicket"]);
            $CmbProyecto=$obCon->normalizar($_REQUEST["CmbProyecto"]);
            $CmbModuloProyecto=$obCon->normalizar($_REQUEST["CmbModuloProyecto"]);
            $TxtAsunto=$obCon->normalizar($_REQUEST["TxtAsunto"]);
            
            $TxtMensaje=$obCon->normalizar($_REQUEST["TxtMensaje"]);
           
            if($CmbUsuarioDestino==''){
                exit("E1;Debe seleccionar un destinatario;select2-CmbUsuarioDestino-container");
            }
            
            if($CmbTipoTicket==''){
                exit("E1;Selecciona un tipo de Ticket;CmbTipoTicket");
            }
            if($CmbProyecto==''){
                exit("E1;Selecciona un Proyecto;CmbProyecto");
            }
            if($CmbModuloProyecto==''){
                exit("E1;Selecciona una Fase del proyecto;CmbModuloProyecto");
            }
            if($TxtAsunto==''){
                exit("E1;Escribe el Asunto o Título del Ticket;TxtAsunto");
            }
            if($TxtMensaje==''){
                exit("E1;Escribe el Mensaje para este Ticket;TxtMensaje");
            }
            $idTicket=$obCon->CrearTicket($CmbProyecto,$CmbTipoTicket,$CmbModuloProyecto,$TxtAsunto,$idUser,$CmbUsuarioDestino);
            $idMensaje=$obCon->AgregarMensajeTicket($idTicket, $TxtMensaje, $idUser);
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['upAdjuntosTickets']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets']['name']);
                $Extension=($info->getExtension());  
                
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $destino=$carpeta.$idMensaje.".".$Extension;
                $NombreArchivo=$idMensaje.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino, $_FILES['upAdjuntosTickets']['name'], $Extension, $idUser, $idMensaje);
                
            }else{
                exit("E1;No se envió ningún archivo");
                
            }
            
            print("OK;Ticket $idTicket Creado");          
            
        break; //fin caso 1
        
        case 2: //Borrar IPS de un usuario
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            if($Tabla==1){
                $Tabla="relacion_usuarios_ips";
            }
            $obCon->BorraReg($Tabla, "ID", $idItem);
            print("Registro eliminado");            
            
        break; //fin caso 1
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>