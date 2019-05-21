<link rel="icon" href="../images/technoIco.png">
<link rel="shortcut icon" href="../images/technoIco.ico" />
<link rel="stylesheet" href="css/touchTouch.css">
<link rel="stylesheet" href="css/style.css">
<script src="js/jquery.js"></script>
<script src="js/jquery-migrate-1.1.1.js"></script>
<script src="js/jquery.equalheights.js"></script>
<script src="js/jquery.ui.totop.js"></script>
<script src="js/jquery.tabs.min.js"></script>
<script src="js/touchTouch.jquery.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/funciones.js"></script>
     
<?php

//////////////////////////////////////////////////////////////////////////
////////////Clase para iniciar css ///////////////////////////////////
////////////////////////////////////////////////////////////////////////

class CssIni{
	
	function __construct(){
		
		
		
	}
	
	/////////////////////Inicio una cabecera
	
	function CabeceraIni(){
		
		print('
			  <header> 
  <div class="container_12">
   <div class="grid_12"> 
    
    <h1><a href="Menu.php"><img src="../images/header-logo.png" style="width: 350px;"></a> </h1>
    <div class="menu_block">
					
		');
	}
	
	/////////////////////Cierro la Cabecera de la pagina
	
	function CabeceraFin(){
		
		print('
				<div class="clear"></div>
          </div>
      </div>
</header>
		
		');
	}
	
	function BlockMenuIni(){
		
		print('
			 <div class="menu_block">


    <nav id="bt-menu" class="bt-menu">
        <a href="#" class="bt-menu-trigger"><span>Menu</span></a>
        <ul>
          <li class="bt-icon"><a href="index.html">Inicio</a></li>
          <li class="current bt-icon"><a href="index-2.html">Menú</a></li>
         <li class="bt-icon"><a href="index-5.html">Contacto</a></li>
		  
         
        </ul>
      </nav>
    
 <div class="clear"></div>
</div>
					
		');
	}
	
////////////////////////////Inicia un Alfa Menu	

	
function MenuAlfaIni($Title){
		
		print('
			 <div class="div-nav  ">
             <div class="grid_12">
                 <ul class="nav">
                    <li class="selected"><a href="#tab-1" class="">'.$Title.'</a></li>
                    
					
		');
	}	
	
	
	
////////////////////////////SubMenu Alfa

	
function SubMenuAlfa($Title,$tab){
		
		print('
			 <li><a href="#tab-'.$tab.'">'.$Title.'</a></li>
          				
		');
	}	
		
	////////////////////////////FIN un Alfa Menu	

	
function MenuAlfaFin(){
		
		print(' </ul>
             </div>    
             </div>
					
		');
	}	
		
////////////////////////////7Inicia el Menu		
		
function IniciaMenu($Title){
		
		print('
		<div class="container_12">
			<div class="grid_12">
			  <h3 class="head2">'.$Title.'</h3>
			</div>  
			 <div class="tabs tb gallery">
             
            
					
		');
	}
	

////////////////////////////Nuevas tabs		
		
function IniciaTabs(){
		
		print('
		<div class="div-tabs">
              
		');
	}
	
	////////////////////////////Nueva Menu Grafico		
		
function FinTabs(){
		
		print('
		</div>
         
		');
	}
	
	////////////////////////////Nueva Menu Grafico		
		
function NuevaTabs($Num){
		
		print('
		
              <div  id="tab-'.$Num.'" class="tab-content gallery1">
		');
	}
	
	
	////////////////////////////Nueva Menu Grafico		
		
function SubTabs($link,$target,$image,$SubTitle){
		
		print('
		
              <div class="grid_3">
                    <a href="'.$link.'" target="'.$target.'" class="gal"><img src="'.$image.'" alt="" style="width: 120px;height: 120px;"><span></span></a>
                    <div class="col2"><span class="col3"><a href="'.$link.'" target="'.$target.'">'.utf8_encode($SubTitle).'</a></span></div>
                  </div>
		');
	}
////////////////////////////Fin el Menu	
	
	function FinMenu(){
		print('
			</div>	</div>		
             </div>
        ');
	}
	/////Crea botones con despliegue
	


////////////////////////////Crear Footer	
		
function Footer(){
		$Year=date("Y");
		print('<footer>    
  <div class="container_12">
    <div class="grid_6 prefix_3">
       <a href="../index.php" class="f_logo"><img src="../images/header-logo.png" alt=""></a>
      <div class="copy">
      &copy; '.$Year.' | <a href="#">Privacy Policy</a> <br> Software  designed by <a href="http://technosoluciones.com.co/" rel="nofollow" target="_blank">Techno Soluciones SAS</a>
      </div>
    </div>
  </div>
</footer>
		');
	}

////////////////////////////Crea boton desplegable
	
	function CreaBotonDesplegable($NombreBoton,$TituloBoton){
	
		
	print('<li><a href="#'.$NombreBoton.'" role="button" class="btn" data-toggle="modal" title="'.$TituloBoton.'">
			<span class="badge badge-success">'.$TituloBoton.'</span></a></li>');

	}	
	
	function CreaBotonAgregaPreventa($Page,$idUser){
		
	print('	<a class="brand" href="'.$Page.'?BtnAgregarPreventa='.$idUser.'">Agregar Preventa</a>');

	}	
	
	
	/////////////////////Crea un Formulario
	
	function CrearForm($nombre,$action,$method,$target){
		print('<li><form name= "'.$nombre.'" action="'.$action.'" id="'.$nombre.'" method="'.$method.'" target="'.$target.'">');
		
	}
	
	
	/////////////////////Cierra un Formulario
	
	function CerrarForm(){
		print('</li></form>');
		
	}
	
	
	/////////////////////Crea un Select
	
	function CrearSelect($nombre,$evento){
		print('<select name="'.$nombre.'" onchange="'.$evento.'">');
		
	}
	
	/////////////////////Cierra un Select
	
	function CerrarSelect(){
		print('</select>');
		
	}
	
	
	/////////////////////Crea un Option Select
	
	function CrearOptionSelect($value,$label,$selected){
		
		if($selected==1)
			print('<option value='.$value.' selected>'.$label.'</option>');
		else
			print('<option value='.$value.'>'.$label.'</option>');
		
	}
	
	
	/////////////////////Crea un Cuadro de texto input
	
	function CrearInputText($nombre,$type,$label,$value,$placeh,$color,$TxtEvento,$TxtFuncion,$Ancho,$Alto,$ReadOnly,$Required){
		
		if($ReadOnly==1)
			$ReadOnly="readonly";
		else
			$ReadOnly="";
		
		if($Required==1)
			$Required="required";
		else
			$Required="";
		
			print('<strong style="color:'.$color.'">'.$label.'<input name="'.$nombre.'" value="'.$value.'" type="'.$type.'" id="'.$nombre.'" placeholder="'.$placeh.'" '.$TxtEvento.' = "'.$TxtFuncion.'" 
			'.$ReadOnly.' '.$Required.' autocomplete="off" style="width: '.$Ancho.'px;height: '.$Alto.'px;"></strong>');
		
	}
	
	/////////////////////Crea un Boton Submit
	
	function CrearBoton($nombre,$value){
		print('<input type="submit" id="'.$nombre.'"  name="'.$nombre.'" value="'.$value.'" class="btn btn-primary">');
		
	}
	
	/////////////////////Crea un Cuadro de Dialogo
	
	function CrearCuadroDeDialogo($id,$title){
		
		print('<div id="'.$id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
       
          	
            <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    	        <h3 id="myModalLabel">'.$title.'</h3>
            </div>
            <div class="modal-body">
           	    <div class="row-fluid">
	               
    	            <div class="span6">
                    	
						
                   
            
        ');
		
	}
		
	/////////////////////Cierra un Cuadro de Dialogo
	
	function CerrarCuadroDeDialogo(){
		print(' </div>
                </div>
            </div>
            <div class="modal-footer">
        	    <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> <strong>Cerrar</strong></button>
            	
            </div></div>');
		
	}
	
	
	/////////////////////Crear una Tabla
	
	function CrearTabla(){
		print('<table class="table table-bordered table table-hover" >');
		
	}
	
	/////////////////////Crear una fila para una tabla
	
	function FilaTabla($FontSize){
		print('<tr style="font-size:'.$FontSize.'px">');
		
	}
	
	function CierraFilaTabla(){
		print('</tr>');
		
	}
	
	/////////////////////Crear una columna para una tabla
	
	function ColTabla($Contenido,$ColSpan){
		print('<td colspan="'.$ColSpan.'">'.$Contenido.'</td>');
		
	}
	
	function CierraColTabla(){
		print('</td>');
		
	}
	/////////////////////Cierra una tabla
	
	function CerrarTabla(){
		print('</table>');
		
	}
	
	/////////////////////Crear una columna para una tabla
	
	function ColTablaDel($Page,$tabla,$IdTabla,$ValueDel,$idPre){
		print('<td>
                  	<a href="'.$Page.'?del='.$ValueDel.'&TxtTabla='.$tabla.'&TxtIdTabla='.$IdTabla.'&TxtIdPre='.$idPre.'" title="Eliminar de la Lista">
               		<i class="icon-remove"></i>
                                    </a>
                                </td>');
		
	}
	
	/////////////////////Crear una columna para enviar una variable por URL
	
	function ColTablaVar($Page,$Variable,$Value,$idPre,$Title){
		print('<td><a href="'.$Page.'?'.$Variable.'='.$Value.'&TxtIdPre='.$idPre.'" title="'.$Title.'">'.$Title.'</a></td>');
                               
		
	}
	
	/////////////////////Crear una columna con un formulario
	
	function ColTablaFormInputText($FormName,$Action,$Method,$Target,$TxtName,$TxtType,$TxtValue,$TxtLabel,$TxtPlaceh,$TxtColor,$TxtEvento,$TxtFuncion,$TxtAncho,$TxtAlto,$ReadOnly,$Required,$TxtHide,$ValueHide,$idPreventa){
				
		print('<td>');
		$this->CrearForm($FormName,$Action,$Method,$Target);
		$this->CrearInputText($TxtHide,"hidden","",$ValueHide,"","","","","","","","");
		$this->CrearInputText("CmbPreVentaAct","hidden","",$idPreventa,"","","","",0,0,0,0);
		$this->CrearInputText($TxtName,$TxtType,$TxtLabel,$TxtValue,$TxtPlaceh,$TxtColor,$TxtEvento,$TxtFuncion,$TxtAncho,$TxtAlto,$ReadOnly,$Required);
		print("<input type='submit' name='BtnEditar' value='' style='width: 10px;height: 10px;'>");
		$this->CerrarForm();
		print('</td>');
                               
		
	}
	
	
	/////////////////////Crear una columna con un formulario
	
	function ColTablaInputText($TxtName,$TxtType,$TxtValue,$TxtLabel,$TxtPlaceh,$TxtColor,$TxtEvento,$TxtFuncion,$TxtAncho,$TxtAlto,$ReadOnly,$Required){
		print('<td>');
		
		$this->CrearInputText($TxtName,$TxtType,$TxtLabel,$TxtValue,$TxtPlaceh,$TxtColor,$TxtEvento,$TxtFuncion,$TxtAncho,$TxtAlto,$ReadOnly,$Required);
		
		print('</td>');
                               
		
	}
	
	/////////////////////Crear una columna con un formulario
	
	function ColTablaBoton($nombre,$value){
		print('<td>');
		
		$this->CrearBoton($nombre,$value);
		
		print('</td>');
                               
		
	}
	
	
	function CreaMenuBasico($Title){
		print('<div id="MenuBasico">
			<ul class="nav">
				
				<li><a href="">'.$Title.'</a>
					<ul>
						
						
						
					
				
	');
		
		                              
		
	}
	
	function CreaSubMenuBasico($Title,$Link){
		print('<li><a href="'.$Link.'" target="_blank">'.$Title.'</a></li>');
	}
	
	function CierraMenuBasico(){
		print('</ul></li></ul></div>');
	}
	
	function CrearImageLink($page,$imagerute,$target){
		print('<a href="'.$page.'" target="'.$target.'"><img src="'.$imagerute.'"></a>');
	}
        /////////////////////Crear un DIV
	
	function CrearDiv($ID, $Class, $Alineacion,$Visible, $Habilitado){
            if($Visible==1)
                $V="block";
            else
                $V="none";
            
            if($Habilitado==1) ///pensado a futuro, aun no esta en uso
                $H="true";
            else
                $H="false";
            print("<div id='$ID' class='$Class' align='$Alineacion' style='display:$V;' >");
		
	}
         /////////////////////Crear un DIV
	
	function CerrarDiv(){
            print("</div>");
		
	}
//Fin Clases        
}
	
?>