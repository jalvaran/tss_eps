<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel general
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class F10_Excel extends conexion{
    
    // Clase para exportar el listado de f10
    
    public function f10_excel_listado($st_f10) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $obCon=new conexion(1);
        $objPHPExcel = new Spreadsheet();
        
        $styleName = [
        
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                    "N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                    "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL",
                    "AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
                    "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL",
                    "BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
                    "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL",
                    "CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",
                ];
        
        $nombres_campos=["ID"=>'IDENTIFICADOR',
				"NumeroInterno"=>'N°',
				"NombreSucursal"=>'SEDE',
				"NitIPSContratada"=>'NIT',
				"RazonSocial"=>'RAZON SOCIAL',
				"Naturaleza"=>'NATURALEZA',
				"Modalidad"=>'MODALIDAD',
				"NumeroContrato"=>'NUMERO DE CONTRATO',
				"llaveCargue"=>'LLAVE',
				"ValorContrato"=>'VALOR CONTRATO',
				"FechaInicioContrato"=>'FECHA INICIO VIGENCIA',
				"FechaFinalContrato"=>'FECHA FIN VIGENCIA',
				"ValorGlosaxConciliar"=>'VALOR GLOSAS POR CONCILIAR',
				"FechaConciliacionGlosa"=>'FECHA CONCILIACION GLOSAS',
				"CumplimientoActaGlosas"=>'CUMPLIMIENTO DE ACTA DE GLOSA',
				"ResponsableConciliacionGlosa"=>'RESPONSABLE CONCILIACIÓN GLOSAS',
				"SaldoCuentaXPagar"=>'SALDO CUENTA POR PAGAR (SEVEN)',
				"FechaConciliacionCartera"=>'FECHA DE CONCILACIÓN CARTERA',
				"nombre_responsable_conciliacion"=>'RESPONSABLE CONCILIACIÓN CARTERA',
				"CumplimientoConciliacionCartera"=>'CUMPLIMIENTO DE CONCILIACIÓN',
				"ObservacionesCartera"=>'OBSERVACIONES',
				"FechaActaLiquidacion"=>'FECHA ELABORACIÓN ACTA DE LIQUIDACIÓN',
				"NumeroActaLiquidacion"=>'No. ACTA DE LIQUIDACION',
				"ActaLiquidacionFirmada"=>'ACTA DE LIQUIDACIÓN FIRMADA',
				"FechaActaLiquidacionFirmada"=>'FECHA ACTA DE LIQUIDACIÓN FIRMADA',
				"ValorFavorContra"=>'VALOR A FAVOR (/) O EN CONTRA (+)',
				"RegistroActaLiquidacionSeven"=>'REGISTRO ACTA DE LIQUIDACIÓN (SEVEN)',
				"AcuerdoPago"=>'SE REALIZÓ ACUERDO DE PAGO',
				"NumeroCuotasAcuerdo"=>'No. CUOTAS',
				"ValorTotalAcuerdo"=>'VALOR TOTAL ACUERDO',
				"ValorCuotaAcuerdo"=>'VALOR CUOTA',
				"FechaInicioAcuerdo"=>'FECHA INICIO ACUERDO DE PAGO',
				"FechaFinAcuerdo"=>'FECHA FIN ACUERDO DE PAGO',
				"ValorSaldoAcuerdo"=>'VALOR SALDO ACUERDO',
				"ResponsableCargue"=>'RESPONSABLE AGS O DEPARTAMENTAL CARGUE',
				"TipoOperacionCargue"=>'TIPO DE OPERACIÓN',
				"NumeroAjusteCargue"=>'NÚMERO AJUSTE',
				"FechaAjusteCargue"=>'FECHA',
				"ValorAjusteCargue"=>'VALOR AJUSTE',
				"SaldoActaLiquidacionCargue"=>'SALDO ACTA DE LIQUIDACIÓN',
				"NotificacionCargue"=>'NOTIFICACIÓN',
				"FechaNotificacionCargue"=>'FECHA NOTIFICACIÓN',
				"UsuarioCargue"=>'USUARIO QUE CARGA',
				"nombre_responsable_liquidacion"=>'RESPONSABLE AGS 2',
				"nombre_cargo_responsable_liquidacion"=>'CARGO RESPONSABLE AGS 2',
				"LiderAcargoLiquidacion"=>'LIDER A CARGO',
				"nombre_responsable_cargue_acta"=>'RESPONSABLE CARGUE DE ACTA',
				"Pareto"=>'PARETO',
				"ParetoContraloria"=>'PARETO CONTRALORIA',
				"Municipio"=>'MUNICIPIO',
				"NivelComplejidad"=>'NIVEL COMPLEJIDAD',
				"ObjetoContrato"=>'OBJETO DEL CONTRATO',
				"AnioFinalizacionContrato"=>'AÑO FINALIZACIÓN',
				"ObservacionesLiquidacion"=>'OBSERVACIONES ACTAS DE LIQUIDACIÓN',
				"SaldoInicialSeven"=>'SALDO INICIAL (SEVEN)',
				"GlosaInicial"=>'GLOSA INICIAL',
				"GlosaFavor"=>'GLOSA A FAVOR',
				"GlosaConciliar"=>'GLOSA POR CONCILIAR',
				"PendienteAuditoria"=>'PENDIENTE DE AUDITORIA',
				"ValorDevoluciones"=>'VALOR DEVOLUCIONES',
				"ValorFacturado"=>'VALOR FACTURADO',
				"ValorPagado"=>'TOTAL PAGADO',
				"FechaEnvioCruceCartera"=>'FECHA ENVIO CRUCE DE CARTERA',
				"DiasTranscurridos"=>'DIAS TRANSCURRIDOS',
				"ConciliadoAsmet"=>'CONCILIADO POR ASMET',
				"CausaNoLiquidacion"=>'CAUSA DE NO LIQUIDACION DEL CONTRATO',
				"GestionLiquidacion"=>'GESTION PARA LA LIQUIDACION',
				"ProcesoNoLiquidacion"=>'PROCESO DE NO LIQUIDACION INMEDIATA',
				"ObservacionesAdicionales"=>'OBSERVACIONES',
				"HYL"=>'HYL',
				"Liquidado31Marzo2018"=>'LIQUIDADO A 31 DE MARZO 2018',
				"Contrato"=>'CONTRATO SI/NO',
				"MarcaGerencia"=>'MARCA JAIR',
				"RecibeCartera"=>'SE RECIBE CARTERA',
				"FechaRecibeCartera"=>'FECHA RECIBE CARTERA',
				"RealizaCruceCartera"=>'SE REALIZA CRUCE DE CARTERA',
				"FechaCruce"=>'FECHA REALIZA CRUCE DE CARTERA',
				"EnviaOferta"=>'SE ENVIA OFERTA',
				"FechaEnviaOferta"=>'FECHA ENVIA OFERTA',
				"ActaArchivada"=>'FECHA ENVIA OFERTA',
				"FechaActualizacionManual"=>'FECHA ACTUALIZACION MANUAL',
				"usuario_edicion"=>'USUARIO ACTUALIZA MANUAL',
				"FechaActualizacion"=>'FECHA ACTUALIZACION AUTOMATICA',
				"nombre_estado"=>'ESTADO DEL CONTRATO',

				];
        
        $campos_seleccionados="";
        $z=0;
        $i=1;
        foreach ($nombres_campos as $key => $value) {
            $campos_seleccionados.="$key,";
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($Campos[$z].$i,$value);
            
            $z=$z+1;
        }
        $campos_seleccionados= substr($campos_seleccionados, 0, -1);
        $st_f10= str_replace("*", $campos_seleccionados, $st_f10);
        $z=0;
        $i=2;
        $sql=$st_f10;
        
        $Consulta=$obCon->Query($sql);
        
        while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
            $z=0;
            foreach ($DatosConsulta as $key => $value) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($Campos[$z++].$i,$value);
            }
            $i++;
        }
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("F10")
        ->setSubject("F10")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("F10");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."F10".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    // Clase para exportar el control de cambios del f10
    
    public function f10_control_cambios_excel($contrato_id) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $obCon=new conexion(1);
        $st_f10="SELECT * FROM vista_f10_control_cambios WHERE contrato_id='$contrato_id'";
        $objPHPExcel = new Spreadsheet();
        
        $styleName = [
        
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
                
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                    "N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                    "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL",
                    "AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
                    "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL",
                    "BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
                    "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL",
                    "CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",
                ];
        
        $nombres_campos=["ID"=>'IDENTIFICADOR',
				"NumeroInterno"=>'N°',
				"NombreSucursal"=>'SEDE',
				"NitIPSContratada"=>'NIT',
				"RazonSocial"=>'RAZON SOCIAL',
				"Naturaleza"=>'NATURALEZA',
				"Modalidad"=>'MODALIDAD',
				"NumeroContrato"=>'NUMERO DE CONTRATO',
				"llaveCargue"=>'LLAVE',
				"ValorContrato"=>'VALOR CONTRATO',
				"FechaInicioContrato"=>'FECHA INICIO VIGENCIA',
				"FechaFinalContrato"=>'FECHA FIN VIGENCIA',
				"ValorGlosaxConciliar"=>'VALOR GLOSAS POR CONCILIAR',
				"FechaConciliacionGlosa"=>'FECHA CONCILIACION GLOSAS',
				"CumplimientoActaGlosas"=>'CUMPLIMIENTO DE ACTA DE GLOSA',
				"ResponsableConciliacionGlosa"=>'RESPONSABLE CONCILIACIÓN GLOSAS',
				"SaldoCuentaXPagar"=>'SALDO CUENTA POR PAGAR (SEVEN)',
				"FechaConciliacionCartera"=>'FECHA DE CONCILACIÓN CARTERA',
				"nombre_responsable_conciliacion"=>'RESPONSABLE CONCILIACIÓN CARTERA',
				"CumplimientoConciliacionCartera"=>'CUMPLIMIENTO DE CONCILIACIÓN',
				"ObservacionesCartera"=>'OBSERVACIONES',
				"FechaActaLiquidacion"=>'FECHA ELABORACIÓN ACTA DE LIQUIDACIÓN',
				"NumeroActaLiquidacion"=>'No. ACTA DE LIQUIDACION',
				"ActaLiquidacionFirmada"=>'ACTA DE LIQUIDACIÓN FIRMADA',
				"FechaActaLiquidacionFirmada"=>'FECHA ACTA DE LIQUIDACIÓN FIRMADA',
				"ValorFavorContra"=>'VALOR A FAVOR (/) O EN CONTRA (+)',
				"RegistroActaLiquidacionSeven"=>'REGISTRO ACTA DE LIQUIDACIÓN (SEVEN)',
				"AcuerdoPago"=>'SE REALIZÓ ACUERDO DE PAGO',
				"NumeroCuotasAcuerdo"=>'No. CUOTAS',
				"ValorTotalAcuerdo"=>'VALOR TOTAL ACUERDO',
				"ValorCuotaAcuerdo"=>'VALOR CUOTA',
				"FechaInicioAcuerdo"=>'FECHA INICIO ACUERDO DE PAGO',
				"FechaFinAcuerdo"=>'FECHA FIN ACUERDO DE PAGO',
				"ValorSaldoAcuerdo"=>'VALOR SALDO ACUERDO',
				"ResponsableCargue"=>'RESPONSABLE AGS O DEPARTAMENTAL CARGUE',
				"TipoOperacionCargue"=>'TIPO DE OPERACIÓN',
				"NumeroAjusteCargue"=>'NÚMERO AJUSTE',
				"FechaAjusteCargue"=>'FECHA',
				"ValorAjusteCargue"=>'VALOR AJUSTE',
				"SaldoActaLiquidacionCargue"=>'SALDO ACTA DE LIQUIDACIÓN',
				"NotificacionCargue"=>'NOTIFICACIÓN',
				"FechaNotificacionCargue"=>'FECHA NOTIFICACIÓN',
				"UsuarioCargue"=>'USUARIO QUE CARGA',
				"nombre_responsable_liquidacion"=>'RESPONSABLE AGS 2',
				"nombre_cargo_responsable_liquidacion"=>'CARGO RESPONSABLE AGS 2',
				"LiderAcargoLiquidacion"=>'LIDER A CARGO',
				"nombre_responsable_cargue_acta"=>'RESPONSABLE CARGUE DE ACTA',
				"Pareto"=>'PARETO',
				"ParetoContraloria"=>'PARETO CONTRALORIA',
				"Municipio"=>'MUNICIPIO',
				"NivelComplejidad"=>'NIVEL COMPLEJIDAD',
				"ObjetoContrato"=>'OBJETO DEL CONTRATO',
				"AnioFinalizacionContrato"=>'AÑO FINALIZACIÓN',
				"ObservacionesLiquidacion"=>'OBSERVACIONES ACTAS DE LIQUIDACIÓN',
				"SaldoInicialSeven"=>'SALDO INICIAL (SEVEN)',
				"GlosaInicial"=>'GLOSA INICIAL',
				"GlosaFavor"=>'GLOSA A FAVOR',
				"GlosaConciliar"=>'GLOSA POR CONCILIAR',
				"PendienteAuditoria"=>'PENDIENTE DE AUDITORIA',
				"ValorDevoluciones"=>'VALOR DEVOLUCIONES',
				"ValorFacturado"=>'VALOR FACTURADO',
				"ValorPagado"=>'TOTAL PAGADO',
				"FechaEnvioCruceCartera"=>'FECHA ENVIO CRUCE DE CARTERA',
				"DiasTranscurridos"=>'DIAS TRANSCURRIDOS',
				"ConciliadoAsmet"=>'CONCILIADO POR ASMET',
				"CausaNoLiquidacion"=>'CAUSA DE NO LIQUIDACION DEL CONTRATO',
				"GestionLiquidacion"=>'GESTION PARA LA LIQUIDACION',
				"ProcesoNoLiquidacion"=>'PROCESO DE NO LIQUIDACION INMEDIATA',
				"ObservacionesAdicionales"=>'OBSERVACIONES',
				"HYL"=>'HYL',
				"Liquidado31Marzo2018"=>'LIQUIDADO A 31 DE MARZO 2018',
				"Contrato"=>'CONTRATO SI/NO',
				"MarcaGerencia"=>'MARCA JAIR',
				"RecibeCartera"=>'SE RECIBE CARTERA',
				"FechaRecibeCartera"=>'FECHA RECIBE CARTERA',
				"RealizaCruceCartera"=>'SE REALIZA CRUCE DE CARTERA',
				"FechaCruce"=>'FECHA REALIZA CRUCE DE CARTERA',
				"EnviaOferta"=>'SE ENVIA OFERTA',
				"FechaEnviaOferta"=>'FECHA ENVIA OFERTA',
				"ActaArchivada"=>'FECHA ENVIA OFERTA',
				"FechaActualizacionManual"=>'FECHA ACTUALIZACION MANUAL',
				"usuario_edicion"=>'USUARIO ACTUALIZA MANUAL',
				"FechaActualizacion"=>'FECHA ACTUALIZACION AUTOMATICA',
				"nombre_estado"=>'ESTADO DEL CONTRATO',

				];
        
        $campos_seleccionados="";
        $z=0;
        $i=1;
        foreach ($nombres_campos as $key => $value) {
            $campos_seleccionados.="$key,";
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($Campos[$z].$i,$value);
            
            $z=$z+1;
        }
        $campos_seleccionados= substr($campos_seleccionados, 0, -1);
        
        $st_f10= str_replace("*", $campos_seleccionados, $st_f10);
        $z=0;
        $i=2;
        $sql=$st_f10;
        
        $Consulta=$obCon->Query($sql);
        
        while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
            $z=0;
            foreach ($DatosConsulta as $key => $value) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($Campos[$z++].$i,$value);
            }
            $i++;
        }
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("F10_control_cambios")
        ->setSubject("F10_control_cambios")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("F10");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."F10_Control_Cambios".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    
    
   //Fin Clases
}
    