/**
 * Controlador para cartera eps
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

function CargarAdminCarteraEPS(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.carteraeps`,`DivCatCartera`,`DivOpcionesCatCartera`)
}

function HistorialGlosas(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.glosaseps_asmet`,`DivCatHisCartera`,`DivOpcionesCatHisCartera`)
}

function CargarHistorialPagos(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.pagos_asmet`,`DivSRAsmet`,`DivOpcionesSRAsmet`)
}

function HistorialAnticipos(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.anticipos2`,`DivControlCargue`,`DivOpcionesControlCargue`)
}

function HistorialNotas(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.notas_db_cr_2`,`DivHistorialNotas`,`DivOpcionesHistorialNotas`)
}

function HistorialActualizacionesCartera(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.historial_carteracargada_eps`,`DivHistorialActualizacion`,`DivOpcionesActualizacion`)
}

function HistorialArchivosCargados(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.controlcargueseps`,`DivHistorialTab7`,`DivOpcionesTab7`)
}

function HistorialCarteraXEdades(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.carteraxedades`,`DivHistorialTab8`,`DivOpcionesTab8`)
}

function CruceCartera(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.vista_cruce_cartera_asmet`,`DivHistorialTab9`,`DivOpcionesTab9`)
}

function HistorialRetenciones(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.retenciones`,`DivHistorialTab10`,`DivOpcionesTab10`)
}

document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
