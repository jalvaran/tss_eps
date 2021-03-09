/**
 * Controlador para la creacion de los contratos
 * JULIAN ALVARAN 2019-09-25
 * TECHNO SOLUCIONES SAS 
 * 
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

function AbreModal(idModal){
    $("#"+idModal).modal();
}


function MarqueErrorElemento(idElemento){
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}

function AbreFormularioCrearContrato(Contrato){
        
    AbreModal('ModalAcciones');
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);        
        form_data.append('Contrato', Contrato);
        
        $.ajax({
        url: '../../general/Consultas/CreacionContratos.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           $('#CmbContratoPadre').select2();           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function ValidarClasificacionContrato(){
    var Clasificacion = document.getElementById('CmbClasificacionContrato').value;
    
    if( Clasificacion=='ACUERDO' || Clasificacion=='CONTRATO' || Clasificacion=='SIN CONTRATO' || Clasificacion=='COTIZACION' || Clasificacion=='URGENCIAS'){
        OcultaXID('DivSelectoresOtroSI');
    }else{
        MuestraXID('DivSelectoresOtroSI');
    }
    
    /*
    if(Clasificacion=='ACUERDO' || Clasificacion=='OTRO SI' || Clasificacion=='SIN CONTRATO' || Clasificacion=='COTIZACION' || Clasificacion=='URGENCIAS'){
        OcultaXID('DivSelectorTipoContrato');
    }else{
        MuestraXID('DivSelectorTipoContrato');
    }
    */
    if(Clasificacion==''){
        OcultaXID('DivSelectoresOtroSI');
        OcultaXID('DivSelectorTipoContrato');
    }
    
}

function ValidaOpcionesTipoContrato(){
    var TipoContrato = document.getElementById('CmbTipoContrato').value;
        if(TipoContrato == 'CAPITA' || TipoContrato == 'CAPITA MORVILIDAD' || TipoContrato == 'CAPITA PDYDT' || TipoContrato == 'CAPITA ACTIVIDADES MINIMAS' ){
            MuestraXID('DivUPCCapita');
        }else{
            OcultaXID('DivUPCCapita');
        }
    
    
}

