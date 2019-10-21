/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$('#CmbIPS').select2();
$('#CmbEPS').select2();

function MostrarHistorial(){
    SeleccioneTablaDB(`contratos`,`DivContratos`,`DivOpcionesContratos`);
}

MostrarHistorial();
document.getElementById('BtnMuestraMenuLateral').click();
