<?php
$myPage="MnuSalud.php";
include_once("../sesiones/php_control.php");
?>
<!DOCTYPE html>
<script src="js/funciones.js"></script>
<html lang="es">
     <head>
	 <title>TS5</title>
     <meta charset="utf-8">
	 
	 
	 
	 <?php
	
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
	
	$css =  new CssIni();

	$css->CabeceraIni(); 
	//$css->BlockMenuIni(); 
	$css->CabeceraFin(); 
	
 ?>
 
 
 

<!--==============================Content=================================-->

<div class="content"><div class="ic">TECHNO SOLUCIONES SAS</div>
  
    
	<?php 
        $obCon =  new ProcesoVenta($idUser);
        $sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
        $DatosUsuario=$obCon->Query($sql);
        $DatosUsuario=$obCon->FetchArray($DatosUsuario);
        $TipoUser=$DatosUsuario["TipoUser"];   
	$css->IniciaMenu("Gestión de Cartera en la Salud"); 
        $i=0;
        $idMenu=26;
        $Datos=$obCon->ConsultarTabla("menu_pestanas", "WHERE idMenu='$idMenu' AND Estado='1' ORDER BY Orden");
        while($DatosPestanas=$obCon->FetchArray($Datos)){
            $Submenus[$i]=$DatosPestanas["ID"];
            if($i==0){
            $css->MenuAlfaIni($DatosPestanas["Nombre"]);
            }else{
                $css->SubMenuAlfa($DatosPestanas["Nombre"],$DatosPestanas["Orden"]);
            }
            $i++;
        }
        $css->MenuAlfaFin();
        $css->IniciaTabs();
            $i=0;
            foreach($Submenus as $idPestana){
               $i++;
                $css->NuevaTabs($i);
                    $Datos=$obCon->ConsultarTabla("menu_submenus", "WHERE idPestana='$idPestana' AND Estado='1' ORDER BY Orden");
                    while ($DatosPaginas=$obCon->FetchArray($Datos)){
                        if($DatosUsuario["TipoUser"]=="administrador"){
                        $Visible=1;
                        }else{
                            $Visible=0;
                            $sql="SELECT ID FROM paginas_bloques WHERE TipoUsuario='$TipoUser' AND Pagina='$DatosPaginas[Pagina]' AND Habilitado='SI'";
                            $DatosUser=$obCon->Query($sql);
                            $DatosUser=$obCon->FetchArray($DatosUser);
                            if($DatosUser["ID"]>0){
                                $Visible=1;
                            }
                        }
                        if($Visible==1){
                            $DatosCarpeta=$obCon->DevuelveValores("menu_carpetas", "ID", $DatosPaginas["idCarpeta"]);
                            $css->SubTabs($DatosCarpeta["Ruta"].$DatosPaginas["Pagina"],$DatosPaginas["Target"],"../images/".$DatosPaginas["Image"],$DatosPaginas["Nombre"]);
                        }
                    }
                $css->FinTabs();
            }
        
        $css->FinMenu();
        /*
	$css->IniciaMenu("Compra de Productos y Servicios"); 
	$css->MenuAlfaIni("Compras");
               
	$css->MenuAlfaFin();
	
	$css->IniciaTabs();
	
		$css->NuevaTabs(1);
                        $css->SubTabs("../VAtencion/factura_compra.php","_self","../images/historial2.png","Historial");
			$css->SubTabs("../VAtencion/vista_compras_productos.php","_self","../images/historial.png","Historial de Productos Comprados");
                        $css->SubTabs("../VAtencion/vista_compras_productos_devoluciones.php","_self","../images/devoluciones.png","Historial de Productos Devueltos");
                        $css->SubTabs("../VAtencion/vista_compras_servicios.php","_self","../images/servicios_compras.png","Historial de Compras Servicios");
                        $css->SubTabs("../VAtencion/RegistraCompra.php","_self","../images/compras.png","Registrar una Compra");
		$css->FinTabs();
		
		
	$css->FinMenu(); 
	
         * 
         */
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