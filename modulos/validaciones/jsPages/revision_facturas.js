/**
 * Controlador para revisar las facturas con posibles problemas en el cruce
 * JULIAN ALVARAN 2019-11-06
 * TECHNO SOLUCIONES SAS 
 * 
 */


function ConstruirTablaCerosIzquierda(){
    document.getElementById("DivTab10").innerHTML='<div id="GifProcess">Construyendo la tabla con la información de las Facturas para revisión...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        $.ajax({
        url: './procesadores/revision_facturas.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               document.getElementById("DivTab10").innerHTML="";
                alertify.success(respuestas[1]);  
                 
                MostrarTablaConFacturasCerosIzquierda();
                
            }else if(respuestas[0]==="E1"){
                document.getElementById("DivTab10").innerHTML="";
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);                
                return;                
            }else{
               document.getElementById("DivTab10").innerHTML="";
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById("DivTab10").innerHTML="";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function MostrarTablaConFacturasCerosIzquierda(Page=1){
    document.getElementById("DivTab10").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/revision_facturas.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab10').innerHTML=data;
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaFacturasCerosIzquierda(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPageFacturasCeroIzquierda').value;
    }
    MostrarTablaConFacturasCerosIzquierda(Page);
}