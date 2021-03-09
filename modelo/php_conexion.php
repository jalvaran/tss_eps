<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'php_mysql_i.php';

class conexion extends db_conexion{
    
    
    function __construct($idUser){
        $idUserR=$this->normalizar($idUser);		
        $this->consulta =$this->Query("SELECT Nombre, TipoUser FROM usuarios WHERE idUsuarios='$idUserR'");
        $this->fetch = $this->FetchArray($this->consulta);
        $this->NombreUser = $this->fetch['Nombre'];
        $this->idUser=$idUserR;
        $this->TipoUser=$this->fetch['TipoUser'];
        
    }
    
    /**
     * Verifica los permisos que tiene un usario
     * @param type $VectorPermisos
     * @return boolean
     */
    public function VerificaPermisos($VectorPermisos) {
        if($this->TipoUser<>"administrador"){
            $Page=$VectorPermisos["Page"];        
            $Consulta=  $this->ConsultarTabla("paginas_bloques", " WHERE Pagina='$Page' AND TipoUsuario='$this->TipoUser' AND Habilitado='SI'");
            $PaginasUser=  $this->FetchArray($Consulta);
            if($PaginasUser["Pagina"]==$Page){
                return true;
            }
            return false;
        }
        return true;
    }
    
    public function getUniqId($prefijo='') {
         return (str_replace(".","",uniqid($prefijo, true)));
     }

}
