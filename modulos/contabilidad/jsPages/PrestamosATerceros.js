/**
 * Controlador para Prestamos a Terceros
 * JULIAN ALVARAN 2019-04-06
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */
/**
 * Cierra una ventana modal
 * @param {type} idModal
 * @returns {undefined}
 */
function CierraModal(idModal) {
    $("#"+idModal).modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}

/**
 * Dibuja el formulario para realizar un prestamo
 * @returns {undefined}
 */
function FormularioPrestar(){
    $("#ModalAcciones").modal();
    var form_data = new FormData();
        form_data.append('Accion', 1);                
        $.ajax({
        url: './Consultas/PrestamosATerceros.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
            document.getElementById("DivFormulariosModal").innerHTML=data           
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
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Elije una accion a ejecutar de acuerdo a un formulario
 * @returns {undefined}
 */
function GuardarAccion(){
    var Accion = document.getElementById("idFormulario").value;
    
    if(Accion==1){
        CrearPrestamo();
    }
    
    if(Accion==2){
        AbonarPrestamo();
    }
}
/**
 * Crea un prestamo
 * @returns {undefined}
 */
function CrearPrestamo(){
    document.getElementById('BtnAccionesModal').disabled=true;
            
    var TxtFecha = document.getElementById("TxtFecha").value;
    var CmbTercero = document.getElementById("CmbTercero").value;
    var CmbCuentaDestino = document.getElementById("CmbCuentaDestino").value;
    var CmbCuentaOrigen = document.getElementById("CmbCuentaOrigen").value;
    var CmbEmpresa = document.getElementById("CmbEmpresa").value;
    var CmbSucursal = document.getElementById("CmbSucursal").value;
    var CmbCentroCosto = document.getElementById("CmbCentroCosto").value;
    var TxtObservaciones = document.getElementById("TxtObservaciones").value;
    
    var TxtValor = parseFloat(document.getElementById("TxtValor").value);
        
    if(TxtFecha==''){
        alertify.error("El campo Fecha no puede estar vacío");
        document.getElementById("TxtFecha").style.backgroundColor="pink";
        document.getElementById('BtnAccionesModal').disabled=false;
        return;
    }else{
        document.getElementById("TxtFecha").style.backgroundColor="white";
    }
    
    if(CmbTercero==''){
        alertify.error("Debe Seleccionar un tercero");
        document.getElementById("select2-CmbTercero-container").style.backgroundColor="pink";
        document.getElementById('BtnAccionesModal').disabled=false;
        return;
    }else{
        document.getElementById("select2-CmbTercero-container").style.backgroundColor="white";
    }
    
    if(isNaN(TxtValor) || TxtValor<=0){
        alertify.error("El campo Valor debe ser un número mayor a cero");
        document.getElementById('TxtValor').style.backgroundColor="pink";
        document.getElementById('BtnAccionesModal').disabled=false;
        return;
    }else{
        document.getElementById('TxtValor').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', '1'); 
        form_data.append('TxtFecha', TxtFecha);
        form_data.append('CmbTercero', CmbTercero);
        form_data.append('CmbCuentaOrigen', CmbCuentaOrigen);
        form_data.append('CmbCuentaDestino', CmbCuentaDestino);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbSucursal', CmbSucursal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
        form_data.append('TxtObservaciones', TxtObservaciones);
        form_data.append('TxtValor', TxtValor);
        
        $.ajax({
        url: './procesadores/PrestamosATerceros.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];
                alertify.success(mensaje);           
            }else{
                alertify.alert(data);
                
            }
            CierraModal("ModalAcciones");
            MuestreCuentasXCobrar();
            document.getElementById('BtnAccionesModal').disabled=false;
            
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Abona a un prestamo
 * @returns {undefined}
 */
function AbonarPrestamo(){
    document.getElementById('BtnAccionesModal').disabled=true;
    var idPrestamo = document.getElementById("idPrestamo").value;   
    var TxtFecha = document.getElementById("TxtFecha").value;
    var CmbCuentaDestino = document.getElementById("CmbCuentaDestino").value;
    
    var TxtValor = parseFloat(document.getElementById("TxtValor").value);
        
    if(TxtFecha==''){
        alertify.error("El campo Fecha no puede estar vacío");
        document.getElementById("TxtFecha").style.backgroundColor="pink";
        document.getElementById('BtnAccionesModal').disabled=false;
        return;
    }else{
        document.getElementById("TxtFecha").style.backgroundColor="white";
    }
    
        
    if(isNaN(TxtValor) || TxtValor<=0){
        alertify.error("El campo Valor debe ser un número mayor a cero");
        document.getElementById('TxtValor').style.backgroundColor="pink";
        document.getElementById('BtnAccionesModal').disabled=false;
        return;
    }else{
        document.getElementById('TxtValor').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', '2'); 
        form_data.append('TxtFecha', TxtFecha);
        form_data.append('idPrestamo', idPrestamo);
        form_data.append('CmbCuentaDestino', CmbCuentaDestino);
        form_data.append('TxtValor', TxtValor);
        
        $.ajax({
        url: './procesadores/PrestamosATerceros.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];
                alertify.alert(mensaje);           
            }else{
                alertify.alert(data);
                
            }
            CierraModal("ModalAcciones");
            MuestreCuentasXCobrar();
            document.getElementById('BtnAccionesModal').disabled=false;
            
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Muestra las cuentas x cobrar
 * @returns {undefined}
 */
function MuestreCuentasXCobrar(){
    SeleccioneTablaDB('prestamos_terceros','DivListadoCuentaXCobrar','DivOpcionesListadoCuentaXCobrar');
    
}
/**
 * Refreca los datos de las cuentas x cobrar
 * @returns {undefined}
 */
function RefrescarCuentasXCobrar(){
    DibujeTablaDB('prestamos_terceros','DivListadoCuentaXCobrar');
}

/**
 * Abre el modal y muestra el formulario para abonar
 * @param {type} idPrestamo
 * @returns {undefined}
 */
function AbreModalAbonar (idPrestamo){
    $("#ModalAcciones").modal();
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('idPrestamo', idPrestamo);
        $.ajax({
        url: './Consultas/PrestamosATerceros.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
            document.getElementById("DivFormulariosModal").innerHTML=data           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  
/**
 * Abre el modal con el historial de abonos un Prestamo
 * @param {type} idPrestamo
 * @returns {undefined}
 */
function HistorialAbonos(idPrestamo){
    $("#ModalAcciones").modal();
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idPrestamo', idPrestamo);
        $.ajax({
        url: './Consultas/PrestamosATerceros.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
            document.getElementById("DivFormulariosModal").innerHTML=data           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  


MuestreCuentasXCobrar()