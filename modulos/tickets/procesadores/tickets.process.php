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
            $CmbPrioridad=$obCon->normalizar($_REQUEST["CmbPrioridad"]);
            $TxtAsunto=$obCon->normalizar($_REQUEST["TxtAsunto"]);
            
            $TxtMensaje=$obCon->normalizar($_REQUEST["TxtMensaje"]);
           
            if($CmbUsuarioDestino==''){
                exit("E1;Debe seleccionar un destinatario;select2-CmbUsuarioDestino-container");
            }
            
            if($CmbPrioridad==''){
                exit("E1;Debe seleccionar una prioridad;CmbPrioridad");
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
            $idTicket=$obCon->CrearTicket($CmbProyecto,$CmbTipoTicket,$CmbPrioridad,$CmbModuloProyecto,$TxtAsunto,$idUser,$CmbUsuarioDestino);
            $idMensaje=$obCon->AgregarMensajeTicket($idTicket, $TxtMensaje, $idUser);
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['upAdjuntosTickets1']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets1']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets1']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['upAdjuntosTickets1']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets1']['name'], $Extension, $idUser, $idMensaje);
                
            }
            
            if(!empty($_FILES['upAdjuntosTickets2']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets2']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets2']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets2']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets2']['name'], $Extension, $idUser, $idMensaje);
                
            }
            
            if(!empty($_FILES['upAdjuntosTickets3']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets3']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets3']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets3']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets3']['name'], $Extension, $idUser, $idMensaje);
                
            }
            print("OK;Ticket $idTicket Creado");          
            
        break; //fin caso 1
        
        case 2: //Agregar un Adjunto a un mensaje
            $idMensaje=$obCon->normalizar($_REQUEST["idMensaje"]);
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);
            if($idMensaje==''){
                exit("E1;No se recibió el id del Mensaje");
            }
            if(!empty($_FILES['upAdjuntosTickets']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets']['name'], $Extension, $idUser, $idMensaje);
                
            }else{
                exit("E1;No se recibió un archivo");
            }
            print("OK;Adjunto Agregado");            
            
        break; //fin caso 1
        
        case 3: //Responder a un ticket
            $CmbCerrarTicket=$obCon->normalizar($_REQUEST["CmbCerrarTicket"]); 
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);        
            $TxtMensaje=$obCon->normalizar($_REQUEST["TxtMensaje"]);
           
            if($CmbCerrarTicket==''){
                exit("E1;Debe seleccionar un estado para el ticket;CmbCerrarTicket");
            }
            
            if($TxtMensaje==''){
                exit("E1;Escribe el Mensaje para este Ticket;TxtMensaje");
            }
            
            $idMensaje=$obCon->AgregarMensajeTicket($idTicket, $TxtMensaje, $idUser);
            
            if(!empty($_FILES['upAdjuntosTickets1']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets1']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets1']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['upAdjuntosTickets1']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets1']['name'], $Extension, $idUser, $idMensaje);
                
            }
            
            if(!empty($_FILES['upAdjuntosTickets2']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets2']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets2']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets2']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets2']['name'], $Extension, $idUser, $idMensaje);
                
            }
            
            if(!empty($_FILES['upAdjuntosTickets3']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets3']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets3']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets3']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets3']['name'], $Extension, $idUser, $idMensaje);
                
            }
            $obCon->ActualizaRegistro("tickets", "Estado", $CmbCerrarTicket, "ID", $idTicket);
            /*
            if($CmbCerrarTicket==1){
                $obCon->ActualizaRegistro("tickets", "Estado", 10, "ID", $idTicket);
               
            }else{
                $obCon->ActualizaRegistro("tickets", "Estado", 3, "ID", $idTicket);
            }
             * 
             */
            
            print("OK;Respuesta Agregada");          
            
        break; //fin caso 3
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>