function CrearContratoEPS(){
    
    var idBoton="btnCrearContrato";
    
    document.getElementById(idBoton).disabled=true;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var CmbClasificacionContrato=document.getElementById('CmbClasificacionContrato').value;
    var CmbTipoContrato=document.getElementById('CmbTipoContrato').value;
    var TxtUPC=document.getElementById('TxtUPC').value;
    var TxtNumeroAfiliados=document.getElementById('TxtNumeroAfiliados').value;
    var NumeroContrato=document.getElementById('NumeroContrato').value;
    var CmbNumeroOtroSI=document.getElementById('CmbNumeroOtroSI').value;
    var FechaInicial=document.getElementById('FechaInicial').value;
    var FechaFinal=document.getElementById('FechaFinal').value;
    var TxtObjetoContrato=document.getElementById('TxtObjetoContrato').value;
    var TxtNivelComplejidad=document.getElementById('TxtNivelComplejidad').value;
    var FinalidadContrato=document.getElementById('FinalidadContrato').value;
    var DepartamentoCobertura=document.getElementById('DepartamentoCobertura').value;
    var ValorContrato=document.getElementById('ValorContrato').value;
    var CmbTipoPlan=document.getElementById('CmbTipoPlan').value;
    var CmbNivelPrioridad=document.getElementById('CmbNivelPrioridad').value;
    var CmbCobertura=document.getElementById('CmbCobertura').value;
    var ContratoEquivalente=document.getElementById('ContratoEquivalente').value;
    var CmbContratoPadre=document.getElementById('CmbContratoPadre').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
        form_data.append('ContratoEquivalente', ContratoEquivalente);
        form_data.append('CmbClasificacionContrato', CmbClasificacionContrato);
        form_data.append('CmbTipoContrato', CmbTipoContrato);
        form_data.append('TxtUPC', TxtUPC);
        form_data.append('TxtNumeroAfiliados', TxtNumeroAfiliados);
        form_data.append('NumeroContrato', NumeroContrato);
        form_data.append('CmbNumeroOtroSI', CmbNumeroOtroSI);
        form_data.append('FechaInicial', FechaInicial);
        form_data.append('FechaFinal', FechaFinal);
        form_data.append('TxtObjetoContrato', TxtObjetoContrato);
        form_data.append('TxtNivelComplejidad', TxtNivelComplejidad);
        form_data.append('FinalidadContrato', FinalidadContrato);
        form_data.append('DepartamentoCobertura', DepartamentoCobertura);
        form_data.append('ValorContrato', ValorContrato);
        form_data.append('CmbTipoPlan', CmbTipoPlan);
        form_data.append('CmbNivelPrioridad', CmbNivelPrioridad);
        form_data.append('CmbCobertura', CmbCobertura);
        form_data.append('CmbContratoPadre', CmbContratoPadre);
                    
    $.ajax({
        //async:false,
        url: '../../general/procesadores/CrearContratos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               
                alertify.success(respuestas[1]);  
                document.getElementById("DivFrmModalAcciones").innerHTML=respuestas[1];
                        
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function AsociarContratoEquivalente(ContratoEquivalente,idCmbContratoExistente){
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var ContratoExistente=document.getElementById(idCmbContratoExistente).value;
    var form_data = new FormData();
        form_data.append('Accion', 2);        
        form_data.append('ContratoEquivalente', ContratoEquivalente);
        form_data.append('ContratoExistente', ContratoExistente);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: '../../general/procesadores/CrearContratos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           alertify.success(data);
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
           
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function DibujeFrmPercapita(idContrato){
        
    AbreModal('ModalAcciones');
        
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('idContrato', idContrato);   
        
        
        $.ajax({
        url: '../../general/Consultas/CreacionContratos.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           $('#CmbMunicipioPercapita').select2({
		
                placeholder: 'Seleccione un municipio',
                ajax: {
                  url: './../../general/buscadores/municipios.search.php',
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
      });
}

function CrearContratoPercapita(){
    var idBoton="BntModalAcciones";
    document.getElementById(idBoton).disabled=true;
    var idContratoPadre=document.getElementById('idContratoPadre').value;
    var FechaInicialPercapita=document.getElementById('FechaInicialPercapita').value;
    var FechaFinalPercapita=document.getElementById('FechaFinalPercapita').value;
    var CmbMunicipioPercapita=document.getElementById('CmbMunicipioPercapita').value;
    var TxtPorcentajePercapita=document.getElementById('TxtPorcentajePercapita').value;
    var TxtValorPercapita=document.getElementById('TxtValorPercapita').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);        
        form_data.append('idContratoPadre', idContratoPadre);
        form_data.append('FechaInicialPercapita', FechaInicialPercapita);
        form_data.append('FechaFinalPercapita', FechaFinalPercapita);
        form_data.append('CmbMunicipioPercapita', CmbMunicipioPercapita);
        form_data.append('TxtPorcentajePercapita', TxtPorcentajePercapita);
        form_data.append('TxtValorPercapita', TxtValorPercapita);
        
        $.ajax({
        url: '../../general/procesadores/CrearContratos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               
                alertify.success(respuestas[1]);  
                document.getElementById("DivFrmModalAcciones").innerHTML=respuestas[1];
                document.getElementById(idBoton).disabled=false;
                        
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
           document.getElementById(idBoton).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function adjuntos_contrato(contrato_id){
        
    AbreModal('ModalAcciones');
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);        
        form_data.append('contrato_id', contrato_id);
        
        $.ajax({
        url: '../../general/Consultas/CreacionContratos.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           add_events_dropzone_contrato();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}



function add_events_dropzone_contrato(){
    Dropzone.autoDiscover = false;
           
    urlQuery='../../general/procesadores/CrearContratos.process.php';
    var contrato_id=$("#contrato_adjuntos").data("contrato_id");
    
    var myDropzone = new Dropzone("#contrato_adjuntos", { url: urlQuery,paramName: "adjunto_contrato"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 4);
            formData.append("contrato_id", contrato_id);
            
            
        });

        myDropzone.on("addedfile", function(file) {
            file.previewElement.addEventListener("click", function() {
                myDropzone.removeFile(file);
            });
        });

        myDropzone.on("success", function(file, data) {

            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listar_adjuntos_contrato(contrato_id);
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }

        });
    listar_adjuntos_contrato(contrato_id);
}


 
 function listar_adjuntos_contrato(contrato_id=''){
    var idDiv="div_adjuntos_contrato";
     
    var form_data = new FormData();
        form_data.append('Accion', 7);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        
        
        form_data.append('contrato_id', contrato_id);
                        
       $.ajax({// se arma un objecto por medio de ajax  
        url: '../../general/Consultas/CreacionContratos.draw.php',
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function EliminarItemContrato(tabla_id,item_id,contrato_id){    
    
    
         
    var form_data = new FormData();
        
        form_data.append('Accion', 5);
        
        form_data.append('item_id', item_id);  
        form_data.append('tabla_id', tabla_id);
                        
        $.ajax({
        url: '../../general/procesadores/CrearContratos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                if(tabla_id==1){
                    listar_adjuntos_contrato(contrato_id);
                }
                
                
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);
                
                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
            }
               
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  
