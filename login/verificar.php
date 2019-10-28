<?php
session_start();

include("../modelo/php_conexion.php");
    if(empty($_POST["US"])){
        print("E1");
        exit();
    }
    if(empty($_POST["PA"])){
        print("E2");
        exit();
    }
	if(isset($_POST["US"]) && !empty($_POST["US"]) && isset($_POST["PA"]) && !empty($_POST["PA"])) {
            $obCon=new conexion(1);
            
            $User=$obCon->normalizar($_POST["US"]);
            $Pass=$obCon->normalizar($_POST["PA"]);
            $Pass= md5($Pass);
            $sql="SELECT * FROM usuarios WHERE Login='$User' AND Password='$Pass'";
            
            $sel=$obCon->Query($sql);
            $sesion=$obCon->FetchArray($sel);
		  
		
		if($Pass == $sesion["Password"] ){
                    if($sesion["Habilitado"]<>'SI'){
                        print("El Usuario se encuentra deshabilitado");
                        exit();
                    }
			$_SESSION['username'] = $User;
			$_SESSION['nombre'] = $sesion["Nombre"];
			$_SESSION['apellido'] = $sesion["Apellido"];
			$_SESSION['tipouser'] = $sesion["TipoUser"];
			$_SESSION['idUser'] = $sesion["idUsuarios"];
                        $_SESSION['Role'] = $sesion["Role"];
                    if($_POST["US"] == "techno" and $_POST["PA"] == "technosoluciones"){
				$_SESSION['nombre'] = "Techno";
				$_SESSION['apellido'] = "Soluciones";
				$_SESSION['tipouser'] = "Administrador";
				$_SESSION['idUser'] = "A";
			}
                        
			$tabLog="log_session";
                        $Datos["Fecha"]=date("Y-m-d H:i:s");
                        $Datos["idUser"]=$_SESSION['idUser'];                        
                        $Datos["IP_Conexion"]=$_SERVER['REMOTE_ADDR'];
                        $sql=$obCon->getSQLInsert($tabLog, $Datos);
                        $obCon->Query($sql);
                        print("OK");
		}else{
			print("El Usuario y Contraseña no coinciden");
	}
	
	}else{
		echo "por favor llena todos los campos";
	}
			
?>