
function DibujaTotalesCruce(CmbIPS){
    postMessage("Entra a dibujar el total")
    var form_data = new FormData();
        form_data.append('Accion', 33);
        form_data.append('CmbIPS', CmbIPS);   
        //form_data.append('CmbEPS', CmbEPS);        
        
        httpEdicion = new XMLHttpRequest();

        httpEdicion.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data=this.responseText;
                postMessage(data);                     
            }else{
                postMessage(this.responseText);                     
            }
        };    
        
       httpEdicion.open("POST",'../Consultas/validaciones.draw.php',true);
       httpEdicion.send(form_data);
}

onmessage = function (oEvent) {
  DibujaTotalesCruce(oEvent.data);
};
