<?php
ini_set("display_errors","On");
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

if(isset($_REQUEST["idAccion"])){
    include_once '../../../modelo/php_conexion.php';
    include_once '../clases/facturacion_electronica.class.php';
    $obCon = new Factura_Electronica($idUser);
    
    switch ($_REQUEST["idAccion"]) {

        case 1://Emitir Comprobante
            $idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
            $WebService=$obCon->DevuelveValores("fe_webservice", "ID", 1); //Tabla que aloja la direccion del web service
            $client = new SoapClient($WebService["DireccionWebService"]);
            $param=$obCon->ConstruyaLayoutEmitirFactura($WebService["User"],$WebService["Pass"],$idFactura); 
            
            
           
            print("<pre>");
            print_r($param);
            print("</pre>");
             
            // Call RemoteFunction () 
            $error = 0; 
            try { 
                $result= $client->__call("EmitirComprobante", array($param));
                $EmitirComprobanteResult=$result->EmitirComprobanteResult;
	
                $XMLFiscalValido=$EmitirComprobanteResult->XMLFiscalValido;
                $fileName=$EmitirComprobanteResult->fileName;
                $documentNumber=$EmitirComprobanteResult->documentNumber;
                $transactionId=$EmitirComprobanteResult->ID;
                $msgError=$EmitirComprobanteResult->MensajeErrorLAYOUT;

                //document status
                $processName=$EmitirComprobanteResult->MensajeDocumentStatus->processName;
                $processStatus=$EmitirComprobanteResult->MensajeDocumentStatus->processStatus;
                $processDate=$EmitirComprobanteResult->MensajeDocumentStatus->processDate;
                $messageType=$EmitirComprobanteResult->MensajeDocumentStatus->messageType;
                $errorMessage=$EmitirComprobanteResult->MensajeDocumentStatus->errorMessage;
                $businessStatus=$EmitirComprobanteResult->MensajeDocumentStatus->businessStatus;

                //get CUFE
                $Status=$EmitirComprobanteResult->MensajeRespuestaCUFE->Status;
                $CUFE=$EmitirComprobanteResult->MensajeRespuestaCUFE->CUFE;
                
                if(!empty($XMLFiscalValido)){
                    $Datos["idFactura"]=$idFactura;
                    $Datos["XmlFiscal"]=str_replace( "'" , "" , $XMLFiscalValido);
                    $Datos["NumeroDocumento"]=$documentNumber;
                    $Datos["idDocumento"]=$transactionId;
                    $Datos["EstadoCUFE"]=$Status;
                    $Datos["CUFE"]=$CUFE;
                    $sql=$obCon->getSQLInsert("facturas_electronicas", $Datos);
                    $obCon->Query($sql);
                    $myfilexmlResponse = fopen("filename.xml", "w");
                    fwrite($myfilexmlResponse, $XMLFiscalValido);
                    fclose($myfilexmlResponse);
                    echo 'OK';

                }
                else{
                    
                    echo $errorMessage;
                }

                //var_dump($result);

                
            } catch (SoapFault $fault) { 
                $error = 1; 
                print(" 
                alert('Sorry, blah returned the following ERROR: ".$fault->faultcode."-".$fault->faultstring.". We will now take you back to our home page.'); 
                window.location = 'main.php'; 
                "); 
            } 
            
            catch (Exception $e){
                echo 'Error: '.$e->getMessage();
            }
            
            break;

        }
}else{
    print("No se recibieron parametros");
}
?>