<?php
/* 
 * Clase donde se realizaran procesos de compras u otros modulos.
 * Julian Alvaran
 * Techno Soluciones SAS
 */
//include_once '../../php_conexion.php';
class CreaMenu extends ProcesoVenta{
    
    public function listar_archivos($carpeta, $Vector) {
        if(is_dir($carpeta)){
            if($dir = opendir($carpeta)){
                while(($archivo = readdir($dir)) !== false){
                    if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){
                        echo '<li><a target="_blank" href="'.$carpeta.'/'.$archivo.'">'.$archivo.'</a></li>';
                    }
                }
                closedir($dir);
            }
        }
    }
     
    //Fin Clases
}