/**
 * Controlador para las actas
 * JULIAN ALVARAN 2019-09-10
 * TECHNO SOLUCIONES SAS 
 * 
 */

function CargarHistorialActas(Page=1){
    document.getElementById("DivTab1").innerHTML='<div id="GifProcess">Cargando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    //var CmbEPS=document.getElementById('CmbEPS').value;
    //var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        //form_data.append('CmbIPS', CmbIPS);   
        //form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/ActasConciliacion.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab1').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePagina(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPage').value;
    }
    CargarHistorialActas(Page);
}


function ExportarExcel(db,Tabla,st){
    //document.getElementById("DivMensajes").innerHTML="Exportando...";
    document.getElementById("DivMensajes").innerHTML='<div id="GifProcess">Exportando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var idBoton="BtnExportarExcelCruce";
    document.getElementById(idBoton).disabled=true; 
    
    //var CmbEPS=document.getElementById('CmbEPS').value;
    //var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Opcion', 2); 
        
        form_data.append('Tabla', Tabla);
        form_data.append('db', db);
        form_data.append('st', st);
              
    $.ajax({
        
        url: '../../general/procesadores/GeneradorCSV.process.php',
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(idBoton).disabled=false; 
               console.log(data)
                
            document.getElementById("DivMensajes").innerHTML=data;
                
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
