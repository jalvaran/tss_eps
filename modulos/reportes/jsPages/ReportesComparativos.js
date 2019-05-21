/**
 * Controlador para generar los reportes comparativos de ventas vs compras
 * JULIAN ALVARAN 2019-02-11
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


function LimpiarDivs(){
    document.getElementById("DivProceso").innerHTML="";
    
}

/*
 * Genera el reporte contable balance por terceros
 * @returns {undefined}
 */
function CrearReporteComprasXVentas(){
    document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportes").innerHTML="Obteniendo Clasificación de inventarios";
    var TxtFechaInicial = document.getElementById('FechaInicial').value;
    var TxtFechaFinal = document.getElementById('FechaFinal').value;
    var Nivel = document.getElementById('CmbNivel').value;
    
      
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
        form_data.append('Nivel', Nivel);       
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        
        
        $.ajax({
        url: './procesadores/ReportesComparativos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          
            LimpiarDivs();              
            document.getElementById("DivReportes").innerHTML=data;        
                
             //$('#TblReporte').DataTable();
            //ObtengaCompras(Clasificacion);
         
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

/**
 * Obtiene la informacion de las compras
 * @param {type} Clasificacion
 * @returns {undefined}
 */
function ObtengaCompras(Clasificacion){
    //document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Obteniendo información de las compras";
    var TxtFechaInicial = document.getElementById('FechaInicial').value;
    var TxtFechaFinal = document.getElementById('FechaFinal').value;
    var Nivel = document.getElementById('CmbNivel').value;
    
      
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
        form_data.append('Nivel', Nivel);       
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        
        
        $.ajax({
        url: './procesadores/ReportesComparativos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK") { 
              var Compras = (respuestas[1]);
              console.log(Compras);              
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Infcormación de compras obtenida";
              ObtengaVentas(Clasificacion,Compras);
          }else{
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>"+data;
              LimpiarDivs();
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Obtiene las ventas
 * @param {type} Clasificacion
 * @param {type} Compras
 * @returns {undefined}
 */
function ObtengaVentas(Clasificacion,Compras){
    //document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Obteniendo información de las compras";
    var TxtFechaInicial = document.getElementById('FechaInicial').value;
    var TxtFechaFinal = document.getElementById('FechaFinal').value;
    var Nivel = document.getElementById('CmbNivel').value;
    
      
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
        form_data.append('Accion', 3);
        form_data.append('Nivel', Nivel);       
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        
        
        $.ajax({
        url: './procesadores/ReportesComparativos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK") { 
              var Ventas = (respuestas[1]);
              console.log(Ventas);              
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Infcormación de ventas obtenida";
              ObtengaDatosCompletos(Clasificacion,Compras,Ventas);
          }else{
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>"+data;
              LimpiarDivs();
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}

/**
 * Se organiza la informacion 
 * @param {type} Clasificacion
 * @param {type} Compras
 * @param {type} Ventas
 * @returns {undefined}
 */
function ObtengaDatosCompletos(Clasificacion,Compras,Ventas){
    //document.getElementById("DivProceso").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Organizando la información";
        
    var Nivel = document.getElementById('CmbNivel').value;
    
    
    
        
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('Nivel', Nivel);       
        form_data.append('Clasificacion', Clasificacion);
        form_data.append('Compras', Compras);
        form_data.append('Ventas', Ventas);
        
        $.ajax({
        url: './procesadores/ReportesComparativos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          console.log(data);  
          if (respuestas[0] == "OK") { 
              var JsonCompleto = (respuestas[1]);
              console.log(JsonCompleto);              
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>Información completa obtenida";
              //ObtengaDatosCompletos(Clasificacion,Compras,Ventas);
          }else{
              document.getElementById("DivReportes").innerHTML=document.getElementById("DivReportes").innerHTML+"<br>"+data;
              LimpiarDivs();
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}

