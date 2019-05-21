/**
 * Controlador para Documentos contables
 * JULIAN ALVARAN 2019-04-10
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

function MuestraXID(id){
    
    
    document.getElementById(id).style.display="block";
    
    
}


function OcultaXID(id){
    
    
    document.getElementById(id).style.display="none";
    
    
}

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
 * Crea el formulario para guardar o editar un documento
 * @param {type} Proceso
 * @returns {undefined}
 */
function AbrirModalNuevoDocumento(Proceso="Nuevo"){
    $("#ModalAcciones").modal();
    var idDocumento=document.getElementById('idDocumento').value;
    
    var form_data = new FormData();
        if(Proceso=="Nuevo"){
            var Accion=1;
        }
        if(Proceso=="Editar"){
            var Accion=2;
            
        }
        form_data.append('Accion', Accion);
        form_data.append('idDocumento', idDocumento);
        $.ajax({
        url: './Consultas/DocumentosContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivFrmModalAcciones').innerHTML=data;
            
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
function SeleccioneAccionFormularios(){
    var Accion = document.getElementById("idFormulario").value;
    
    if(Accion==1 || Accion==2){
        CrearEditarDocumento(Accion);
    }
    
    if(Accion==100){
        CrearTercero('ModalAcciones','BntModalAcciones');
    }
}
/**
 * Crear o editar una accion
 * @param {type} idAccion->1 para Crear 2 para Editar
 * @returns {undefined}
 */
function CrearEditarDocumento(Accion){ 
    
    var idDocumentoActivo=document.getElementById('idDocumento').value;
    var Fecha = document.getElementById('TxtFecha').value;
    var TipoDocumento = document.getElementById('CmbTipoDocumento').value;
    var Observaciones = document.getElementById('TxtObservaciones').value;
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbSucursal = document.getElementById('CmbSucursal').value;
    var CmbCentroCosto = document.getElementById('CmbCentroCosto').value;
    
    
    if(Observaciones==""){
        alertify.alert("El campo Observaciones no puede estar vacío");
        document.getElementById('TxtObservaciones').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtObservaciones').style.backgroundColor="white";
    }
    
    if(Fecha==""){
        alertify.alert("Debe seleccionar una fecha");
        document.getElementById('TxtFecha').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFecha').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);        
        form_data.append('TxtFecha', Fecha);
        form_data.append('CmbTipoDocumento', TipoDocumento);
        form_data.append('idDocumentoActivo', idDocumentoActivo);
        form_data.append('TxtObservaciones', Observaciones);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbSucursal', CmbSucursal);
        form_data.append('CmbCentroCosto', CmbCentroCosto);
            
        document.getElementById('TxtObservaciones').value='';
        
    
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK"){ 
              if(Accion==1){
                var idDocumento=respuestas[1];
                var TextoComboDocumento=respuestas[2];
                alertify.success("Documento "+idDocumento+" creado");
                var x = document.getElementById("idDocumento");
                  var option = document.createElement("option");
                  option.text = TextoComboDocumento;
                  option.value = idDocumento;

                  x.add(option); 
                  $("#idDocumento option:last").attr('selected','selected');
                  DibujeDocumento();
              }  
              if(Accion==2){
                  var index = document.getElementById("idDocumento").selectedIndex;
                  var TextoOpcion=respuestas[2];
                  document.getElementById("idDocumento").options[index].text=TextoOpcion;
                  alertify.success(respuestas[1]);
              }
              CierraModal('ModalAcciones');
          }else{
              alertify.alert("Error: "+data);
              
          }
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  

}

/**
 * Agrega un movimiento a un documento contablee
 * @returns {undefined}
 */
function AgregarItem(){ 
       
    var idDocumento=document.getElementById('idDocumento').value;
    var CuentaPUC = document.getElementById('CuentaPUC').value;
    var Tercero = document.getElementById('Tercero').value;
    var TxtConcepto = document.getElementById('TxtConcepto').value;
    var TipoMovimiento = document.getElementById('TipoMovimiento').value;
    var Valor = parseFloat(document.getElementById('Valor').value);
    var Base = parseFloat(document.getElementById('Base').value);
    var Porcentaje = parseFloat(document.getElementById('Porcentaje').value);
    document.getElementById("BtnAgregarItem").disabled=true;
    document.getElementById("BtnAgregarItem").value="Agregando";
    
    if(idDocumento==""){
        alertify.alert("Debe seleccionar un Documento");
        document.getElementById('idDocumento').style.backgroundColor="pink";
        document.getElementById("BtnAgregarItem").disabled=false;
        document.getElementById("BtnAgregarItem").value="Agregar";   
        return;
    }else{
        document.getElementById('idDocumento').style.backgroundColor="white";
    }
    
    
    if(CuentaPUC==""){
        alertify.alert("Debe seleccionar una Cuenta Contable");
        document.getElementById('select2-CuentaPUC-container').style.backgroundColor="pink";
        document.getElementById("BtnAgregarItem").disabled=false;
        document.getElementById("BtnAgregarItem").value="Agregar";   
        return;
    }else{
        document.getElementById('select2-CuentaPUC-container').style.backgroundColor="white";
    }
    
    if(Tercero==""){
        alertify.alert("Debe seleccionar una Tercero");
        document.getElementById('select2-Tercero-container').style.backgroundColor="pink";
        document.getElementById("BtnAgregarItem").disabled=false;
        document.getElementById("BtnAgregarItem").value="Agregar";   
        return;
    }else{
        document.getElementById('select2-Tercero-container').style.backgroundColor="white";
    }
    
    if(TxtConcepto==""){
        alertify.alert("El campo Concepto no puede estar vacío");
        document.getElementById('TxtConcepto').style.backgroundColor="pink";
        document.getElementById("BtnAgregarItem").disabled=false;
        document.getElementById("BtnAgregarItem").value="Agregar";   
        return;
    }else{
        document.getElementById('TxtConcepto').style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Valor) ||  Valor<=0){
        
        alertify.error("El Valor debe ser un número mayor a cero");
        document.getElementById("Valor").style.backgroundColor="pink";   
        document.getElementById("BtnAgregarItem").disabled=false;
        document.getElementById("BtnAgregarItem").value="Agregar";   
         
        return;
    }else{
        document.getElementById("Valor").style.backgroundColor="white";
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', 3);        
        form_data.append('idDocumento', idDocumento);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Tercero', Tercero );
        form_data.append('TxtConcepto', TxtConcepto);
        form_data.append('TipoMovimiento', TipoMovimiento );
        form_data.append('Valor', Valor );
        form_data.append('Base', Base );
        form_data.append('Porcentaje', Porcentaje );
        
         
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK"){ 
                //OcultaXID('DivBases');
                document.getElementById("Base").value=0;
                document.getElementById("Porcentaje").value=0;
                var idDocumento=respuestas[1];                
                alertify.success(respuestas[1]);                
                document.getElementById('select2-CuentaPUC-container').innerHTML="Seleccione una Cuenta";
                document.getElementById('CuentaPUC').value="";
          }else{
              alertify.alert("Error: "+data);
              
          }
          document.getElementById("BtnAgregarItem").disabled=false;
          document.getElementById("BtnAgregarItem").value="Agregar";
          DibujeDocumento();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
            document.getElementById("BtnAgregarItem").disabled=false;
            document.getElementById("BtnAgregarItem").value="Agregar";  
          }
      })  

}
/**
 * Dibuja todos los elementos de un documento contable
 * @param {type} idDocumento
 * @returns {undefined}
 */
