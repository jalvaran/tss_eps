<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

if(file_exists("../../../general/clases/mail.class.php")){
    include_once("../../../general/clases/mail.class.php");
}

/* 
 * Clase donde se realizaran procesos para construir recetas
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class Ticket extends conexion{
    
    public function CrearTicket($idProyecto,$TipoTicket,$Prioridad,$idModuloProyecto,$Asunto,$idUser,$idUsuarioDestino) {
        $Datos["idProyecto"]=$idProyecto;
        $Datos["TipoTicket"]=$TipoTicket;
        $Datos["idModuloProyecto"]=$idModuloProyecto;
        $Datos["Prioridad"]=$Prioridad;
        $Datos["FechaApertura"]=date("Y-m-d H:i:s");
        $Datos["FechaActualizacion"]=date("Y-m-d H:i:s");
        $Datos["Asunto"]=$Asunto;
        $Datos["Estado"]=1;
        $Datos["idUsuarioSolicitante"]=$idUser;
        $Datos["idUsuarioActualiza"]=$idUser;
        $Datos["idUsuarioAsignado"]=$idUsuarioDestino;
        
        $sql= $this->getSQLInsert("tickets", $Datos);
        $this->Query($sql);
        $ID= $this->ObtenerMAX("tickets", "ID", 1, "");
        return($ID);
    }
    
    public function AgregarMensajeTicket($idTicket,$Mensaje,$idUser) {
        $FechaHora=date("Y-m-d H:i:s");
        $Datos["idTicket"]=$idTicket;
        $Datos["Mensaje"]=$Mensaje;
        $Datos["Estado"]=1;
        $Datos["Created"]=$FechaHora;
        $Datos["idUser"]=$idUser;
                
        $sql= $this->getSQLInsert("tickets_mensajes", $Datos);
        $this->Query($sql);
        $ID= $this->ObtenerMAX("tickets_mensajes", "ID", 1, "");
        $sql="UPDATE tickets SET FechaActualizacion='$FechaHora',idUsuarioActualiza='$idUser' WHERE ID='$idTicket' ";
        $this->Query($sql);
        return($ID);
    }
    
    public function AgregarAdjuntoMensaje($Ruta,$Tamano,$NombreArchivo,$Extension,$idUser,$idMensaje) {
        $Datos["Ruta"]=$Ruta;
        $Datos["NombreArchivo"]=$NombreArchivo;
        $Datos["Extension"]=$Extension;
        $Datos["Created"]=date("Y-m-d H:i:s");
        $Datos["idUser"]=$idUser;
        $Datos["idMensaje"]=$idMensaje;
        $Datos["Tamano"]=$Tamano;
        $sql= $this->getSQLInsert("tickets_adjuntos", $Datos);
        $this->Query($sql);
        $ID= $this->ObtenerMAX("tickets_adjuntos", "ID", 1, "");
        return($ID);
    }
    
    public function NotificarTicketXMail($idTicket,$idMensaje,$idUser) {
        $obMail=new TS_Mail($idUser);
        $DatosTickets=$this->DevuelveValores("tickets", "ID", $idTicket);
        $DatosMensaje=$this->DevuelveValores("tickets_mensajes", "ID", $idMensaje);
        $idUsuarioRemitente=$DatosTickets["idUsuarioSolicitante"];
        $idUsuarioDestino=$DatosTickets["idUsuarioAsignado"];
        $sql="SELECT Nombre,Apellido,Email FROM usuarios WHERE idUsuarios = '$idUsuarioRemitente'";
        $DatosUsuarioRemitente=$this->FetchAssoc($this->Query($sql));
        $sql="SELECT Nombre,Apellido,Email FROM usuarios WHERE idUsuarios = '$idUsuarioDestino'";
        $DatosUsuarioDestino=$this->FetchAssoc($this->Query($sql));
        $Para=$DatosUsuarioRemitente["Email"].",".$DatosUsuarioDestino["Email"];
        $NombreRemitente=$DatosUsuarioRemitente["Nombre"]." ".$DatosUsuarioRemitente["Apellido"];  
        $Parametros=$this->DevuelveValores("configuracion_general", "ID", 25); //Determina el metrodo de envio del mail
        
        if($Parametros["Valor"]==1){
                
            $EstadoEnvio=$obMail->EnviarMailXPHPNativo($Para, $DatosUsuarioRemitente["Email"], $NombreRemitente, "Ticket $idTicket: ".$DatosTickets["Asunto"], $DatosMensaje["Mensaje"]);
        }else{
            $EstadoEnvio=$obMail->EnviarMailXPHPMailer($Para, $DatosUsuarioRemitente["Email"], $NombreRemitente, "Ticket $idTicket: ".$DatosTickets["Asunto"], $DatosMensaje["Mensaje"]);
        }
        return($EstadoEnvio);
    }
    //Fin Clases
}
