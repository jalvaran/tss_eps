<?php
header('Content-Type: text/html charset=UTF-8'); 
$myPage="Menu.php";
include_once("../sesiones/php_control.php");
?>
<!DOCTYPE html>
<html lang="es">
     <head>
	 <title>TS5</title>
     <meta charset="ISO-8859-1">
	 <?php
	 

	include_once("../modelo/php_conexion.php");
	include_once("css_construct.php");

	if (!isset($_SESSION['username']))
	{
	  exit("No se ha iniciado una sesion <a href='../index.php' >Iniciar Sesion </a>");
	  
	}

	$NombreUser=$_SESSION['nombre'];
	$idUser=$_SESSION['idUser'];	
	
	 ?>
       
     </head>
     <body  class="">

<!--==============================header=================================-->

 <?php 
    $obCon = new db_conexion();
	$css =  new CssIni();

	$css->CabeceraIni(); 
	//$css->BlockMenuIni(); 
	$css->CabeceraFin(); 
	
 ?>
 
 
 

<!--==============================Content=================================-->

<div class="content"><div class="ic">TECHNO SOLUCIONES SAS</div>
  
    
<?php 

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchArray($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];                
$css->IniciaMenu("Bienvenido $NombreUser que deseas hacer?"); 
$css->MenuAlfaIni("Menu");
//$css->SubMenuAlfa("Otro",2);
$css->MenuAlfaFin();

$css->IniciaTabs();
    $css->NuevaTabs(1);

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
                $css->SubTabs($DatosMenu["Ruta"].$DatosMenu["Pagina"],$DatosMenu["Target"],"../images/".$DatosMenu["Image"],$DatosMenu["Nombre"]);
            }    
        }

$css->FinTabs();
$css->FinMenu(); 
$css->CrearDiv("DivProcesosInternos", "", "center", 1, 1); //Muestra los resultados de los procesos background

$css->CerrarDiv();

if($TipoPC=="Server"){
    print("<script>
    page='../VAtencion/Consultas/KardexFacturas.php?Autorizado=1&idPreventa=';
    var myVar = setInterval(function(){ myTimer(page) }, 5000);
    </script>");
}
?>
    
  
 </div>

<!--==============================footer=================================-->
<?php 

$css->Footer();

?>

       <script>
      $(document).ready(function(){ 
         $(".bt-menu-trigger").toggle( 
          function(){
            $('.bt-menu').addClass('bt-menu-open'); 
          }, 
          function(){
            $('.bt-menu').removeClass('bt-menu-open'); 
          } 
        ); 
      }) 
    </script>
       
</body>

</html>