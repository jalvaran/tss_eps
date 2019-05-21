<?php
$myPage="MnuRestaurante.php";
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
        $obCon =  new ProcesoVenta($idUser);
        $sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
        $DatosUsuario=$obCon->Query($sql);
        $DatosUsuario=$obCon->FetchArray($DatosUsuario);
        $TipoUser=$DatosUsuario["TipoUser"];   
	$css->IniciaMenu("Restaurante"); 
        $i=0;
        $idMenu=16;
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
        
        
	?>
    
  
 </div>

  
<!--==============================footer=================================-->
<?php 

	$css->Footer();
	
?>



       
</body>

</html>

<?php
ob_end_flush();
?>