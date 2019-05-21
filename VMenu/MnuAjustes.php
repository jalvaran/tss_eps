<?php
$myPage="MnuAjustes.php";
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
 
	$css->IniciaMenu("Ajustes y Servicios del Sistema"); 
	$css->MenuAlfaIni("Generales");
            $css->SubMenuAlfa("Backups",2);
            $css->SubMenuAlfa("Politicas de Acceso",3);
            $css->SubMenuAlfa("Parametrizacion",4);   
	$css->MenuAlfaFin();
	
	$css->IniciaTabs();
	
		$css->NuevaTabs(1);
			
                        $css->SubTabs("../VAtencion/config_tiketes_promocion.php","_self","../images/tiketes.png","Configurar Tikete de Promocion");
		$css->FinTabs();
		$css->NuevaTabs(2);
                    $css->SubTabs("../VAtencion/AgregueParametros.php","_self","../images/database.png","Verificar Disponibilidad Local");
                    $css->SubTabs("../VAtencion/backups.php","_self","../images/backup.png","Crear Backup");
                $css->FinTabs();          
                $css->NuevaTabs(3);
			
                        $css->SubTabs("../VAtencion/paginas_bloques.php","_self","../images/seguridadinformatica.png","Agregar Paginas a Un Tipo de Usuario");
		     
		$css->FinTabs();
                $css->NuevaTabs(4);
			
                        $css->SubTabs("../VAtencion/parametros_contables.php","_self","../images/parametros.png","Parametros Contables");
                        $css->SubTabs("../VAtencion/conceptos.php","_self","../images/listado.png","Lista de Conceptos Contables");
                        $css->SubTabs("../VAtencion/CreacionConceptos.php","_self","../images/conceptos.png","Crear Conceptos Contables");
		
                $css->FinTabs();
		
	$css->FinMenu(); 
	
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