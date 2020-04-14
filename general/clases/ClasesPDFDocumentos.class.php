<?php
/* 
 * Clase donde se realizaran la generacion de informes.
 * Julian Alvaran
 * Techno Soluciones SAS
 */
include_once 'numeros_letras.class.php';

class Documento{
    /**
     * Constructor 
     * @param type $db
     */
    function __construct($db){
        $this->UltimoNumeral="";
        $this->DataBase=$db;
        $this->obCon=new conexion(1);
        
    }
    
    /**
     * Inicia la creacion de un pdf
     * @param type $TituloFormato
     * @param type $FontSize
     * @param type $VectorPDF
     * @param type $Margenes
     */
    public function PDF_Ini($TituloFormato,$FontSize,$VectorPDF,$Margenes=1,$Patch="../../") {
        
        //require_once('../../librerias/tcpdf/examples/config/tcpdf_config_alt.php');
        $tcpdf_include_dirs = array(realpath($Patch.'librerias/tcpdf/tcpdf.php'), '/usr/share/php/tcpdf/tcpdf.php', '/usr/share/tcpdf/tcpdf.php', '/usr/share/php-tcpdf/tcpdf.php', '/var/www/tcpdf/tcpdf.php', '/var/www/html/tcpdf/tcpdf.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf.php');
        foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
                if (@file_exists($tcpdf_include_path)) {
                        require_once($tcpdf_include_path);
                        break;
                }
        }
        // create new PDF document
        $this->PDF = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF8', false);
        // set document information
        $this->PDF->SetCreator(PDF_CREATOR);
        $this->PDF->SetAuthor('Techno Soluciones');
        $this->PDF->SetTitle($TituloFormato);
        $this->PDF->SetSubject($TituloFormato);
        $this->PDF->SetKeywords('Techno Soluciones, PDF, '.$TituloFormato.' , CCTV, Alarmas, Computadores, Software');
        // set default header data
        
        // set header and footer fonts
        //$this->PDF->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        //$this->PDF->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        //$this->PDF->setFooterData(array(0,64,0), array(0,64,128));
        $this->PDF->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->PDF->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $this->PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        if($Margenes==1){
            $this->PDF->SetMargins(25, 5, 25);
            $this->PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->PDF->SetFooterMargin(10);
        }
        if($Margenes==2){
            $this->PDF->SetMargins(10, 10, PDF_MARGIN_RIGHT);
            $this->PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->PDF->SetFooterMargin(10);
        }
        
