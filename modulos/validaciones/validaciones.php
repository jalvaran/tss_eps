<?php
/**
 * Pagina para Realizar las validaciones de las facturas
 * 2019-05-28, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="validaciones.php";
$myTitulo="Validacion de Facturas";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$_SESSION["Role"];

$css->PageInit($myTitulo);

    $css->Modal("ModalAcciones", "TS5", "", 1);
    
        
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();    
            
      
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    
        
    print("<br>");
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
    
            
        if($Role=="SUPERVISOR"){
            $sql="SELECT * FROM ips";
        }else{
            $sql="SELECT * FROM ips i INNER JOIN relacion_usuarios_ips ri ON ri.idIPS=i.NIT WHERE ri.idUsuario='$idUser' ";
        }
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "onchange=MuestreFacturasNRIPS()", "");
            $Consulta=$obCon->Query($sql);
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["NIT"], "", "");
                    print($DatosIPS["Nombre"]." ".$DatosIPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        $sql="SELECT * FROM eps";
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbEPS", "form-control", "CmbEPS", "EPS", "", "", "");
            while($DatosEPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosEPS["NIT"], "", "");
                    print($DatosEPS["Nombre"]." ".$DatosEPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        print('<br><div class="input-group">');
        $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar Factura o Contrato", "", "", "onchange=BuscarFactura()");


         print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
              </div>');
    $css->CerrarDiv();
    
    $css->CrearDiv("DivProgress", "col-md-12", "center", 1, 1);
        print("<br><br><br>");
        $css->ProgressBar("PgProgresoUp", "LyProgresoUP", "", 0, 0, 100, 0, "0%", "", "");
    $css->CerrarDiv();
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    print("<br><br><br><br>");
    $css->CrearDiv("", "col-md-12", "center", 1, 1);   
        
        $css->TabInit();
            $css->TabLabel("TabCuentas1", "<strong >Facturas sin Relación IPS</strong>", "Tab_1", 1,"onclick=MuestreFacturasNRIPS()");
            $css->TabLabel("TabCuentas2", "<strong >Facturas sin Relación EPS</strong>", "Tab_2",0,"onclick=FacturasSinRelacionarPorIPS()");
            $css->TabLabel("TabCuentas3", "<strong >Facturas Pagadas Sin Relación</strong>", "Tab_3",0,"onclick=MuestrePagadasSR()"); 
            $css->TabLabel("TabCuentas6", "<strong >Facturas a Favor de EPS</strong>", "Tab_6",0,"onclick=FacturasAFavor()");  
            $css->TabLabel("TabCuentas4", "<strong >Cruce de Cartera</strong>", "Tab_4",0,"onclick=MuestreCruce()"); 
            $css->TabLabel("TabCuentas7", "<strong >Historial de Conciliaciones</strong>", "Tab_7",0,"onclick=MuestreConciliaciones()"); 
            $css->TabLabel("TabCuentas8", "<strong >Actas de Conciliaciones</strong>", "Tab_8",0,"onclick=ActasConciliaciones()"); 
            
            $css->TabLabel("TabCuentas5", "<strong >Informe Consolidado</strong>", "Tab_5",0,"onclick=MuestreConsolidado()"); 
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            /*
             * Contenido de Facturas sin relacion IPS
             * Son las facturas que las tiene la IPS pero no la EPS
             */
            $css->CrearDiv("DivFacturasIPS", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_2");
            
            /*
             * Contenido de Facturas sin relacion EPS
             * Son las facturas que las tiene la EPS pero no la IPS
             */
        
            $css->CrearDiv("DivFacturasEPS", "", "center", 1, 1);

            $css->CerrarDiv();
        
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_3");
            /*
             * Contenido de Cruce de cartera Factura por factura
             * 
             */
             
            $css->CrearDiv("DivPagasSinRelacion", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_4");
            /*
             * Contenido de Cruce de cartera Factura por factura
             * 
             */
             
            $css->CrearDiv("DivCruce", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_5");
            
             
            $css->CrearDiv("DivTab5", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_6", 1);
           
            $css->CrearDiv("DivTab6", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_8", 1);
            
            $css->CrearDiv("DivOpcionesActasConciliacion", "col-md-12", "left", 0, 1);
                $css->CrearDiv("", "col-md-3", "", 1, 1);                
                    $css->CrearBotonEvento("BntNuevaActaConciliacion", "Nueva Acta", 1, "onclick", "AbreModalNuevaActaConciliacion()", "verde", "");
                $css->Cdiv();
                $css->CrearDiv("DivSelectActas", "col-md-3", "", 1, 1); 
                    
                $css->Cdiv();
            $css->Cdiv();
            
            print("<br><br><br><br>");
                           
            $css->CrearDiv("DivTab8", "", "center", 1, 1);
                 
            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_7", 1);
           
            $css->CrearDiv("DivTab7", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        
        
        
    $css->CerrarDiv();
    print("<br><br><br><br><br><br><br><br><br><br>");
$css->PageFin();
print('<script src="jsPages/validaciones.js"></script>');  //script propio de la pagina
$css->AddJSExcel();

$css->Cbody();
$css->Chtml();

?>