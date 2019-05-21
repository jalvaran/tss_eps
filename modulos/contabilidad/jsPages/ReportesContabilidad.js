/**
 * Controlador para generar los reportes contables
 * JULIAN ALVARAN 2019-01-08
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


/*
 * Genera el reporte contable balance por terceros
 * @returns {undefined}
 */
function GenereBalanceXTerceros(){
    var Reporte = document.getElementById('CmbReporteContable').value;
    var CmbTipo = document.getElementById('CmbTipo').value;
    var TxtFechaInicial = document.getElementById('TxtFechaInicial').value;
    var TxtFechaFinal = document.getElementById('TxtFechaFinal').value;
    var CmbCentroCosto = document.getElementById('CmbCentroCosto').value;
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbOpciones = document.getElementById('CmbOpciones').value;
    var CmbTercero = document.getElementById('CmbTercero').value;
    var TxtCuentaContable = document.getElementById('TxtCuentaContable').value;
    
    if(Reporte==""){
        alertify.alert("Debe seleccionar un Reporte");
        document.getElementById('CmbReporteContable').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbReporteContable').style.backgroundColor="white";
    }
    
        
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('TxtFechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('TxtFechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaFinal').style.backgroundColor="white";
    }
    
    if(CmbCentroCosto==""){
        alertify.alert("Debe seleccionar un Centro de costos");
        document.getElementById('CmbCentroCosto').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbCentroCosto').style.backgroundColor="white";
    }
    
    if(CmbEmpresa==""){
        alertify.alert("Debe seleccionar una  Empresa");
        document.getElementById('CmbEmpresa').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbEmpresa').style.backgroundColor="white";
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('Reporte', Reporte);
        form_data.append('CmbTipo', CmbTipo);
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbOpciones', CmbOpciones);
        form_data.append('CmbTercero', CmbTercero);
        form_data.append('TxtCuentaContable', TxtCuentaContable);
        
        $.ajax({
        url: './Consultas/ReportesContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           

        //SeleccioneTablaDB("vista_balancextercero2","DivReportesContables","DivOpcionesReportes");      
          document.getElementById("DivOpcionesReportes").innerHTML="";
          document.getElementById("DivReportesContables").innerHTML=data;
          //document.getElementById("LinkExport").click();
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  
/**
 * Limpia los divs utilizados para las consutlas enviadas por ajax
 * @returns {undefined}
 */
function limpiarDivs(){
    document.getElementById("DivDibujeOpcionesReporte").innerHTML='';
    document.getElementById("DivOpcionesReportes").innerHTML='';
    document.getElementById("DivReportesContables").innerHTML='';
    document.getElementById("DivPDFReportes").style.display="none";
}
/**
 * Dibuja las diferentes opciones para cada reporte contable
 * @returns {undefined}
 */
function DibujeOpcionesReporte(){
    var Reporte = document.getElementById('CmbReporteContable').value;
    var CmbAnio='';
    
    if ($("#CmbAnio").length>0){
        CmbAnio=document.getElementById('CmbAnio').value;
       
    }    
    limpiarDivs();
    if(Reporte==1){
        var Accion=1;
    }
    
    if(Reporte==2){
        var Accion=3;
    }
    
    if(Reporte==3){//estado de resultados
        var Accion=5;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);
        form_data.append('Reporte', Reporte);
        form_data.append('CmbAnio', CmbAnio);  
        
        $.ajax({
        url: './Consultas/ReportesContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
            document.getElementById("DivDibujeOpcionesReporte").innerHTML=data;
            
            ConviertaSelectsToSelect2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}

/**
 * Convierte los selects que se necesiten en select2 verificando antes si está creado 
 * @returns {undefined}
 */
function ConviertaSelectsToSelect2(){
    if($("#CmbTercero").length > 0) { 
        $('#CmbTercero').select2({		  
            placeholder: 'Selecciona un Tercero',
            ajax: {
              url: 'buscadores/proveedores.search.php',
              dataType: 'json',
              delay: 250,
              processResults: function (data) {

                return {                     
                  results: data
                };
              },
             cache: true
            }
          });
      }      
      
      if($("#CmbCiudadRetencion").length > 0) {
        $('#CmbCiudadRetencion').select2({		  
            placeholder: 'Ciudad donde se practicó la retención',
            ajax: {
              url: 'buscadores/municipios.search.php',
              dataType: 'json',
              delay: 250,
              processResults: function (data) {

                return {                     
                  results: data
                };
              },
             cache: true
            }
          });
      }      
      
      if($("#CmbCiudadPago").length > 0) {
        $('#CmbCiudadPago').select2({		  
            placeholder: 'Ciudad donde se pagó la retención',
            ajax: {
              url: 'buscadores/municipios.search.php',
              dataType: 'json',
              delay: 250,
              processResults: function (data) {

                return {                     
                  results: data
                };
              },
             cache: true
            }
          });
      }      
}
/**
 * Genera la peticion para dibujar el certificado de retenciones
 * @returns {undefined}
 */
function GenereCertificaRetenciones(){    
   
    var TxtFechaInicial = document.getElementById('TxtFechaInicial').value;
    var TxtFechaFinal = document.getElementById('TxtFechaFinal').value;
    var CmbCentroCosto = document.getElementById('CmbCentroCosto').value;
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbTercero = document.getElementById('CmbTercero').value;
    var CmbCiudadRetencion = document.getElementById('CmbCiudadRetencion').value;
    var CmbCiudadPago = document.getElementById('CmbCiudadPago').value;
    
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('TxtFechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('TxtFechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaFinal').style.backgroundColor="white";
    }
    
    if(CmbCentroCosto==""){
        alertify.alert("Debe seleccionar un Centro de costos");
        document.getElementById('CmbCentroCosto').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbCentroCosto').style.backgroundColor="white";
    }
    
    if(CmbEmpresa==""){
        alertify.alert("Debe seleccionar una  Empresa");
        document.getElementById('CmbEmpresa').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbEmpresa').style.backgroundColor="white";
    }
    
    if(CmbTercero==""){
        alertify.alert("Debe seleccionar un tercero");
        document.getElementById('select2-CmbTercero-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbTercero-container').style.backgroundColor="white";
    }
    
    if(CmbCiudadRetencion==""){
        alertify.alert("Debe seleccionar la ciudad donde se practicó la retención");
        document.getElementById('select2-CmbCiudadRetencion-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbCiudadRetencion-container').style.backgroundColor="white";
    }
    
    if(CmbCiudadPago==""){
        alertify.alert("Debe seleccionar la ciudad donde se pagó la rentención");
        document.getElementById('select2-CmbCiudadPago-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbCiudadPago-container').style.backgroundColor="white";
    }
    
    
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('CmbCiudadPago', CmbCiudadPago);
        form_data.append('CmbCiudadRetencion', CmbCiudadRetencion);
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbTercero', CmbTercero);
        
        $.ajax({
        url: './Consultas/ReportesContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            //console.log(data);
            document.getElementById("DivOpcionesReportes").innerHTML="";
            document.getElementById("DivReportesContables").innerHTML=data;
            document.getElementById("LinkPDF").click();
            document.getElementById("DivPDFReportes").style.display="block";
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Genera el estado de resultados de un año determinado
 * @returns {undefined}
 */
function GenereEstadoResultadosAnio(){    
   
    var TxtFechaInicial = document.getElementById('TxtFechaInicial').value;
    var TxtFechaFinal = document.getElementById('TxtFechaFinal').value;
    var CmbCentroCosto = document.getElementById('CmbCentroCosto').value;
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbAnio = document.getElementById('CmbAnio').value;
        
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('TxtFechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('TxtFechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaFinal').style.backgroundColor="white";
    }
    
    if(CmbCentroCosto==""){
        alertify.alert("Debe seleccionar un Centro de costos");
        document.getElementById('CmbCentroCosto').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbCentroCosto').style.backgroundColor="white";
    }
    
    if(CmbEmpresa==""){
        alertify.alert("Debe seleccionar una  Empresa");
        document.getElementById('CmbEmpresa').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbEmpresa').style.backgroundColor="white";
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbAnio', CmbAnio);
        
        $.ajax({
        url: './Consultas/ReportesContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            console.log(data);
            document.getElementById("DivOpcionesReportes").innerHTML="";
            document.getElementById("DivReportesContables").innerHTML=data;
            document.getElementById("LinkPDF").click();
            document.getElementById("DivPDFReportes").style.display="block";
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}


/**
 * Genera el estado de resultados de un año determinado
 * @returns {undefined}
 */
function GenereHTMLEstadoResultadosAnio(){    
    document.getElementById("DivReportesContables").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    var TxtFechaInicial = document.getElementById('TxtFechaInicial').value;
    var TxtFechaFinal = document.getElementById('TxtFechaFinal').value;
    var CmbCentroCosto = document.getElementById('CmbCentroCosto').value;
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbAnio = document.getElementById('CmbAnio').value;
        
    if(TxtFechaInicial==""){
        alertify.alert("Debe seleccionar una fecha inicial");
        document.getElementById('TxtFechaInicial').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaInicial').style.backgroundColor="white";
    }
    
    if(TxtFechaFinal==""){
        alertify.alert("Debe seleccionar una fecha final");
        document.getElementById('TxtFechaFinal').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFechaFinal').style.backgroundColor="white";
    }
    
    if(CmbCentroCosto==""){
        alertify.alert("Debe seleccionar un Centro de costos");
        document.getElementById('CmbCentroCosto').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbCentroCosto').style.backgroundColor="white";
    }
    
    if(CmbEmpresa==""){
        alertify.alert("Debe seleccionar una  Empresa");
        document.getElementById('CmbEmpresa').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CmbEmpresa').style.backgroundColor="white";
    }
    
    
    var form_data = new FormData();
        form_data.append('idDocumento', 2);
        
        form_data.append('TxtFechaInicial', TxtFechaInicial);
        form_data.append('TxtFechaFinal', TxtFechaFinal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbAnio', CmbAnio);
          
        $.ajax({
        url: './Consultas/PDF_ReportesContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            console.log(data);
            document.getElementById("DivOpcionesReportes").innerHTML="";
            document.getElementById("DivReportesContables").innerHTML=data;
            document.getElementById("DivPDFReportes").style.display="none";
          
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
