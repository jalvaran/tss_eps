/**
 * Controlador para generar los reportes de venta e ingresos de plataformas como sistecredito
 * JULIAN ALVARAN 2019-04-08
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


function LimpiarDivs(){
    document.getElementById("DivProceso").innerHTML="";
    
}
function CrearReportePlataformas(){
    CrearReporteIngresos();
    CrearReporteVentas();
}
/*
 * Genera el reporte de ingresos por plataformas
 * @returns {undefined}
 */
function CrearReporteIngresos(){
    document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportesIngresos").innerHTML="Obteniendo Información";
    var TxtFechaInicial = document.getElementById('FechaInicial').value;
    var TxtFechaFinal = document.getElementById('FechaFinal').value;
    var Plataforma = document.getElementById('Plataforma').value;
    
      
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('FechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('FechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('FechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('FechaFinal').style.backgroundColor="white";
    }
    
        
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('Plataforma', Plataforma);       
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        
        
        $.ajax({
        url: './Consultas/ReportesPlataformas.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          
            LimpiarDivs();              
            document.getElementById("DivReportesIngresos").innerHTML=data;        
                
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

function CrearReporteVentas(){
    document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportesVentas").innerHTML="Obteniendo Información";
    var TxtFechaInicial = document.getElementById('FechaInicial').value;
    var TxtFechaFinal = document.getElementById('FechaFinal').value;
    var Plataforma = document.getElementById('Plataforma').value;
    
      
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('FechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('FechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('FechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('FechaFinal').style.backgroundColor="white";
    }
    
        
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('Plataforma', Plataforma);       
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        
        
        $.ajax({
        url: './Consultas/ReportesPlataformas.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          
            LimpiarDivs();              
            document.getElementById("DivReportesVentas").innerHTML=data;        
                
                     
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  


function ExportarTablaToExcel(idTabla){
    excel = new ExcelGen({
        "src_id": idTabla,
        "show_header": true,
        "type": "table"
    });
    excel.generate();
}


document.getElementById('BtnMuestraMenuLateral').click();