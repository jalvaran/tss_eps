<?php
/**
 * Pagina base para la plataforma TSS
 * 2018-11-27, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="Menu.php";
$myTitulo="Plataforma TS5";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];

$css->PageInit($myTitulo);
    
        $css->CrearDiv("DivMenu", "container", "center", 1, 1);        
        
	$css->IniciaMenu("Menú"); 
        
        $css->IniciaTabs();
        
        $css->CierraTabs();
        
        $sql="SELECT m.Nombre, m.Pagina,m.Target,m.Image,m.Orden, c.Ruta FROM menu m "
        . "INNER JOIN menu_carpetas c ON c.ID=m.idCarpeta WHERE m.Estado=1 ORDER BY m.Orden ASC";
        $Consulta=$obCon->Query($sql);
        while($DatosMenu=$obCon->FetchArray($Consulta)){

            if($DatosUsuario["TipoUser"]=="administrador"){
                $Visible=1;
            }else{
                $Visible=0;
                $sql="SELECT ID FROM paginas_bloques WHERE TipoUsuario='$TipoUser' AND Pagina='$DatosMenu[Pagina]' AND Habilitado='SI'";
                $DatosUser=$obCon->Query($sql);
                $DatosUser=$obCon->FetchArray($DatosUser);
                if($DatosUser["ID"]>0){
                    $Visible=1;
                }
            }
            if($Visible==1){
                
                $css->SubTabs("../../VMenu/".$DatosMenu["Ruta"].$DatosMenu["Pagina"],$DatosMenu["Target"],"../../images/".$DatosMenu["Image"],$DatosMenu["Nombre"]);
            }    
        }
                
        $css->FinMenu();
             
        $css->Cdiv();
        $css->CrearDiv("DivProcesosInternos", "", "center", 1, 1); //Muestra los resultados de los procesos background

        $css->CerrarDiv();
        

$css->PageFin();

//print('<script src="../../general/js/notificaciones.js"></script>');
$ip=$_SERVER['REMOTE_ADDR'];
$ipServer=$_SERVER['SERVER_ADDR'];
/**
 * Deshabilitar para trabajar en la web
 */
//if($ip==$ipServer){
   // print('<script src="../../general/js/backups.js"></script>');
   // print('<script src="../../general/js/ProcesosConFacturas.js"></script>');
//}

$css->Cbody();
$css->Chtml();

?>