<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/usuarios.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Usuarios($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //dibuje el formulario para agregar ips a un usuario
            $idUsuarioSeleccionado=$obCon->normalizar($_REQUEST["idUsuario"]);
            $DatosUsuario=$obCon->DevuelveValores("usuarios", "idUsuarios", $idUsuarioSeleccionado);
            $css->input("hidden", "TxtUsuarioSeleccionado", "", "TxtUsuarioSeleccionado", "", $idUsuarioSeleccionado, "", "", "", "");
            print("<h3>Asignar IPS al Usuario: $DatosUsuario[Nombre] $DatosUsuario[Apellido]</h3>");
            print('<select id="ips" class="form-control" name="ips[]" multiple="multiple">');                
            $css->Cselect();
            print("<br><br>");
            $css->CrearBotonEvento("btnAsigarIPS", "Guardar", 1, "onclick", "RegistrarIPSUsuario()", "verde", "");
            print("<br><br>");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>LISTADO DE IPS DE ESTE USUARIO</strong>", 3,'C');
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Nombre IPS</strong>", 1);
                    $css->ColTabla("<strong>NIT IPS</strong>", 1);
                    $css->ColTabla("<strong>Eliminar</strong>", 1);
                $css->CierraFilaTabla();
                
                $sql="SELECT r.ID as idItem,r.idIPS, i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON r.idIPS=i.NIT "
                        . "WHERE r.idUsuario='$idUsuarioSeleccionado'";
                $Consulta=$obCon->Query($sql);
                while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosIPS["idItem"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosIPS["Nombre"], 1);
                        $css->ColTabla($DatosIPS["idIPS"], 1);
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
        break;//Fin caso 1
        
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>