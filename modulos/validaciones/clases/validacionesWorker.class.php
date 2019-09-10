<?php

if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
        
class My extends Thread {
    
    
    public function run() {
        print("Prueba");
    }
   
    
    
    //Fin Clases
}