function DibujeDocumento(idDocumento=''){
    
    if(document.getElementById('idDocumento').value==""){
        document.getElementById('BtnEditar').disabled=true;
    }else{
        document.getElementById('BtnEditar').disabled=false;
    }
    if(idDocumento==""){
        var idDocumento = document.getElementById('idDocumento').value;
        
    }
    
    DibujeItems(idDocumento);
    DibujeTotales(idDocumento);
    
}
/**
 * Dibuja los movimientos de un documento contable
 * @param {type} idDocumento
 * @returns {undefined}
 */
function DibujeItems(idDocumento){
    var form_data = new FormData();
        
        form_data.append('Accion', 3);
        form_data.append('idDocumento', idDocumento);
        $.ajax({
        url: './Consultas/DocumentosContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivItems').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}
/**
 * Dibuja los totales del documento
 * @param {type} idDocumento
 * @returns {undefined}
 */
function DibujeTotales(idDocumento){
    var form_data = new FormData();
        
        form_data.append('Accion', 4);
        form_data.append('idDocumento', idDocumento);
        $.ajax({
        url: './Consultas/DocumentosContables.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivTotales').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}
/**
 * 
 * @param {type} idTabla
 * @param {type} idItem
 * @returns {undefined}
 */
function EliminarItem(Tabla,idItem){
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('Tabla', Tabla);
        form_data.append('idItem', idItem);
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.error(data);
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Guarda un documento contable
 * @param {type} idDocumento
 * @returns {undefined}
 */
function GuardarDocumento(idDocumento=''){
    document.getElementById('BtnGuardar').disabled=true;
    document.getElementById('BtnGuardar').value="Guardando...";
    if(idDocumento==''){
        var idDocumento = document.getElementById('idDocumento').value;
    }
        
    
    var form_data = new FormData();
        form_data.append('Accion', '5'); 
        form_data.append('idDocumento', idDocumento);
        
        
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
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
                LimpiarDivs();
                var x = document.getElementById("idDocumento");
                x.remove(x.selectedIndex);
                document.getElementById('BtnEditar').disabled=true;
                alertify.alert(mensaje);
                
            }else{
                alertify.alert("Error: <br>"+data);
                document.getElementById('BtnGuardar').disabled=false;
                document.getElementById('BtnGuardar').value="Guardar";
            }
            
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Copia los movimientos de un documento a otro
 * @returns {undefined}
 */
function CopiarDocumento(){
    var idDocumento = document.getElementById('idDocumento').value;
    var idDocumentoACopiar = document.getElementById('idDocumentoAcciones').value;
    var TipoDocumento = document.getElementById('CmbTipoDocumentoAcciones').value;
    if(idDocumento==''){
        
        alertify.error("Debe Seleccionar un Documento");
        document.getElementById("idDocumento").style.backgroundColor="pink";   
        
        return;
    }else{
        document.getElementById("idDocumento").style.backgroundColor="white";
    }
    
    if(!$.isNumeric(idDocumentoACopiar) ||  idDocumentoACopiar<=0){
        
        alertify.error("Valor incorrecto");
        document.getElementById("idDocumentoAcciones").style.backgroundColor="pink";   
        
        return;
    }else{
        document.getElementById("idDocumentoAcciones").style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('idDocumento', idDocumento);
        form_data.append('idDocumentoACopiar', idDocumentoACopiar);
        form_data.append('TipoDocumento', TipoDocumento);
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Documento Copiado");
            }else{
                alertify.alert(data);
            }
            
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

/**
 * Limpia los divs donde se dibujan los movimientos
 * @returns {undefined}
 */
function LimpiarDivs(){
    document.getElementById('DivItems').innerHTML='';
    document.getElementById('DivTotales').innerHTML='';
}

function EditeDebitoCredito(TipoMovimiento,idItem){
    var idItemCaja = "TxtValorItems_"+idItem;
    var Valor = document.getElementById(idItemCaja).value;
    
    if(Valor==''){
        
        alertify.error("El valor no puede estar vacío");
        document.getElementById(idItemCaja).style.backgroundColor="pink"; 
        
        return;
    }else{
        document.getElementById(idItemCaja).style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Valor) ||  Valor<=0){
        
        alertify.error("El Valor debe se un número mayor a Cero");
        document.getElementById(idItemCaja).style.backgroundColor="pink";   
        
        return;
    }else{
        document.getElementById(idItemCaja).style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('idItem', idItem);
        form_data.append('Valor', Valor);
        form_data.append('TipoMovimiento', TipoMovimiento);
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Valor Editado");
            }else{
                alertify.alert(data);
            }
            
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function EditarCuentaPUC(idItem,idSelect){
    
    var CuentaPUC = document.getElementById(idSelect).value;
    
    var form_data = new FormData();
        form_data.append('Accion', 8);
        form_data.append('idItem', idItem);
        form_data.append('CuentaPUC', CuentaPUC);
        
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Cuenta Editada");
            }else{
                alertify.alert(data);
            }
            
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function EditeBase(TipoCaja,idItem){
    if(TipoCaja=="Base"){
        var Accion=11;
        var idCaja="TxtBaseItems_"+idItem;
        var Valor = document.getElementById(idCaja).value;
    }else if(TipoCaja=="Porcentaje"){
        var Accion=12;
        var idCaja="TxtPorcentajeItems_"+idItem;
        var Valor = document.getElementById(idCaja).value;
    }else{
        return;
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);
        form_data.append('idItem', idItem);
        form_data.append('Valor', Valor);
        
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Valores editados");
            }else{
                alertify.alert(data);
            }
            
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function EditarTercero(idItem,idSelect){
    
    var Tercero = document.getElementById(idSelect).value;
    
    var form_data = new FormData();
        form_data.append('Accion', 9);
        form_data.append('idItem', idItem);
        form_data.append('Tercero', Tercero);
        
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Tercero Editado");
            }else{
                alertify.alert(data);
            }
            
            DibujeDocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function ConviertaSelectItems(idItem){
    var idSelect="CmbCuentaPUCItems_"+idItem;
    
    $('#'+idSelect).select2({
            
            placeholder: 'Selecciona una cuenta PUC para este servicio',
            ajax: {
              url: 'buscadores/CuentaPUC.search.php',
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
    
    $('#'+idSelect).bind('change', function() {
        EditarCuentaPUC(idItem,idSelect);
      });
    /*
    document.getElementById(idSelect).addEventListener("onchange", function(idItem,idSelect){
        console.log(idSelect)
        EditarCuentaPUC(idItem,idSelect);
    });  
    */
}

function ConviertaSelectTerceroItems(idItem){
    var idSelect="CmbTerceroItems_"+idItem;
    
    $('#'+idSelect).select2({
            
            placeholder: 'Selecciona una Tercero',
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
    
    $('#'+idSelect).bind('change', function() {
        EditarTercero(idItem,idSelect);
      });
    /*
    document.getElementById(idSelect).addEventListener("onchange", function(idItem,idSelect){
        console.log(idSelect)
        EditarCuentaPUC(idItem,idSelect);
    });  
    */
}

function VerifiqueSolicitaBase(){
    
    var CuentaPUC = document.getElementById('CuentaPUC').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 10);
        form_data.append('CuentaPUC', CuentaPUC);
                
        $.ajax({
        url: './procesadores/DocumentosContables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            console.log(data)
            if(data=='1'){
                MuestraXID('DivBases');
                document.getElementById("Valor").disabled=true;
                document.getElementById("Valor").value=0;
                document.getElementById("TxtSolicitaBase").value=1;
            }else{
                OcultaXID('DivBases');
                document.getElementById("Valor").disabled=false;
                document.getElementById("Valor").value=0;
                document.getElementById("TxtSolicitaBase").value=0;
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CalculeBase(){
    
    var base = document.getElementById("Base").value;
    var porcentaje = document.getElementById("Porcentaje").value;
    
    if(porcentaje==0 || porcentaje==0 || porcentaje>100){
        var multiplo=1;
        document.getElementById("Porcentaje").value=100
    }else{
        var multiplo=porcentaje/100;
    }
    document.getElementById("Valor").value=base*multiplo;
}

/**
 * Inicializa el modulo
 * @returns {undefined}
 */
function initModule(){
    document.getElementById("BtnMuestraMenuLateral").click();
    
    $('#Tercero').select2({
		  
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
      
      $('#CuentaPUC').select2({
		  
        placeholder: 'Selecciona un Cuenta',
        ajax: {
          url: 'buscadores/CuentaPUC.search.php',
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
      
      $('#CuentaPUC').bind('change', function() {
        VerifiqueSolicitaBase();
      });
}

initModule();
