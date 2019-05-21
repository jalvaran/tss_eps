<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/usuarios.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Usuarios($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Registrar una o varias IPS a un usuario
            $idUsuario=$obCon->normalizar($_REQUEST["idUsuario"]); 
            $ips=explode(",",$obCon->normalizar($_REQUEST["ips"])); 
            $DatosUsuario=$obCon->DevuelveValores("usuarios", "idUsuarios", $idUsuario);
            if($DatosUsuario["Role"]=='IPS' AND count($ips)>1){
                print("E1;Error este usuario tiene Role de IPS por lo tanto no es permitido asignar mas de una IPS");
                exit();
            }
            foreach ($ips as $idIps){
                
                $obCon->AgregueIPSAUsuario($idUsuario, $idIps);
            }
            
            print("OK;Registros realizados");            
            
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