        // set auto page breaks
        $this->PDF->SetAutoPageBreak(TRUE, 10);
        // set image scale factor
        $this->PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
                require_once(dirname(__FILE__).'/lang/spa.php');
                $this->PDF->setLanguageArray(1);
        }
        
        // ---------------------------------------------------------
        // set font
        //$pdf->SetFont('helvetica', 'B', 6);
        // add a page
        $this->PDF->AddPage();
        $this->PDF->SetFont('helvetica', '', 6);
        
    }
    
    public function PDF_IniActaLiquidacion($TituloFormato,$TextHeader,$TextFooter,$Margenes=1,$Patch="../../") {
        
        //require_once('../../librerias/tcpdf/examples/config/tcpdf_config_alt.php');
        $tcpdf_include_dirs = array(realpath($Patch.'librerias/tcpdf/tcpdf.php'), '/usr/share/php/tcpdf/tcpdf.php', '/usr/share/tcpdf/tcpdf.php', '/usr/share/php-tcpdf/tcpdf.php', '/var/www/tcpdf/tcpdf.php', '/var/www/html/tcpdf/tcpdf.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf.php');
        foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
                if (@file_exists($tcpdf_include_path)) {
                        require_once($tcpdf_include_path);
                        break;
                }
        }
        // create new PDF document
        $this->PDF = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF8', false);
        // set document information
        $this->PDF->SetCreator(PDF_CREATOR);
        $this->PDF->SetAuthor('Techno Soluciones');
        $this->PDF->SetTitle($TituloFormato);
        $this->PDF->SetSubject($TituloFormato);
        $this->PDF->SetKeywords('Techno Soluciones, PDF, '.$TituloFormato.' , CCTV, Alarmas, Computadores, Software');
        // set default header data
        
        // set header and footer fonts
        //$this->PDF->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $this->PDF->SetHeaderData(PDF_HEADER_LOGO, 50, "", "$TextHeader", array(0,0,0), array(0,0,0));
        $this->PDF->setFooterData(array(0,0,0), array(0,0,0));
        $this->PDF->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 5));
        $this->PDF->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 5));
        // set default monospaced font
        $this->PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set margins
        if($Margenes==1){
            $this->PDF->SetMargins(25, 25, 25);
            $this->PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->PDF->SetFooterMargin(10);
        }
        
        // set auto page breaks
        $this->PDF->SetAutoPageBreak(TRUE, 10);
        // set image scale factor
        $this->PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
                require_once(dirname(__FILE__).'/lang/spa.php');
                $this->PDF->setLanguageArray(1);
        }
        
        // ---------------------------------------------------------
        // set font
        //$pdf->SetFont('helvetica', 'B', 6);
        // add a page
        $this->PDF->AddPage();
        $this->PDF->SetFont('helvetica', '', 7);
        
    }
    /**
     * Encabezado del PDF
     * @param type $Fecha
     * @param type $idEmpresa
     * @param type $idFormatoCalidad
     * @param type $VectorEncabezado
     * @param type $NumeracionDocumento
     */
    public function PDF_Encabezado($Fecha,$idEmpresa,$idFormatoCalidad,$VectorEncabezado,$NumeracionDocumento="",$Patch='../../') {
        $DatosEmpresaPro=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro", $idEmpresa);
        $DatosFormatoCalidad=$this->obCon->DevuelveValores("formatos_calidad", "ID", $idFormatoCalidad);
        
        $RutaLogo=$Patch."$DatosEmpresaPro[RutaImagen]";
///////////////////////////////////////////////////////
//////////////encabezado//////////////////
////////////////////////////////////////////////////////
//////
//////

$tbl = ' 
<table cellspacing="0" cellpadding="1" border="1">
    <tr border="1">
        <td rowspan="3" border="1" style="text-align: center;"><img src="'.$RutaLogo.'" style="width:140px;height:60px;"></td>
        
        <td rowspan="1" colspan="2" width="330px" style="text-align: center; vertical-align: center;"><h2><br>'.utf8_encode($DatosFormatoCalidad["Proceso"]).'</h2></td>
        <td rowspan="2" style="font-size:12px;text-align: center; vertical-align: center;">Fecha de Creación<BR> '.$DatosFormatoCalidad["Fecha"].'</td> 
        
    </tr>
    <tr>
        
    	<td rowspan="1" colspan="2" width="330px" style="text-align: center; vertical-align: center;"><h2><br>'.utf8_encode($DatosFormatoCalidad["Nombre"]).'</h2></td>
        
        
    </tr>
    <tr>
        
       <td style="text-align: center;font-size:12px;" >Código: '.$DatosFormatoCalidad["Codigo"].'</td>
       <td style="text-align: center;font-size:12px;" >Versión: '.$DatosFormatoCalidad["Version"].'</td>
       
    </tr>
</table>
';
$this->PDF->writeHTML($tbl, true, false, false, false, '');
$this->PDF->SetFillColor(255, 255, 255);
$txt="<h3>".$DatosEmpresaPro["RazonSocial"]."<br>NIT ".$DatosEmpresaPro["NIT"]."</h3>";
$this->PDF->MultiCell(62, 5, $txt, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');
$txt=$DatosEmpresaPro["Direccion"]."<br>".$DatosEmpresaPro["Telefono"]."<br>".$DatosEmpresaPro["Ciudad"]."<br>".$DatosEmpresaPro["WEB"];
$this->PDF->MultiCell(62, 5, $txt, 0, 'C', 1, 0, '', '', true,0, true, true, 10, 'M');
$Documento="<strong>$NumeracionDocumento</strong><br><br>";
$this->PDF->MultiCell(62, 5, $Documento, 0, 'R', 1, 0, '', '', true,0, true ,true, 10, 'M');
$this->PDF->writeHTML("<br>", true, false, false, false, '');
//Close and output PDF document
    }
//Crear el documento PDF
    public function PDF_Write($html) {
        $this->PDF->writeHTML($html, true, false, false, false, '');
    } 
//Agregar pagina en PDF
    public function PDF_Add() {
        $this->PDF->AddPage();
    }     
//Crear el documento PDF
    public function PDF_Output($NombreArchivo) {
        $this->PDF->Output("$NombreArchivo".".pdf", 'I');
    } 
    
    
    public function ActaConciliacionPDF($idActaConciliacion) {
        
        $DatosActa=$this->obCon->DevuelveValores("actas_conciliaciones","ID",$idActaConciliacion);
                
        $this->PDF_Ini("Acta de Concilacion de Cartera", 7, "");
        $html= $this->EncabezadoActaConciliacion($idActaConciliacion);
        $this->PDF->writeHTML($html, true, false, false, false, '');
        
        $html= $this->DatosGeneralesActaConciliacion($DatosActa);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->DiferenciasActaConciliacion($DatosActa);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->ResultadosCompromisosActa($idActaConciliacion);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html=$this->FirmasActaConciliacion($DatosActa);
        $this->PDF->writeHTML("<hr>".$html."", true, false, false, false, '');
        /*
        $html= $this->HTML_Movimiento_Contable("CompEgreso",$idEgreso,"");
        $this->PDF_Write("<br><br><br><br><br><br><br><br><br>".$html);
        $html= $this->HTML_Movimiento_Firmas_Egresos($Valor);
        $this->PDF_Write("<br><br>".$html);
         * 
         */
        $this->PDF_Output("Acta_Conciliacion_$idActaConciliacion");
    }
    
    public function EncabezadoActaConciliacion($idActaConciliacion) {
        $RutaLogoASMET="../../LogosEmpresas/logoAsmet.png";
        $RutaLogoAGS="../../LogosEmpresas/logoAGS.png";
        $html =' 
                <table cellspacing="0" cellpadding="1" border="0">
                    <tr border="0">
                        <td border="0" style="text-align: center;"><img src="'.$RutaLogoASMET.'" style="width:200px;height:60px;"></td>
                        <td width="250px" style="text-align: center; vertical-align: center;"><h3><br>ACTA DE CONCILIACION DE CARTERA No. '.$idActaConciliacion.'</h3></td>
                        <td border="0" style="text-align: center;"><img src="'.$RutaLogoAGS.'" style="width:80px;height:60px;"></td>
                    </tr>
                    
                </table>
                ';
        return($html);
    }
    
    public function DatosGeneralesActaConciliacion($DatosActa) {
        $html='<table cellspacing="2" cellpadding="2" border="0" >';
            
            $html.='<tr border="0" >';
                $html.='<td border="0" width="200px">';
                    $html.='<h3>Proveedor:</h3>';
                $html.='</td>'; 
                
                $html.='<td  border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["RazonSocialIPS"]."</h3>";
                $html.='</td>'; 
                
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td  border="0">';
                    $html.='<h3>Representante IPS:</h3>';
                $html.='</td>';  
                $html.='<td  border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["RepresentanteLegal"]."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td  border="0">';
                    $html.='<h3>NIT:</h3>';
                $html.='</td>';  
                $html.='<td border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["NIT_IPS"]."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td border="0" >';
                    $html.='<h3>Departamento:</h3>';
                $html.='</td>';  
                $html.='<td border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["Departamento"]."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td border="0">';
                    $html.='<h3>Encargado Asmet Salud:</h3>';
                $html.='</td>';  
                $html.='<td border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["EncargadoEPS"]."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td border="0">';
                    $html.='<h3>Mes de Corte:</h3>';
                $html.='</td>';  
                $html.='<td border="0" colspan=2>';
                    $html.="<h3>".$DatosActa["FechaCorte"]."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
        $html.='</table>';
        
        $html.='<table cellspacing="2" cellpadding="2" border="0">';
            $html.='<tr>';
                $html.='<td style="text-align:left;">';
                    $html.='Total Cuenta por pagar ASMET SALUD al corte:';
                $html.='</td>';
                $html.='<td>';
                    $html.=' ';
                $html.='</td>';
                $html.='<td border="1" style="text-align:right;">';
                    $html.="<h3>$ ".number_format($DatosActa["ValorSegunEPS"])."</h3>";
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td>';
                    $html.='Total Cuenta por Pagar del proveedor  al corte:';
                $html.='</td>';
                $html.='<td>';
                    $html.=' ';
                $html.='</td>';
                $html.='<td border="1" style="text-align:right;">';
                    $html.="<h3>$ ".number_format($DatosActa["ValorSegunIPS"])."</h3>";
                $html.='</td>';
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td>';
                    $html.='Diferencia por Conciliar:';
                $html.='</td>';
                $html.='<td>';
                    $html.=' ';
                $html.='</td>';
                $html.='<td border="1" style="text-align:right;">';
                    $html.="<h3>$ ".number_format($DatosActa["Diferencia"])."</h3>";
                $html.='</td>';
            $html.='</tr>';
        $html.='</table>';
        return($html);
    }
    
    public function DiferenciasActaConciliacion($DatosActa) {
        $html='<table cellspacing="2" cellpadding="2" border="1" >';
            $html.='<tr>';
                $html.='<td colspan="3" style="text-align:center;">';
                    $html.='<h3>DETALLE DE DIFERENCIAS</h3>';
                $html.='</td>'; 
                
            $html.='</tr>';
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='1. Facturas canceladas por ASMET SALUD no descargadas por el proveedor:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["DiferenciaXPagos"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='2. Relación de facturas no registradas por ASMET SALUD:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["FacturasNoRegistradasXEPS"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='3. Glosas pendientes de conciliar:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["GlosasPendientesXConciliar"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='4. Facturas Devueltas:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["TotalDevoluciones"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='5. Impuestos no aplicados por la IPS:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["ImpuestosNoRelacionadosIPS"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='6. Descuentos financieros no merecidos en proceso de recobro RETEFUENTE:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["RetefuenteNoMerecida"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='7. Facturas registradas en ASMET SALUD que no estan en el listado del Proveedor:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["FacturasSinRelacionIPS"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='8. Retenciones de impuestos no procedentes (retefuente, ica, timbres):';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["RetencionesImpuestosNoProcedentes"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='9. Ajustes de Cartera en proceso (Notas credito IPS):';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["AjustesDeCartera"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='10. Facturas con diferencia en el Valor facturado:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["FacturasConValorDiferente"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='11. Facturas presentadas por reajuste de UPC:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["FacturasConReajusteUPC"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='12. Glosas conciliadas pendientes de descargar por el proveedor:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["GlosasConciliadasPendientesDescargaIPS"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='13. Anticipos pendientes de cruzar con facturas del proveedor:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["TotalAnticipos"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='14. Descuentos y/o reconocimientos según LMA:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["DescuentosReconocimientosLMA"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2">';
                    $html.='15. Facturas pendientes de auditoría:';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>".number_format($DatosActa["FacturasPendienteAuditoria"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2" style="text-align:right">';
                    $html.='<h4>TOTAL DIFERENCIAS:</h4>';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>$ ".number_format($DatosActa["Diferencia"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
            $html.='<tr>';
                $html.='<td  border="0" colspan="2" style="text-align:right">';
                    $html.='<h4>SALDO CONCILIADO PARA PAGO:</h4>';
                $html.='</td>';  
                $html.='<td  border="0" style="text-align:right;">';
                    $html.="<h3>$ ".number_format($DatosActa["SaldoConciliadoPago"])."</h3>";
                $html.='</td>'; 
            $html.='</tr>';
            
        $html.='</table>';
        return($html);
    }
    
    public function ResultadosCompromisosActa($idActaConsolidacion) {
        
        $html='<h3 style="text-decoration: underline;">RESULTADOS Y COMPROMISOS:</h3><br>';
        
        $sql="SELECT * FROM actas_conciliaciones_resultados_compromisos WHERE idActaConciliacion='$idActaConsolidacion'";
        $Consulta=$this->obCon->Query($sql);
        $html.='<ul>';
        while($DatosCompromisos=$this->obCon->FetchArray($Consulta)){
            $html.='<li><p align="justify">'.$DatosCompromisos["ResultadoCompromiso"].'</p></li>';
        }
        $html.='</ul>';
        return($html);
        
    }
    
    public function FirmasActaConciliacion($DatosActa) {
        $idActaConsolidacion=$DatosActa["ID"];
        $obNumLetra=new numeros_letras();
        $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
        $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
        //$dia=$obNumLetra->convertir(31);
        //print($DatosFechaFirma[1]);
        $mes=$obNumLetra->meses($DatosFechaFirma[1]);
        $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
        $html=("Para constancia, se firma en <strong>".($DatosActa["CiudadFirma"])."</strong>");
        $html.=(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]) en dos originales:<br><br><br><br><br>");
        
        $sql="SELECT * FROM actas_conciliaciones_firmas WHERE idActaConciliacion='$idActaConsolidacion'";
        $Consulta=$this->obCon->Query($sql);
        $html.='<table cellspacing="2" cellpadding="2" border="0">';
        $html.='<tr>';
        $i=0;
        while($DatosFirmas=$this->obCon->FetchArray($Consulta)){
            $i++;
            $html.='<td><hr>';
            $html.=''.$DatosFirmas["Nombre"];
            $html.='<br>'.$DatosFirmas["Cargo"];
            $html.='<br>'.$DatosFirmas["Empresa"];
            $html.='</td>';
            if($i==3){
                $html.='</tr><br><br><br>';
                $html.='<tr>';
            }
        }
        if($i==3){
            $html= substr($html,0,-4);
        }
        if($i<>3){
            $html.='</tr>';
        }
        
        $html.='</table>';
        //print($html);
        return($html);
        
    }
    
    public function ActaLiquidacionPDF($idActaLiquidacion,$AnexosEnPdf,$TipoConsulta) {
        
        $DatosActa=$this->obCon->DevuelveValores("actas_liquidaciones","ID",$idActaLiquidacion);
        $DatosTipoActa=$this->obCon->DevuelveValores("actas_liquidaciones_tipo","ID",$DatosActa["TipoActaLiquidacion"]);     
        $NIT_IPS=$DatosActa["NIT_IPS"];
        $TipoActa=$DatosActa["TipoActaLiquidacion"];
        $Unilateral="";
         
         if($TipoActa==2 or $TipoActa==5 or $TipoActa==8 or $TipoActa==10 ){

             $Unilateral="UNILATERAL";
                     
         };
        
        $this->PDF_IniActaLiquidacion("ACTA DE LIQUIDACIÓN No.", utf8_encode($DatosTipoActa["Header"]), "Footer text");
        $TamanoFuente=8;
        if(is_numeric($DatosActa["TamanoFuente"]) and $DatosActa["TamanoFuente"]>0 and $DatosActa["TamanoFuente"]<17){
            $TamanoFuente=$DatosActa["TamanoFuente"];
        }
        $this->PDF->SetFont('helvetica', '', $TamanoFuente);
        
        $Titulo='<p align="center"><h3>ACTA DE LIQUIDACIÓN '. $Unilateral. 'No. '.utf8_encode($DatosActa["IdentificadorActaEPS"].'</h3></p>');
        $Titulo.="";
        $Titulo.='<p align="center"><h3>'.utf8_encode($DatosTipoActa["Titulo"]).'</h3></p>';
        $this->PDF->writeHTML($Titulo, true, false, false, false, '');
        
        $html= $this->EncabezadoActaLiquidacion($DatosActa);
        $this->PDF->writeHTML($html, true, false, false, false, '');
        
        $html= $this->ContratosActaLiquidacion($idActaLiquidacion,$NIT_IPS);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->RepresentantesLegalesActaLiquidacion($DatosActa);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion1($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->ConsideracionesActa($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion2($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->TotalesActaLiquidacion($DatosActa,$TipoActa);
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        if($DatosActa["Asmet"]<>2){
            $html= $this->ObservacionesActaLiquidacion3($idActaLiquidacion,$TipoActa,$DatosActa);        
            $this->PDF->writeHTML("".$html, true, false, false, false, '');
        }
        
        $html= $this->ObservacionesActaLiquidacion4($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        if($DatosActa["Observaciones"]<>''){
            $html= $this->ObservacionesGenerales($idActaLiquidacion,$DatosActa);        
            $this->PDF->writeHTML("".$html, true, false, false, false, '');
        } 
        if($DatosActa["Asmet"]==2 and round($DatosActa["Saldo"])<>0){
            $html= $this->ObservacionesActasLiquidacionSAS($idActaLiquidacion,$DatosActa); 
            $this->PDF->writeHTML("".$html, true, false, false, false, '');
        }
        $html= $this->ObservacionesActaLiquidacion5($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        
        $html= $this->FirmasActaLiquidacion($DatosActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        if($AnexosEnPdf==1){
            if($TipoActa==4){    
                
                $this->PDF->AddPage('L', 'letter');
                $this->PDF->SetMargins(10, 20, 5);
                $html= $this->EncabezadoAnexoActaLiquidacion($DatosActa,$DatosTipoActa);                
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
                $html= $this->DatosItemsAnexoActaLiquidacionCapita($DatosActa,$TipoActa,$TipoConsulta);                
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
                $html= $this->FirmasActaLiquidacion($DatosActa);        
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
            }
            
            if($TipoActa==1 or $TipoActa==2 or $TipoActa==3 or $TipoActa==7 or $TipoActa==8 or $TipoActa==9 or $TipoActa==10){    
                
                $this->PDF->AddPage();
                $this->PDF->SetMargins(10, 20, 5);
                $html= $this->EncabezadoAnexoActaLiquidacion($DatosActa,$DatosTipoActa,0);                
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
                $html= $this->DatosItemsAnexoActaLiquidacionEventoXRadicados($DatosActa,$TipoActa,$TipoConsulta);                
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
                $html= $this->FirmasActaLiquidacion($DatosActa);        
                $this->PDF->writeHTML("".$html, true, false, false, false, '');
            }
            
        }
        
        $this->PDF_Output("Acta_Liquidacion_$idActaLiquidacion");
    }
    
    public function ObservacionesActasLiquidacionSAS($idActaLiquidacion,$DatosActa) {
        if(round($DatosActa["Saldo"])==0){
           return;
        }
        $obCon=new conexion(1);
        
        if(round($DatosActa["Saldo"])<0 AND $DatosActa["FormaPagoIPS"]==1){
            $opt="op13";
        }
        if(round($DatosActa["Saldo"])<0 AND $DatosActa["FormaPagoIPS"]==0){
            $opt="op15";
        }
        if(round($DatosActa["Saldo"])>0 ){
            $opt="op14";
        }
        
        $FechaFormateada = date("d/m/Y", strtotime($DatosActa["FechaCompromisoPagoIPS"]));
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE  Numeral='$opt' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql)); 
        $DatosConsideraciones["Texto"]= str_replace("@FechaCompromisoPagoIPS", $FechaFormateada, $DatosConsideraciones["Texto"]);
        $Numeral= str_replace(".", "", $this->UltimoNumeral);
        $Numeral=$Numeral+1;
        $html='<p align="justify"><strong>'.$Numeral.'.</strong>'.utf8_encode($DatosConsideraciones["Texto"])."</p>";  
       
        return($html);
    }
    
    public function DatosItemsAnexoActaLiquidacionEventoXRadicados($DatosActa,$TipoActa,$TipoConsulta) {
        $obCon=new conexion(1);
        $idActaLiquidacion=$DatosActa["ID"];
        $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $DatosActa["NIT_IPS"]);
        $db=$DatosIPS["DataBase"];
        $Back="#f2f2f2";
        $AnchoColumnas="51px";
        $html='<table border="0" cellpadding="1" cellspacing="1" align="center" >';
            $html.="<tr>";
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:5px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DEPARTAMENTO</strong></td>';
                $html.='<td style="text-align:center;font-size:6px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RADICADO</strong></td>';
                $html.='<td style="text-align:center;font-size:6px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>MES<BR>DE<BR>SERVICIOS</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VALOR<BR>FACTURADO</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RETENCION<BR>IMPUESTOS</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DEVOLUCION</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>GLOSA</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>GLOSA A<BR>FAVOR<BR>ASMET</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>NOTA<BR>CREDITO /<BR>COPAGOS</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RECUPERACION<BR>EN<BR>IMPUESTOS</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>OTROS<BR>DESCUENTOS</strong></td>';
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VALOR<BR>PAGADO</strong></td>';                
                $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>SALDO</strong></td>';
                
            $html.="</tr>";
            
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            
            if($TipoConsulta==1){
                $Tabla="actas_conciliaciones_items";
                $Condicion=" WHERE ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t5 WHERE t5.idContrato=$Tabla.NumeroContrato AND t5.idActaLiquidacion='$idActaLiquidacion') ";
                $GroupOrder=" GROUP BY NumeroRadicado,MesServicio,NumeroContrato ORDER BY MesServicio,NumeroRadicado ";
                $TablaUnion="historial_carteracargada_eps";

                $Union3="     
                         SELECT  t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,t1.NumeroContrato,
                            SUM(t1.ValorOriginal) AS ValorDocumento,
                            '0' AS Impuestos,'0' AS TotalPagos,'0' AS TotalNotasCopagos,
                            '0' AS DescuentoPGP,'0' AS DescuentoBDUA,'0' AS TotalOtrosDescuentos,
                            '0' AS TotalGlosaInicial,'0' AS TotalGlosaFavor,
                            SUM(t1.ValorOriginal) AS TotalDevoluciones,'0' AS Saldo,'0' as GlosaXConciliar 

                            FROM $db.$TablaUnion t1 WHERE NOT
                        EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado )
                         AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                        AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                        AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t4 WHERE t4.Estado=1 AND t1.TipoOperacion=t4.TipoOperacion AND Aplicacion='FACTURA')
                              ";

                $sql=" UNION ALL SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,NumeroContrato,SUM(ValorDocumento) AS ValorDocumento,
                            SUM(Impuestos) AS Impuestos,SUM(TotalPagos + TotalAnticipos) AS TotalPagos,SUM(TotalCopagos) AS TotalNotasCopagos,
                            SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                            SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                            SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo,SUM(GlosaXConciliar) AS GlosaXConciliar 

                            FROM $db.$Tabla $Condicion";
                $sql=$Union3.$sql.$GroupOrder;

                //print($sql);

            }    

            if($TipoConsulta==2){
                $Tabla="actas_liquidaciones_radicados_items";
                $TablaUnion="historial_carteracargada_eps";
                $Condicion=" WHERE idActaLiquidacion='$idActaLiquidacion' ";
                $GroupOrder=" GROUP BY NumeroRadicado,MesServicio,NumeroContrato ORDER BY MesServicio,NumeroRadicado ";
                $sql=" UNION ALL

                SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,SUM(ValorDocumento) AS ValorDocumento,
                                    SUM(Impuestos) AS Impuestos,SUM(TotalPagos + TotalAnticipos) AS TotalPagos,SUM(TotalCopagos) AS TotalNotasCopagos,
                                    SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) AS TotalOtrosDescuentos,
                                    SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                                    SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS Saldo,sum(GlosaXConciliar) as GlosaXConciliar

                                    FROM $db.$Tabla $Condicion ";

                $Union3="     
                         SELECT  t1.MesServicio,t1.DepartamentoRadicacion,t1.NumeroRadicado,t1.NumeroContrato,
                            SUM(t1.ValorOriginal) AS ValorDocumento,
                            '0' AS Impuestos,'0' AS TotalPagos,'0' AS TotalNotasCopagos,
                            '0' AS DescuentoPGP,'0' AS DescuentoBDUA,'0' AS TotalOtrosDescuentos,
                            '0' AS TotalGlosaInicial,'0' AS TotalGlosaFavor,
                            SUM(t1.ValorOriginal) AS TotalDevoluciones,'0' AS Saldo,'0' AS GlosaXConciliar

                            FROM $db.$TablaUnion t1 WHERE NOT
                        EXISTS (SELECT 1 FROM $db.$Tabla t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado )
                         AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                        AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     

                              ";

                $sql=$Union3.$sql.$GroupOrder;

            }    
            $Consulta=$obCon->Query($sql);
            $Totales["ValorDocumento"]=0;
            $Totales["Impuestos"]=0;
            $Totales["TotalDevoluciones"]=0;
            $Totales["TotalGlosaInicial"]=0;
            $Totales["TotalGlosaFavor"]=0;
            $Totales["TotalNotasCopagos"]=0;
            $Totales["TotalOtrosDescuentos"]=0;
            $Totales["TotalPagos"]=0;
            $Totales["Saldo"]=0;
            $Totales["GlosaXConciliar"]=0;
            if($TipoActa<>6){
                $z=0;
                while($DatosVista=$obCon->FetchAssoc($Consulta)){
                    if($z==0){
                        $Back="white";
                        $z=1;
                    }else{
                        $Back="#f2f2f2";                        
                        $z=0;
                    }
                    $Totales["ValorDocumento"]=$Totales["ValorDocumento"]+$DatosVista["ValorDocumento"]; 
                    $Totales["Impuestos"]=$Totales["Impuestos"]+$DatosVista["Impuestos"]; 
                    $Totales["TotalDevoluciones"]=$Totales["TotalDevoluciones"]+$DatosVista["TotalDevoluciones"]; 
                    $Totales["TotalGlosaInicial"]=$Totales["TotalGlosaInicial"]+$DatosVista["TotalGlosaInicial"]; 
                    $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]; 
                    $Totales["TotalNotasCopagos"]=$Totales["TotalNotasCopagos"]+$DatosVista["TotalNotasCopagos"]; 
                    $Totales["TotalOtrosDescuentos"]=$Totales["TotalOtrosDescuentos"]+$DatosVista["TotalOtrosDescuentos"];
                    $Totales["TotalPagos"]=$Totales["TotalPagos"]+$DatosVista["TotalPagos"]; 
                    $Totales["Saldo"]=$Totales["Saldo"]+$DatosVista["Saldo"]; 
                    $Totales["GlosaXConciliar"]=$Totales["GlosaXConciliar"]+$DatosVista["GlosaXConciliar"];
                    $html.="<tr>";
                        $html.='<td style="text-align:left;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["DepartamentoRadicacion"].'</td>';
                        $html.='<td style="text-align:left;font-size:6px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["NumeroRadicado"].'</td>';
                        $html.='<td style="text-align:left;font-size:6px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["MesServicio"].'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["ValorDocumento"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["Impuestos"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalDevoluciones"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalGlosaInicial"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalGlosaFavor"]+$DatosVista["GlosaXConciliar"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalNotasCopagos"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">0</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalOtrosDescuentos"]).'</td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalPagos"]).'</td>';                
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["Saldo"]).'</td>';

                    $html.="</tr>";
                    
                    
                }               
                
            }
            $Back="#f2f2f2";
            
            $html.="<tr>";
                        $html.='<td style="text-align:center;width:'.$AnchoColumnas.';font-size:6px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                        $html.='<td style="text-align:center;font-size:6px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                        $html.='<td style="text-align:center;font-size:7px;width:'.$AnchoColumnas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TOTAL</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["ValorDocumento"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["Impuestos"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalDevoluciones"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalGlosaInicial"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalGlosaFavor"]+$Totales["GlosaXConciliar"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalNotasCopagos"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>0</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalOtrosDescuentos"]).'</strong></td>';
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalPagos"]).'</strong></td>';                
                        $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["Saldo"]).'</strong></td>';

                    $html.="</tr>";
                    
            if($DatosActa["PagosPendientesPorLegalizar"]<>0){
                $html.="<tr>";
                        
                    $html.='<td colspan="12" style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>PAGOS PENDIENTES POR LEGALIZAR</strong></td>';                
                    $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($DatosActa["PagosPendientesPorLegalizar"]).'</strong></td>';

                $html.="</tr>";
                $html.="<tr>";
                        
                    $html.='<td colspan="12" style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>SALDO FINAL</strong></td>';                
                    $html.='<td style="text-align:rigth;width:'.$AnchoColumnas.';font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["Saldo"]-$DatosActa["PagosPendientesPorLegalizar"]).'</strong></td>';

                $html.="</tr>";
            }
            
        $html.='</table>';  
        return($html);
    }
    
    public function DatosItemsAnexoActaLiquidacionCapita($DatosActa,$TipoActa,$TipoConsulta) {
        $obCon=new conexion(1);
        $idActaLiquidacion=$DatosActa["ID"];
        $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $DatosActa["NIT_IPS"]);
        $db=$DatosIPS["DataBase"];
        $Back="#f2f2f2";
        $html='<table border="0" cellpadding="1" cellspacing="1" align="center" >';
            $html.="<tr>";
                $html.='<td style="text-align:center;width:65px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DEPARTAMENTO</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>MUNICIPIO</strong></td>';
                $html.='<td style="text-align:center;font-size:6px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>Mes LMA<BR> (AAAAMM)</strong></td>';
                $html.='<td style="text-align:center;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DIAS<BR>RECONOCIDOS LMA</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VR A PAGAR IPS SEGÚN LMA</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>No. RADICADO</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>No. FACTURA</strong></td>';
                $html.='<td style="text-align:center;width:60px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VALOR<BR>FACTURADO</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VALOR<BR>RETENIDO</strong></td>';
                $html.='<td style="text-align:center;;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>Descuento o <BR>Reconocimiento<BR> por BDUA</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DESCUENTO<BR>INICIAL</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>DESCUENTOS<BR>CONCILIADO A FAVOR ASMET</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>VALOR<BR>PAGADO</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>SALDO</strong></td>';
                
            $html.="</tr>";
            
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            
            if($TipoConsulta==1 AND $TipoActa<>6){
                $Tabla="actas_conciliaciones_items";
                $sql="SELECT MesServicio,NumeroContrato, DepartamentoRadicacion,GROUP_CONCAT(NumeroRadicado) AS NumeroRadicado,CodigoSucursal AS CodigoDaneAnexo,
                        (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                        NumeroContrato,GROUP_CONCAT(NumeroFactura) as NumeroFactura,SUM(ValorDocumento) as ValorDocumento,SUM(Impuestos) AS Impuestos,SUM(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                        SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                        SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) as Saldo,SUM(NumeroDiasLMA) AS NumeroDiasLMA,SUM(ValorAPagarLMA) AS  ValorAPagarLMA                  
                        FROM $db.$Tabla WHERE                    
                          ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato AND t2.idActaLiquidacion='$idActaLiquidacion') 
                        GROUP BY  MesServicio,NumeroContrato,CodigoDaneAnexo ORDER BY MesServicio,CodigoDaneAnexo,NumeroFactura";

            }    
            if($TipoConsulta==2){
                $Tabla="actas_liquidaciones_items";
                /*
                $sql="SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,
                        (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                        NumeroContrato,NumeroFactura,ValorDocumento,Impuestos,(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                        DescuentoPGP,DescuentoBDUA,(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,TotalGlosaInicial,TotalGlosaFavor,
                        TotalDevoluciones,ValorSegunEPS as Saldo,NumeroDiasLMA,ValorAPagarLMA                 
                        FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                  
                          ";
                 * 
                 */
                $sql="SELECT MesServicio,NumeroContrato,DepartamentoRadicacion,GROUP_CONCAT(NumeroRadicado) AS NumeroRadicado,CodigoSucursal AS CodigoDaneAnexo,
                        (SELECT Ciudad FROM municipios_dane WHERE CodigoDane=$Tabla.CodigoSucursal LIMIT 1) as Municipio,
                        NumeroContrato,GROUP_CONCAT(NumeroFactura) as NumeroFactura,SUM(ValorDocumento) as ValorDocumento,SUM(Impuestos) AS Impuestos,SUM(TotalPagos+TotalCopagos+TotalAnticipos) as TotalPagos,
                        SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos+AjustesCartera) as TotalOtrosDescuentos,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                        SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) as Saldo,SUM(NumeroDiasLMA) AS NumeroDiasLMA,SUM(ValorAPagarLMA) AS  ValorAPagarLMA                  
                        FROM $db.$Tabla WHERE idActaLiquidacion='$idActaLiquidacion'                

                        GROUP BY  MesServicio,NumeroContrato,CodigoDaneAnexo ORDER BY MesServicio,CodigoDaneAnexo,NumeroFactura";
            } 
            //print($sql);
            if($TipoActa<>6){
                $Consulta=$obCon->Query($sql);
            }
            $Totales["ValorDocumento"]=0;
            $Totales["Impuestos"]=0;
            $Totales["TotalDevoluciones"]=0;
            $Totales["TotalGlosaInicial"]=0;
            $Totales["TotalGlosaFavor"]=0;
            //$Totales["TotalNotasCopagos"]=0;
            $Totales["TotalOtrosDescuentos"]=0;
            $Totales["TotalPagos"]=0;
            $Totales["Saldo"]=0;
            $Totales["ValorAPagarLMA"]=0;
            $Totales["DescuentoBDUA"]=0;
            if($TipoActa<>6){
                $z=0;
                while($DatosVista=$obCon->FetchAssoc($Consulta)){
                    if($z==0){
                        $Back="white";
                        $z=1;
                    }else{
                        $Back="#f2f2f2";                        
                        $z=0;
                    }
                    $Totales["ValorDocumento"]=$Totales["ValorDocumento"]+$DatosVista["ValorDocumento"]; 
                    $Totales["Impuestos"]=$Totales["Impuestos"]+$DatosVista["Impuestos"]; 
                    $Totales["TotalDevoluciones"]=$Totales["TotalDevoluciones"]+$DatosVista["TotalDevoluciones"]; 
                    $Totales["TotalGlosaInicial"]=$Totales["TotalGlosaInicial"]+$DatosVista["TotalGlosaInicial"]; 
                    $Totales["TotalGlosaFavor"]=$Totales["TotalGlosaFavor"]+$DatosVista["TotalGlosaFavor"]; 
                    //$Totales["TotalNotasCopagos"]=$Totales["TotalNotasCopagos"]+$DatosVista["TotalNotasCopagos"]; 
                    $Totales["TotalOtrosDescuentos"]=$Totales["TotalOtrosDescuentos"]+$DatosVista["TotalOtrosDescuentos"];
                    $Totales["TotalPagos"]=$Totales["TotalPagos"]+$DatosVista["TotalPagos"]; 
                    $Totales["Saldo"]=$Totales["Saldo"]+$DatosVista["Saldo"]; 
                    $Totales["ValorAPagarLMA"]=$Totales["ValorAPagarLMA"]+$DatosVista["ValorAPagarLMA"]; 
                    $Totales["DescuentoBDUA"]=$Totales["DescuentoBDUA"]+$DatosVista["DescuentoBDUA"]; 
                    
                    $html.="<tr>";
                        $html.='<td style="text-align:center;width:65px;font-size:5px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["DepartamentoRadicacion"].'</td>';
                        $html.='<td style="text-align:center;font-size:4px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["Municipio"].'</td>';
                        $html.='<td style="text-align:center;font-size:7px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["MesServicio"].'</td>';
                        $html.='<td style="text-align:center;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["NumeroDiasLMA"].'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["ValorAPagarLMA"]).'</td>';
                        $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["NumeroRadicado"].'</td>';
                        $html.='<td style="text-align:left;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosVista["NumeroFactura"].'</td>';
                        $html.='<td style="text-align:rigth;width:60px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["ValorDocumento"]).'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["Impuestos"]).'</td>';
                        $html.='<td style="text-align:rigth;;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["DescuentoBDUA"]).'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalGlosaInicial"]).'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalGlosaFavor"]).'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["TotalPagos"]).'</td>';
                        $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosVista["Saldo"]).'</td>';

                    $html.="</tr>";
                    
                    
                }               
                
            }
            $Back="#f2f2f2";
            $html.="<tr>";
                $html.='<td style="text-align:center;width:65px;font-size:5px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                $html.='<td style="text-align:center;font-size:4px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                $html.='<td style="text-align:center;font-size:7px;width:40px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                $html.='<td style="text-align:rigth;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TOTAL:</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["ValorAPagarLMA"]).'</strong></td>';
                $html.='<td style="text-align:center;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                $html.='<td style="text-align:left;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"> </td>';
                $html.='<td style="text-align:rigth;width:60px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["ValorDocumento"]).'</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["Impuestos"]).'</strong></td>';
                $html.='<td style="text-align:rigth;;width:85px;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["DescuentoBDUA"]).'</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalGlosaInicial"]).'</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalGlosaFavor"]).'</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalPagos"]).'</strong></td>';
                $html.='<td style="text-align:rigth;font-size:7px;border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>'.number_format($Totales["Saldo"]).'</strong></td>';
                
            $html.="</tr>";
            $Back="white";
            $html.="<tr>";
                $html.='<td colspan="14"> </td>';
            $html.="</tr>";
            $html.="<tr>";
            
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>VR A PAGAR IPS S/N LMA: </strong> </td>';
                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($Totales["ValorAPagarLMA"]).'</strong></td>';
                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>VALOR RETENCION DE IMPUESTOS</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($Totales["Impuestos"]).'</strong></td>';                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>DESCUENTOS A FAVOR ASMET</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalGlosaFavor"]).'</strong></td>';                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>OTROS DESCUENTOS CONCILIADOS</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($DatosActa["OtrosDescuentosConciliadosAfavor"]).'</strong></td>';                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>VALOR PAGADO</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($Totales["TotalPagos"]).'</strong></td>';                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>PAGOS PENDIENTES POR LEGALIZAR</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($DatosActa["PagosPendientesPorLegalizar"]).'</strong></td>';                
            $html.="</tr>";
            $html.="<tr>";                
                $html.='<td colspan="12" style="text-align:rigth;font-size:9px;background-color: '.$Back.';"><strong>SALDO</strong> </td>';                
                $html.='<td colspan="2" style="text-align:left;font-size:9px;background-color: '.$Back.';"><strong>'.number_format($Totales["Saldo"]-$DatosActa["OtrosDescuentosConciliadosAfavor"]-$DatosActa["PagosPendientesPorLegalizar"]).'</strong></td>';                
            $html.="</tr>";
            
        $html.='</table>';  
        return($html);
    }
    
    public function EncabezadoAnexoActaLiquidacion($DatosActa,$DatosTipoActa,$MuestraPercapitas=1) {
        $obCon=new conexion(1);
        $idActaLiquidacion=$DatosActa["ID"];
        
        $TituloTipoActa=$DatosTipoActa["Titulo"];
        $Modalidad=$DatosTipoActa["Nombre"];
        $CmbIPS=$DatosActa["NIT_IPS"];
        $html='<table cellspacing="1" cellpadding="1" border="0">';
            $html.="<tr>";
                $html.='<td colspan="14" style="text-align:center"><h3>'.utf8_encode($TituloTipoActa).'</h3></td>';
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2" style="text-align:left"><strong>IPS:</strong></td>'; 
                $html.='<td colspan="10"><strong>'.utf8_encode($DatosActa["RazonSocialIPS"]).'</strong></td>';
                $html.='<td colspan="2"> </td>';
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2"><strong>NIT:</strong></td>'; 
                $html.='<td colspan="4"><strong>'.($DatosActa["NIT_IPS"]).'</strong></td>';
                $html.='<td colspan="8"> </td>';
            $html.="</tr>";
            
            $sql="SELECT t1.ID,t1.NombreContrato AS Contrato,t1.FechaInicial as FechaInicioContrato,t1.FechaFinal  as FechaFinalContrato,t1.Valor as ValorContrato
                FROM actas_liquidaciones_contratos t1 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion'";
            $Consulta= $obCon->Query($sql);
            $i=0;
            while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                $ContratosAgregados["Contrato"][$i]=$DatosContratos["Contrato"];
                $ContratosAgregados["FechaInicioContrato"][$i]=$DatosContratos["FechaInicioContrato"];
                $ContratosAgregados["FechaFinalContrato"][$i]=$DatosContratos["FechaFinalContrato"];
                $ContratosAgregados["ValorContrato"][$i]=$DatosContratos["ValorContrato"];
                $i=$i+1;
            }
            
            $html.="<tr>";
                $html.='<td colspan="2"><strong>Contrato:</strong></td>';
                foreach ($ContratosAgregados["Contrato"] as $value) {
                    $html.='<td colspan="2">'.$value.'</td>';
                }
                
                
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2" ><strong>Vigencia (Inicio):</strong></td>'; 
                foreach ($ContratosAgregados["FechaInicioContrato"] as $value) {
                    $html.='<td colspan="2">'.$value.'</td>';
                }
                
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2" ><strong>Vigencia (Fin):</strong></td>'; 
                foreach ($ContratosAgregados["FechaFinalContrato"] as $value) {
                    $html.='<td colspan="2">'.$value.'</td>';
                }
                
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2" ><strong>Valor Contrato:</strong></td>'; 
                foreach ($ContratosAgregados["ValorContrato"] as $value) {
                    $html.='<td colspan="2">'.number_format($value).'</td>';
                }
                
            $html.="</tr>";
            $html.="<tr>";
                $html.='<td colspan="2"><strong>Modalidad:</strong></td>'; 
                $html.='<td colspan="2">'.$Modalidad.'</td>';
                $html.='<td colspan="10"> </td>';
            $html.="</tr>";
            if($MuestraPercapitas==1){
                $sql="SELECT t3.PorcentajePoblacional,t3.ValorPercapitaXDia,
                    (SELECT Ciudad FROM municipios_dane t4 WHERE t3.CodigoDane=t4.CodigoDane LIMIT 1) as Municipio 
                                 FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente
                                 INNER JOIN contrato_percapita t3 ON t2.Contrato=t3.Contrato                             
                                 WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$CmbIPS'";
                $Consulta=$obCon->Query($sql);
                while($DatosPercapita=$obCon->FetchAssoc($Consulta)){
                    $html.="<tr>";
                        $html.='<td colspan="2" style="font-size:6px"><strong>Municipio:</strong></td>'; 
                        $html.='<td colspan="2" style="font-size:6px">'.$DatosPercapita["Municipio"].'</td>';
                        $html.='<td colspan="10"> </td>';
                    $html.="</tr>";
                    $html.="<tr>";
                        $html.='<td colspan="2" style="font-size:6px"><strong>Valor percapita día:</strong></td>'; 
                        $html.='<td colspan="2" style="font-size:6px">'.$DatosPercapita["ValorPercapitaXDia"].'</td>';
                        $html.='<td colspan="10"> </td>';
                    $html.="</tr>";
                    $html.="<tr>";
                        $html.='<td colspan="2" style="font-size:6px"><strong>% Poblacional:</strong></td>'; 
                        $html.='<td colspan="2" style="font-size:6px">'.$DatosPercapita["PorcentajePoblacional"].'%</td>';
                        $html.='<td colspan="10"> </td>';
                    $html.="</tr>";
                }
            }
        $html.="</table>";
        return($html);
    }
    
    public function ObservacionesGenerales($idActaLiquidacion,$DatosActa) {
          
        $html='<p align="justify">'. ($DatosActa["Observaciones"])."</p>";        
        return($html);
    }
    
    public function TotalesActaLiquidacion($DatosActa,$TipoActa) {
        $obNumLetra=new numeros_letras();
        $SaldoAPagarContratista=0;
        $SaldoAPagarContratante=0;
        if($DatosActa["Saldo"]>0){
            $SaldoAPagarContratista=$DatosActa["Saldo"];
        }else{
            $SaldoAPagarContratante=$DatosActa["Saldo"];
        }
        $html="";
        if($TipoActa==3 or $TipoActa==6){
            $DatosActa["Saldo"]=0;
            $DatosActa["ValorFacturado"]=0;
            $DatosActa["RetencionImpuestos"]=0;
            $DatosActa["Devolucion"]=0;
            $DatosActa["Glosa"]=0;
            $DatosActa["GlosaFavor"]=0;
            $DatosActa["NotasCopagos"]=0;
            $DatosActa["RecuperacionImpuestos"]=0;
            $DatosActa["OtrosDescuentos"]=0;
            
            $DatosActa["ValorPagado"]=0;
            $DatosActa["Saldo"]=0;
            $DatosActa["DescuentoBDUA"]=0;
            $DatosActa["GlosaFavor"]=0;
            
        }
        if($TipoActa==1 or $TipoActa==2 or $TipoActa==3 or $TipoActa==7 or $TipoActa==8 or $TipoActa==9 or $TipoActa==10){
            if($DatosActa["PagosPendientesPorLegalizar"]==0){
                $html='<table cellspacing="1" cellpadding="1" border="1">
                            <tr>
                                <td style="text-align:center;"><strong>VALOR FACTURADO</strong></td>
                                <td style="text-align:center;"><strong>RETENCION IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>DEVOLUCIÓN</strong></td>
                                <td style="text-align:center;"><strong>GLOSA</strong></td>
                                <td style="text-align:center;"><strong>GLOSA A FAVOR ASMET</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorFacturado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RetencionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Devolucion"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Glosa"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["GlosaFavor"]).'</td>

                            </tr>
                            <tr>
                                <td colspan="5">

                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center;"><strong>NOTA CREDITO / COPAGOS</strong></td>
                                <td style="text-align:center;"><strong>RECUPERACION EN IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>OTROS DESCUENTOS</strong></td>
                                <td style="text-align:center;"><strong>VALOR PAGADO</strong></td>
                                <td style="text-align:center;"><strong>SALDO</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["NotasCopagos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RecuperacionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["OtrosDescuentos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorPagado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Saldo"]).'</td>

                            </tr>

                            <tr>
                                <td colspan="5">

                                </td>
                            </tr>
                            ';
                $ColspanTotales=4;
            
            }else{
                $html='<table cellspacing="1" cellpadding="1" border="1">
                            <tr>
                                <td style="text-align:center;"><strong>VALOR FACTURADO</strong></td>
                                <td style="text-align:center;"><strong>RETENCION IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>DEVOLUCIÓN</strong></td>
                                <td style="text-align:center;"><strong>GLOSA</strong></td>
                                <td colspan="2"  style="text-align:center;"><strong>GLOSA A FAVOR ASMET</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorFacturado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RetencionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Devolucion"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Glosa"]).'</td>
                                <td colspan="2" style="text-align:rigth;">'. number_format($DatosActa["GlosaFavor"]).'</td>

                            </tr>
                            <tr>
                                <td colspan="6">

                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center;"><strong>NOTA CREDITO / COPAGOS</strong></td>
                                <td style="text-align:center;"><strong>RECUPERACION EN IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>OTROS DESCUENTOS</strong></td>
                                <td style="text-align:center;"><strong>VALOR PAGADO</strong></td>
                                <td style="text-align:center;"><strong>PAGOS PENDIENTES POR LEGALIZAR</strong></td>
                                <td style="text-align:center;"><strong>SALDO</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["NotasCopagos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RecuperacionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["OtrosDescuentos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorPagado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["PagosPendientesPorLegalizar"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Saldo"]).'</td>

                            </tr>

                            <tr>
                                <td colspan="6">

                                </td>
                            </tr>
                            ';
                $ColspanTotales=5;
            }
                    
                    
        }
        
        if($TipoActa==4 or $TipoActa==5 or $TipoActa==6){
            if($DatosActa["PagosPendientesPorLegalizar"]==0){
                $html='<table cellspacing="1" cellpadding="1" border="1">
                            <tr>
                                <td style="text-align:center;"><strong>VALOR FACTURADO</strong></td>
                                <td style="text-align:center;"><strong>RETENCION IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>Descuento o Reconocimiento por BDUA</strong></td>
                                <td style="text-align:center;"><strong>DESCUENTOS CONCILIADOS A FAVOR ASMET</strong></td>
                                <td style="text-align:center;"><strong>VALOR PAGADO</strong></td>
                                <td style="text-align:center;"><strong>SALDO</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorFacturado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RetencionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["DescuentoBDUA"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["GlosaFavor"]+$DatosActa["OtrosDescuentosConciliadosAfavor"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorPagado"]+$DatosActa["NotasCopagos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["Saldo"]).'</td>    

                            </tr>
                            <tr>
                                <td colspan="6">

                                </td>
                            </tr>

                            ';
                $ColspanTotales=5;
            }else{
                $html='<table cellspacing="1" cellpadding="1" border="1">
                            <tr>
                                <td style="text-align:center;"><strong>VALOR FACTURADO</strong></td>
                                <td style="text-align:center;"><strong>RETENCION IMPUESTOS</strong></td>
                                <td style="text-align:center;"><strong>Descuento o Reconocimiento por BDUA</strong></td>
                                <td style="text-align:center;"><strong>DESCUENTOS CONCILIADOS A FAVOR ASMET</strong></td>
                                <td style="text-align:center;"><strong>VALOR PAGADO</strong></td>
                                <td style="text-align:center;"><strong>PAGOS PENDIENTES POR LEGALIZAR</strong></td>
                                <td style="text-align:center;"><strong>SALDO</strong></td>

                            </tr>

                            <tr>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorFacturado"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["RetencionImpuestos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["DescuentoBDUA"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["GlosaFavor"]+$DatosActa["OtrosDescuentosConciliadosAfavor"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["ValorPagado"]+$DatosActa["NotasCopagos"]).'</td>
                                <td style="text-align:rigth;">'. number_format($DatosActa["PagosPendientesPorLegalizar"]).'</td>    
                                <td style="text-align:rigth;">'. number_format($DatosActa["Saldo"]).'</td>    

                            </tr>
                            <tr>
                                <td colspan="6">

                                </td>
                            </tr>

                            ';
                $ColspanTotales=6;
            }
        }
      
        if(round($DatosActa["Saldo"])==0){
            
            $html.='<tr>
                <td colspan="'.$ColspanTotales.'" style="text-align:left;"></td>
                <td style="text-align:rigth;">'. number_format($SaldoAPagarContratista).'</td>

            </tr>';
        }
      
         
      if(round($DatosActa["Saldo"])>0){
            $TextoConclusionTotales="En razón de lo anterior, la presente liquidación generó un saldo a pagar al CONTRATISTA DE";
            if($DatosActa["Asmet"]==2){
                $TextoConclusionTotales=strtoupper($obNumLetra->convertir(abs($DatosActa["Saldo"])))." PESOS";
            }
            $html.='<tr>
                <td colspan="'.$ColspanTotales.'" style="text-align:left;"><strong>'.$TextoConclusionTotales.'</strong></td>
                <td style="text-align:rigth;">'. number_format($SaldoAPagarContratista).'</td>

            </tr>';
           
        }
        if(round($DatosActa["Saldo"])<0){
            $TextoConclusionTotales="En razón de lo anterior, la presente liquidación generó un saldo a favor del CONTRATANTE DE ";
            if($DatosActa["Asmet"]==2){
                $TextoConclusionTotales=strtoupper($obNumLetra->convertir(abs($DatosActa["Saldo"])))." PESOS";
            }
            
            $html.='<tr>
                <td  colspan="'.$ColspanTotales.'" style="text-align:left;"><strong>'.$TextoConclusionTotales.'</strong></td>
                <td style="text-align:rigth;">'. number_format(abs($SaldoAPagarContratante)).'</td>

            </tr>';
        }
        $html.='</table>';
            
        return($html);
    }
    
    public function FirmasActaLiquidacion($DatosActa) {
        $idActaLiquidacion=$DatosActa["ID"];
        $obNumLetra=new numeros_letras();
        $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
        $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
        //$dia=$obNumLetra->convertir(31);
        $mes=$obNumLetra->meses($DatosFechaFirma[1]);
        $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
        $html=('<p align="justify"><BR>Para constancia se firma en <strong>'.($DatosActa["CiudadFirma"])."</strong>");
        $html.=(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]),  en dos Originales uno para la IPS y otro para la EPS:<br><br><br><br><br><br></p>");
        
        $sql="SELECT * FROM actas_liquidaciones_firmas WHERE idActaLiquidacion='$idActaLiquidacion'";
        $Consulta=$this->obCon->Query($sql);
        if($this->obCon->NumRows($Consulta)){
        $html.='<table cellspacing="2" cellpadding="2" border="0">';
        $html.='<tr>';
        $i=0;
        while($DatosFirmas=$this->obCon->FetchArray($Consulta)){
            $i++;
            $html.='<td><hr>';
            $html.=''.$DatosFirmas["Nombre"];
            $html.='<br>'.$DatosFirmas["Cargo"];
            $html.='<br>'.$DatosFirmas["Empresa"];
            if($DatosFirmas["Aprueba"]==1){
                $html.='<br>Aprobó';
            }
            $html.='</td>';
            if($i==3){
                $html.='</tr><BR><BR><BR><BR><BR><BR>';
                $html.='<tr>';
            }
        }
        if($i==3){
            $html= substr($html,0,-4);
        }
        if($i<>3){
            $html.='</tr>';
        }
        
        $html.='</table>';
        }
        $DatosUsuario=$this->obCon->DevuelveValores("usuarios","idUsuarios",$DatosActa["idUser"]);
        $html.='<BR><BR>';
        $html.='Elaboró: '.($DatosUsuario["Nombre"])." ".($DatosUsuario["Apellido"]);
        $html.='<BR>';
        $html.='Revisó: '.($DatosActa["Revisa"]);
        $html.='<BR>';
        $html.='Auditó: ';
        //print($html);
        return($html);
        
    }
    
    public function ObservacionesActaLiquidacion5($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op5' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'.utf8_encode($DatosConsideraciones["Texto"])."</p>";        
        return($html);
    }
    public function ObservacionesActaLiquidacion4($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op4' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'.utf8_encode($DatosConsideraciones["Texto"])."</p>";        
        return($html);
    }
    
    public function ObservacionesActaLiquidacion3($idActaLiquidacion,$TipoActa,$DatosActa) {
        $obCon=new conexion(1);
        $obNumLetra=new numeros_letras();
        if($DatosActa["Saldo"]>=0){
            $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op3' LIMIT 1";
        }else{
            $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op10' LIMIT 1";
        }
        
        if(round($DatosActa["Saldo"])==0 or $TipoActa==3 or $TipoActa==6){
            $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op11' LIMIT 1";
        }
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";
        $SaldoEnLetras=$obNumLetra->convertir(abs($DatosActa["Saldo"]));
        $SaldoEnLetras.=" PESOS ($ ".number_format(abs($DatosActa["Saldo"])).")";
        
        $html= str_replace("@ValorLetras",strtoupper("<strong>".$SaldoEnLetras."</strong>"), $html);
        
        $sql="SELECT t2.* FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente WHERE t1.idActaLiquidacion='$idActaLiquidacion' ORDER BY t1.ID";
        
        $sql="SELECT t1.ID,t1.NombreContrato AS Contrato,t1.FechaInicial as FechaInicioContrato,t1.FechaFinal  as FechaFinalContrato,t1.Valor as ValorContrato
                FROM actas_liquidaciones_contratos t1 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion' ORDER BY ID";
        
        $Consulta=$obCon->Query($sql);
        $ContratosActa="";
        
        while($DatosContratos=$obCon->FetchAssoc($Consulta)){
            
            $FechaInicial=explode("-",$DatosContratos["FechaInicioContrato"]);   
            //print_r($FechaInicial);
            $dia=$obNumLetra->convertir($FechaInicial[2]); 
            if($dia=="un"){
                $dia="primero";
            }
            
            $mes=$obNumLetra->meses($FechaInicial[1]);
            $anio=$obNumLetra->convertir($FechaInicial[0]);
            $FechaFinal=explode("-",$DatosContratos["FechaFinalContrato"]);
            $diaFin=$obNumLetra->convertir($FechaFinal[2]); 
            if($diaFin=="un"){
                $diaFin="primero";
            }
            $mesFin=$obNumLetra->meses($FechaFinal[1]);
            $anioFin=$obNumLetra->convertir($FechaFinal[0]);
            $ContratosActa.="<strong>".$DatosContratos["Contrato"]."</strong> con vigencia del $dia de $mes del $anio al $diaFin de $mesFin del $anioFin, ";
        }
        //print("Contratos $ContratosActa");
        $ContratosActa=substr($ContratosActa,0,-2);
        
        $html= str_replace("@Numerocontratos", $ContratosActa, $html);
        return($html);
    }
    
    public function ConsideracionesActa($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $DatosActa= $obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
        $sql="SELECT t1.ID,t1.NombreContrato AS Contrato,t1.FechaInicial as FechaInicioContrato,t1.FechaFinal  as FechaFinalContrato,t1.Valor as ValorContrato
                FROM actas_liquidaciones_contratos t1 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion'";
        
        $ConsultaContratos=$obCon->Query($sql);
        $Contratos="";
        while($DatosContratos=$obCon->FetchAssoc($ConsultaContratos)){
            $Contratos.=$DatosContratos["Contrato"].", ";
            
        }
        
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND SUBSTRING(Numeral,1,2)<>'op' ORDER BY Orden";
        
        $Consulta=$obCon->Query($sql);
        $html="";
        
        while($DatosConsideraciones=$obCon->FetchAssoc($Consulta)){
            $Texto= str_replace("@DocumentoReferencia", $DatosActa["DocumentoReferencia"], $DatosConsideraciones["Texto"]);
            $Texto= str_replace("@NombreIPS", $DatosActa["RazonSocialIPS"], $Texto);
            $Texto= str_replace("@NumeroContrato", $Contratos, $Texto);
            $html.='<p align="justify"><strong>'.(utf8_encode($DatosConsideraciones["Numeral"]))."</strong> ".utf8_encode($Texto)."</p><br>";
            $this->UltimoNumeral=$DatosConsideraciones["Numeral"];
        }
        
        //$html= str_replace("@NumerosContratos", $ContratosActa, $html);
        return($html);
    }
    
    public function ObservacionesActaLiquidacion1($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op1' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));
        $DatosActa= $obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
        $NIT_IPS=$DatosActa["NIT_IPS"];
        $DatosRepresentanteEPS= $obCon->DevuelveValores("eps_representantes_legales", "ID", 1);
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";
        $sql="SELECT t2.Contrato FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente WHERE t1.idActaLiquidacion='$idActaLiquidacion' ORDER BY t1.ID";
        $sql="SELECT 
                (SELECT Contrato FROM contratos t2 WHERE t1.idContrato=t2.ContratoEquivalente AND t2.NitIPSContratada='$NIT_IPS' LIMIT 1) AS Contrato,
                FechaInicial,FechaFinal
                FROM actas_liquidaciones_contratos t1  
                WHERE t1.idActaLiquidacion='$idActaLiquidacion'";
        $Consulta=$obCon->Query($sql);
        $ContratosActa="<strong>";
        while($DatosContratos=$obCon->FetchAssoc($Consulta)){
            $ContratosActa.=$DatosContratos["Contrato"]." del ";
            $ContratosActa.=$DatosContratos["FechaInicial"]." al ";
            $ContratosActa.=$DatosContratos["FechaFinal"].", ";
        }
        $ContratosActa=substr($ContratosActa,0,-2);
        $ContratosActa.="</strong>";
        $html= str_replace("@NombreIPS", $DatosActa["RazonSocialIPS"], $html);
        $html= str_replace("@NumerosContratos", $ContratosActa, $html);
        $html= str_replace("@RepresentanteEPS", $DatosRepresentanteEPS["Nombres"]." ".$DatosRepresentanteEPS["Apellidos"], $html);
        $html= str_replace("@IdentificacionRepresentanteEPS", $DatosRepresentanteEPS["Identificacion"], $html);
        $html= str_replace("@OrigenIdentificacion", $DatosRepresentanteEPS["OrigenIdentificacion"], $html);
        $html= str_replace("@DireccionRepresentanteEPS", $DatosRepresentanteEPS["Direccion"]." ".$DatosRepresentanteEPS["Domicilio"], $html);
        return($html);
    }
    
    public function ObservacionesActaLiquidacion2($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op2' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";        
        return($html);
    }
    
    public function RepresentantesLegalesActaLiquidacion($DatosActa) {
        $html="<h3>REPRESENTANTES LEGALES:</h3>";
        $html.='<table cellspacing="3" cellpadding="2" border="1">
                    <tr>
                        <td colspan="2" style="width:50%;text-align:center;"><h2>E.P.S.</h2></td>
                        <td colspan="2" style="width:50%;text-align:center;"><h2>I.P.S.</h2></td>
                    
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Nombres</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Nombres_Representante_Legal"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Nombres</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Nombres_Representante_Legal"])).'</td>
                        
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Apellidos</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Apellidos_Representante_Legal"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Apellidos</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Apellidos_Representante_Legal"])).'</td>
                        
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Identificación</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Identificacion_Representante_Legal"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Identificación</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Identificacion_Representante_Legal"])).'</td>
                        
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Domicilio</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Domicilio"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Domicilio</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Domicilio"])).'</td>
                        
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Dirección</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Direccion"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Dirección</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Direccion"])).'</td>
                        
                    </tr>
                    
                    <tr>
                        <td style="width:20%;text-align:left;"><h3>Teléfono</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["EPS_Telefono"])).'</td>
                        <td style="width:20%;text-align:left;"><h3>Teléfono</h3></td>
                        <td style="width:30%;text-align:left;">'. utf8_encode(strtoupper($DatosActa["IPS_Telefono"])).'</td>
                        
                    </tr>
                    
                </table>';
            
        return($html);
    }
    
    public function EncabezadoActaLiquidacion($DatosActa) {
        $html="<h3>PARTES CONTRATANTES:</h3><br>";
        $html.='<table cellspacing="3" cellpadding="2" border="1">
                    <tr>
                        <td style="width:20%;"><strong>Contratista IPS:</strong></td>
                    
                        <td style="width:30%;"><strong>'.utf8_encode($DatosActa["RazonSocialIPS"]).'</strong></td>
                    
                        <td style="width:20%;"><strong>NIT IPS:</strong></td>
                    
                        <td style="width:30%;" ><strong>'.$DatosActa["NIT_IPS"].'</strong></td>
                    </tr>
                    <tr>
                        <td style="width:20%;"><strong>Contratante EPS:</strong></td>
                    
                        <td style="width:30%;"><strong>'.$DatosActa["RazonSocialEPS"].'</strong></td>
                    
                        <td style="width:20%;"><strong>NIT EPS:</strong></td>
                    
                        <td style="width:30%;" ><strong>'.$DatosActa["NIT_EPS"].'</strong></td>
                    </tr>
                </table>';
            
        return($html);
    }
    
    public function ContratosActaLiquidacion($idActaLiquidacion,$NIT_IPS) {
        $obCon=new conexion(1);
        $sql="SELECT t1.ID,t1.NombreContrato AS Contrato,t1.FechaInicial as FechaInicioContrato,t1.FechaFinal  as FechaFinalContrato,t1.Valor as ValorContrato
                FROM actas_liquidaciones_contratos t1 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion'";
        
        $Consulta=$obCon->Query($sql);
        $html='<table cellspacing="3" cellpadding="2" border="1">';
            while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                $html.='<tr>';
                    $html.='<td colspan="2" style="text-align:rigth">';
                        $html.='CONTRATO DE PRESTACIÓN DE SERVICIOS NO.';
                    $html.='</td>';
                    $html.='<td>';
                        $html.="<h4>".$DatosContratos["Contrato"]."</h4>";
                    $html.='</td>';
                $html.='</tr>';
                $html.='<tr>';
                    $html.='<td rowspan="2" style="text-align:center">';
                        $html.='<h4>Vigencia Contrato</h4>';
                    $html.='</td>';
                    $html.='<td style="text-align:rigth">';
                        $html.='<h4>Inicial:</h4>';
                    $html.='</td>';
                    $html.='<td>';
                        $html.=$DatosContratos["FechaInicioContrato"];
                    $html.='</td>';
                    
                    
                $html.='</tr>';
                $html.='<tr>';
                    $html.='<td style="text-align:rigth">';
                        $html.='<h4>Final:</h4>';
                    $html.='</td>';
                    $html.='<td>';
                        $html.=$DatosContratos["FechaFinalContrato"];
                    $html.='</td>';
                    
                $html.='</tr>';
                
                $html.='<tr>';
                    $html.='<td style="text-align:rigth" colspan="2">';
                        $html.='<h4>Valor:</h4>';
                    $html.='</td>';
                    $html.='<td>';
                        $html.="<h4>".number_format($DatosContratos["ValorContrato"])."</h4>";
                    $html.='</td>';
                    
                $html.='</tr>';
                    
                
            }
        $html.='</table>';
        return($html);
    }
    
    public function InformeTickets($FechaInicial,$FechaFinal,$CmbEstado,$CmbProyectosTicketsListado,$CmbModulosTicketsListado,$CmbTiposTicketsListado) {
        $obCon=new conexion(1);
        $idFormato=1;
        $fecha=date("Y-m-d");
        $DatosFormatos= $this->obCon->DevuelveValores("formatos_calidad", "ID", $idFormato);
        
        $Documento="$DatosFormatos[Nombre]";
        
        $this->PDF_Ini($DatosFormatos["Nombre"], 9, "",2);
        $this->PDF_Encabezado($fecha,1, $idFormato, "",$Documento);
        
        $html=$this->ResumenInformeTickets($FechaInicial,$FechaFinal,$CmbEstado,$CmbProyectosTicketsListado,$CmbModulosTicketsListado,$CmbTiposTicketsListado);
        $this->PDF->writeHTML("<BR><BR><BR><BR><BR>".$html, true, false, false, false, '');
        $Condicional="";
        if($CmbProyectosTicketsListado>0){
            $Condicional.=" AND t1.idProyecto='$CmbProyectosTicketsListado'";
        }
        if($CmbModulosTicketsListado>0){
            $Condicional.=" AND t1.idModuloProyecto='$CmbModulosTicketsListado'";
        }
        if($CmbTiposTicketsListado>0){
            $Condicional.=" AND t1.TipoTicket='$CmbTiposTicketsListado'";
        }
        if($CmbEstado>0){
            $Condicional.=" AND t1.Estado='$CmbEstado'";
        }
        $sql="SELECT t1.*
            FROM vista_tickets t1 WHERE t1.FechaApertura>='$FechaInicial' AND t1.FechaApertura<='$FechaFinal' $Condicional ORDER BY idProyecto,TipoTicket,idModuloProyecto,Prioridad";
        $Consulta=$obCon->Query($sql);
        while($DatosTickets=$obCon->FetchAssoc($Consulta)){
            $idTicket=$DatosTickets["ID"];
            $Asunto="<BR><h4>Ticket No. $idTicket ".$DatosTickets["Asunto"]."</h4>";
            $Asunto.="<br>Estado: ".$DatosTickets["NombreEstado"];
            $Asunto.="<br>Proyecto: ".$DatosTickets["NombreProyecto"];
            $Asunto.="<br>TipoTicket: ".$DatosTickets["NombreTipoTicket"];
            $Asunto.="<br>Fecha: ".$DatosTickets["FechaApertura"];
            $Asunto.="<br>De: ".$DatosTickets["NombreSolicitante"]." ".$DatosTickets["ApellidoSolicitante"];
            $Asunto.="<br>Para: ".utf8_encode($DatosTickets["NombreAsignado"]." ".$DatosTickets["ApellidoAsignado"]);
            $Asunto.="<BR>";
            $this->PDF->writeHTML($Asunto, true, false, false, false, '');
            $sql="SELECT t1.*,
                 (SELECT Nombre FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser) as NombreUsuario, 
                 (SELECT Apellido FROM usuarios t2 WHERE t2.idUsuarios=t1.idUser) as ApellidoUsuario  
                 FROM tickets_mensajes t1 WHERE t1.idTicket='$idTicket' ";
            $ConsultaRespuestas= $obCon->Query($sql);
            while($DatosMensajes=$obCon->FetchAssoc($ConsultaRespuestas)){
                
                $this->PDF->writeHTML( $DatosMensajes["Created"]."<BR>", true, false, false, false, '');
                $this->PDF->writeHTML( $DatosMensajes["NombreUsuario"]." ".$DatosMensajes["ApellidoUsuario"].":<BR>", true, false, false, false, '');
                $this->PDF->writeHTML( $DatosMensajes["Mensaje"]."<BR><HR>", true, false, false, false, '');
                
            }
            
        }
        
        $this->PDF_Output($DatosFormatos["Nombre"]);
    }
    
    public function ResumenInformeTickets($FechaInicial,$FechaFinal,$CmbEstado,$CmbProyectosTicketsListado,$CmbModulosTicketsListado,$CmbTiposTicketsListado) {
        $obCon=new conexion(1);
        $Condicion="";
        if($CmbEstado>0){
            $Condicion.=" AND Estado='$CmbEstado' ";
        }
        if($CmbProyectosTicketsListado>0){
            $Condicion.=" AND idProyecto='$CmbProyectosTicketsListado' ";
        }
        if($CmbModulosTicketsListado>0){
            $Condicion.=" AND idModuloProyecto='$CmbModulosTicketsListado' ";
        }
        if($CmbTiposTicketsListado>0){
            $Condicion.=" AND TipoTicket='$CmbTiposTicketsListado' ";
        }
        $sql="SELECT COUNT(ID) as Total, NombreProyecto,NombreModulo,NombreTipoTicket,NombreEstado FROM vista_tickets 
              WHERE FechaApertura>='$FechaInicial' AND FechaApertura<='$FechaFinal' $Condicion  
              GROUP BY idProyecto,Estado,TipoTicket,idModuloProyecto  ";
        
        $Consulta=$obCon->Query($sql);
        $html='<table cellspacing="3" cellpadding="2" border="0">';
            $html.='<tr>';
                $html.='<td colspan="5" style="text-align:center">';
                    $html.= "<strong>RESUMEN DE GESTIÓN DE TICKETS DEL $FechaInicial AL $FechaFinal</strong>";
                $html.='</td>';
                
                
            $html.='</tr>';
            
            
            $html.='<tr>';
                
                $html.='<td>';
                    $html.= "<strong>Nombre del Proyecto</strong>";
                $html.='</td>';
                $html.='<td>';
                    $html.= "<strong>Nombre del Modulo</strong>";
                $html.='</td>';
                $html.='<td>';
                    $html.= "<strong>Tipo de Ticket</strong>";
                $html.='</td>';
                $html.='<td>';
                    $html.= "<strong>Estado</strong>";
                $html.='</td>';
                $html.='<td>';
                    $html.= "<strong>Total</strong>";
                $html.='</td>';
                
            $html.='</tr>';
            $h=0;
            $TotalTickets=0;
        while($DatosResumen=$obCon->FetchAssoc($Consulta)){
            $TotalTickets=$TotalTickets+$DatosResumen["Total"];
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            } 
            $html.='<tr>';
                
                $html.='<td style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                    $html.= utf8_encode($DatosResumen["NombreProyecto"]);
                $html.='</td>';
                $html.='<td style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                    $html.= utf8_encode($DatosResumen["NombreModulo"]);
                $html.='</td>';
                $html.='<td style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                    $html.= utf8_encode($DatosResumen["NombreTipoTicket"]);
                $html.='</td>';
                $html.='<td style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                    $html.= utf8_encode($DatosResumen["NombreEstado"]);
                $html.='</td>';
                $html.='<td style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                    $html.= ($DatosResumen["Total"]);
                $html.='</td>';
                
            $html.='</tr>';
        }
        $html.='<tr>';
                
                $html.='<td colspan="4" style="text-align:rigth">';
                    $html.= "<strong>TOTAL DE TICKETS</strong>";
                $html.='</td>';
                $html.='<td >';
                    $html.= number_format($TotalTickets);
                $html.='</td>';
                                
            $html.='</tr>';
        $html.='</table>';
        return($html);
    }
    
   //Fin Clases
}
    