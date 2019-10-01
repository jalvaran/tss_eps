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
$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr border="1">
        <td rowspan="3" border="1" style="text-align: center;"><img src="$RutaLogo" style="width:110px;height:60px;"></td>
        
        <td rowspan="3" width="290px" style="text-align: center; vertical-align: center;"><h2><br>$DatosFormatoCalidad[Nombre]</h2></td>
        <td width="70px" style="text-align: center;">Versión<br></td>
        <td width="130px"> $DatosFormatoCalidad[Version]</td>
    </tr>
    <tr>
    	
    	<td style="text-align: center;" >Código<br></td>
        <td> $DatosFormatoCalidad[Codigo]</td>
        
    </tr>
    <tr>
       <td style="text-align: center;" >Fecha<br></td>
       <td style="font-size:6px;"> $DatosFormatoCalidad[Fecha]</td> 
    </tr>
</table>
EOD;
$this->PDF->writeHTML($tbl, true, false, false, false, '');
$this->PDF->SetFillColor(255, 255, 255);
$txt="<h3>".$DatosEmpresaPro["RazonSocial"]."<br>NIT ".$DatosEmpresaPro["NIT"]."</h3>";
$this->PDF->MultiCell(62, 5, $txt, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');
$txt=$DatosEmpresaPro["Direccion"]."<br>".$DatosEmpresaPro["Telefono"]."<br>".$DatosEmpresaPro["Ciudad"]."<br>".$DatosEmpresaPro["WEB"];
$this->PDF->MultiCell(62, 5, $txt, 0, 'C', 1, 0, '', '', true,0, true, true, 10, 'M');
$Documento="<strong>$NumeracionDocumento</strong><br><h5>Impreso por TS5, Techno Soluciones SAS <BR>NIT 900.833.180 3177740609</h5><br>";
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
    
    public function ActaLiquidacionPDF($idActaLiquidacion) {
        
        $DatosActa=$this->obCon->DevuelveValores("actas_liquidaciones","ID",$idActaLiquidacion);
        $DatosTipoActa=$this->obCon->DevuelveValores("actas_liquidaciones_tipo","ID",$DatosActa["TipoActaLiquidacion"]);     
        $NIT_IPS=$DatosActa["NIT_IPS"];
        $TipoActa=$DatosActa["TipoActaLiquidacion"];
        $this->PDF_IniActaLiquidacion("ACTA DE LIQUIDACIÓN No.", utf8_encode($DatosTipoActa["Header"]), "Footer text");
        
        
        $Titulo='<p align="center"><h3>ACTA DE LIQUIDACIÓN No. '.utf8_encode($DatosActa["IdentificadorActaEPS"].'</h3></p>');
        $Titulo.="<br>";
        $Titulo.='<p align="center"><h3>'.utf8_encode($DatosTipoActa["Titulo"]).'</h3></p>';
        $this->PDF->writeHTML($Titulo, true, false, false, false, '');
        
        $html= $this->EncabezadoActaLiquidacion($DatosActa);
        $this->PDF->writeHTML($html, true, false, false, false, '');
        
        $html= $this->ContratosActaLiquidacion($idActaLiquidacion,$NIT_IPS);
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->RepresentantesLegalesActaLiquidacion($DatosActa);
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion1($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->ConsideracionesActa($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion2($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->TotalesActaLiquidacion($DatosActa,$TipoActa);
        $this->PDF->writeHTML("<br><br>".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion3($idActaLiquidacion,$TipoActa,$DatosActa);        
        $this->PDF->writeHTML("".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion4($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->ObservacionesActaLiquidacion5($idActaLiquidacion,$TipoActa);        
        $this->PDF->writeHTML("<br>".$html, true, false, false, false, '');
        
        $html= $this->FirmasActaLiquidacion($DatosActa);        
        $this->PDF->writeHTML("<br><br><br>".$html, true, false, false, false, '');
        /*
        
        $html=$this->FirmasActaConciliacion($DatosActa);
        $this->PDF->writeHTML("<hr>".$html."", true, false, false, false, '');
        
         * 
         */
        $this->PDF_Output("Acta_Liquidacion_$idActaLiquidacion");
    }
    
    public function TotalesActaLiquidacion($DatosActa,$TipoActa) {
        $SaldoAPagarContratista=0;
        $SaldoAPagarContratante=0;
        if($DatosActa["Saldo"]>0){
            $SaldoAPagarContratista=$DatosActa["Saldo"];
        }else{
            $SaldoAPagarContratante=$DatosActa["Saldo"];
        }
        $html="";
        if($TipoActa==1 or $TipoActa==7 or $TipoActa==9){
            $html='<table cellspacing="3" cellpadding="2" border="1">
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
                        <tr>
                            <td colspan="4" style="text-align:left;"><strong>En razón de lo anterior, la presente liquidación generó un saldo a pagar al CONTRATISTA DE $</strong></td>
                            <td style="text-align:rigth;">'. number_format($SaldoAPagarContratista).'</td>

                        </tr>
                        <tr>
                            <td  colspan="4" style="text-align:left;"><strong>En razón de lo anterior, la presente liquidación generó un saldo a favor del CONTRATANTE DE $</strong></td>
                            <td style="text-align:rigth;">'. number_format($SaldoAPagarContratante).'</td>

                        </tr>

                    </table>';
        }
        
        if($TipoActa==4){
            $html='<table cellspacing="3" cellpadding="2" border="1">
                        <tr>
                            <td style="text-align:center;"><strong>VALOR FACTURADO</strong></td>
                            <td style="text-align:center;"><strong>RETENCION IMPUESTOS</strong></td>
                            <td style="text-align:center;"><strong>Descuento o Reconocimiento por BDUA</strong></td>
                            <td style="text-align:center;"><strong>DESCUENTOS CONCILIADO A FAVOR ASMET</strong></td>
                            <td style="text-align:center;"><strong>VALOR PAGADO</strong></td>
                            <td style="text-align:center;"><strong>SALDO</strong></td>

                        </tr>

                        <tr>
                            <td style="text-align:rigth;">'. number_format($DatosActa["ValorFacturado"]).'</td>
                            <td style="text-align:rigth;">'. number_format($DatosActa["RetencionImpuestos"]).'</td>
                            <td style="text-align:rigth;">'. number_format($DatosActa["DescuentoBDUA"]).'</td>
                            <td style="text-align:rigth;">'. number_format($DatosActa["GlosaFavor"]).'</td>
                            <td style="text-align:rigth;">'. number_format($DatosActa["ValorPagado"]).'</td>
                            <td style="text-align:rigth;">'. number_format($DatosActa["Saldo"]).'</td>    

                        </tr>
                        <tr>
                            <td colspan="6">

                            </td>
                        </tr>
                        
                        <tr>
                            <td colspan="5" style="text-align:left;"><strong>En razón de lo anterior, la presente liquidación generó un saldo a pagar al CONTRATISTA DE $</strong></td>
                            <td style="text-align:rigth;">'. number_format($SaldoAPagarContratista).'</td>

                        </tr>
                        <tr>
                            <td  colspan="5" style="text-align:left;"><strong>En razón de lo anterior, la presente liquidación generó un saldo a favor del CONTRATANTE DE $</strong></td>
                            <td style="text-align:rigth;">'. number_format($SaldoAPagarContratante).'</td>

                        </tr>

                    </table>';
        }
            
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
        $html=('<p align="justify">Para constancia se firma en <strong>'.($DatosActa["CiudadFirma"])."</strong>");
        $html.=(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]),  en dos Originales uno para la IPS y otro para la EPS:<br><br><br><br><br></p>");
        
        $sql="SELECT * FROM actas_liquidaciones_firmas WHERE idActaLiquidacion='$idActaLiquidacion'";
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
    
    public function ObservacionesActaLiquidacion5($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op5' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";        
        return($html);
    }
    public function ObservacionesActaLiquidacion4($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op4' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";        
        return($html);
    }
    
    public function ObservacionesActaLiquidacion3($idActaLiquidacion,$TipoActa,$DatosActa) {
        $obCon=new conexion(1);
        $obNumLetra=new numeros_letras();
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op3' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";
        $SaldoEnLetras=$obNumLetra->convertir($DatosActa["Saldo"]);
        $SaldoEnLetras.=" PESOS ($ ".number_format($DatosActa["Saldo"]).")";
        
        $html= str_replace("@ValorLetras",strtoupper("<strong>".$SaldoEnLetras."</strong>"), $html);
        $sql="SELECT t2.* FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente WHERE t1.idActaLiquidacion='$idActaLiquidacion' ORDER BY t1.ID";
        $Consulta=$obCon->Query($sql);
        $ContratosActa="";
        while($DatosContratos=$obCon->FetchAssoc($Consulta)){
            $FechaInicial=explode("-",$DatosContratos["FechaInicioContrato"]);             
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
        $ContratosActa=substr($ContratosActa,0,-2);
        
        $html= str_replace("@Numerocontratos", $ContratosActa, $html);
        return($html);
    }
    
    public function ConsideracionesActa($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND SUBSTRING(Numeral,1,2)<>'op' ORDER BY ID";
        
        $Consulta=$obCon->Query($sql);
        $html="";
        while($DatosConsideraciones=$obCon->FetchAssoc($Consulta)){
            $html.='<p align="justify"><strong>'.($DatosConsideraciones["Numeral"])."</strong> ".utf8_encode($DatosConsideraciones["Texto"])."</p><br>";
            
        }
        
        //$html= str_replace("@NumerosContratos", $ContratosActa, $html);
        return($html);
    }
    
    public function ObservacionesActaLiquidacion1($idActaLiquidacion,$TipoActa) {
        $obCon=new conexion(1);
        $sql="SELECT * FROM actas_liquidaciones_consideraciones WHERE TipoActaLiquidacion='$TipoActa' AND Numeral='op1' LIMIT 1";
        $DatosConsideraciones=$obCon->FetchAssoc($obCon->Query($sql));
        
        $html='<p align="justify">'. utf8_encode($DatosConsideraciones["Texto"])."</p>";
        $sql="SELECT t2.Contrato FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente WHERE t1.idActaLiquidacion='$idActaLiquidacion' ORDER BY t1.ID";
        $Consulta=$obCon->Query($sql);
        $ContratosActa="<strong>";
        while($DatosContratos=$obCon->FetchAssoc($Consulta)){
            $ContratosActa.=$DatosContratos["Contrato"].", ";
        }
        $ContratosActa=substr($ContratosActa,0,-2);
        $ContratosActa.="</strong>";
        $html= str_replace("@NumerosContratos", $ContratosActa, $html);
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
                    
                        <td style="width:30%;"><strong>'.$DatosActa["RazonSocialIPS"].'</strong></td>
                    
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
        $sql="SELECT t1.ID,t2.Contrato,t2.FechaInicioContrato,t2.FechaFinalContrato,t2.ValorContrato
                FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente 
                WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$NIT_IPS'";
        $Consulta=$obCon->Query($sql);
        $html='<table cellspacing="3" cellpadding="2" border="1">';
            while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                $html.='<tr>';
                    $html.='<td colspan="2" style="text-align:rigth">';
                        $html.='PRESTACIÓN DE SERVICIOS NO.';
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
    
   //Fin Clases
}
    