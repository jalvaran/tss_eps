<?php
$myPage="MnuIngresos.php";
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
	$NombreUser=$_SESSION['nombre'];
		
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
 
	$css->IniciaMenu("Ingresos"); 
	$css->MenuAlfaIni("Ingresos");
		
	$css->MenuAlfaFin();
	
	$css->IniciaTabs();

            $css->NuevaTabs(1);
                $css->SubTabs("../VAtencion/comprobantes_ingreso.php","_blank","../images/historial3.png","Historial Comprobantes Ingreso");
                $css->SubTabs("../VAtencion/RegistrarIngreso.php","_blank","../images/pago.jpg","Registrar Pago");
                $css->SubTabs("../VAtencion/ComprobantesIngreso.php","_blank","../images/ingreso.jpg","Registrar Ingreso");
                $css->SubTabs("../VAtencion/RegistrarAnticipos.php","_blank","../images/Anticipos.png","Anticipos");    //Uso Futuro
                //$css->SubTabs("../VAtencion/CompraEquipos.php","_blank","../images/equipos.png","Comprar Equipos");//Uso Futuro
            $css->FinTabs();
		
	$css->FinMenu(); 
	
	?>
    
  
 </div>

  
<!--==============================footer=================================-->
<?php 

	$css->Footer();
	
?>



       
</body>

</html>