<?php
/**
 * Pagina para crear o editar las diferentes opciones del software
 * 2019-05-20, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="adminCarteraEPS.php";
$myTitulo="Cartera EPS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$DatosUsuario["Role"];

$css->PageInit($myTitulo);
print("<br>");
   $css->section("", "content", "", "");
   $css->CrearDiv("", "col-md-3", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT * FROM ips";
        }else{
            $sql="SELECT r.idIPS as NIT,i.Nombre,i.DataBase FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "onchange=CargarAdminCarteraEPS()", "");
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["DataBase"], "", "");
                    print($DatosIPS["Nombre"].$DatosIPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();    
    print("<br><br><br><br>");
   $css->TabInit();
            $css->TabLabel("TabCuentas1", "<strong >Cartera cargada EPS</strong>", "Tab_1", 1,"onclick=CargarAdminCarteraEPS()");
            $css->TabLabel("TabCuentas2", "<strong >Historial Glosas EPS</strong>", "Tab_2",0,"onclick=HistorialGlosas()");
            $css->TabLabel("TabCuentas3", "<strong >Historial de Pagos ASMET</strong>", "Tab_3",0,"onclick=CargarHistorialPagos()");  
            $css->TabLabel("TabCuentas4", "<strong >Anticipos Cargados</strong>", "Tab_4",0,"onclick=HistorialAnticipos()"); 
            $css->TabLabel("TabCuentas5", "<strong >Historial de Cuentas por Pagar</strong>", "Tab_5",0,"onclick=HistorialNotas()"); 
            $css->TabLabel("TabCuentas6", "<strong >Conciliaciones de la Cartera EPS</strong>", "Tab_6",0,"onclick=HistorialActualizacionesCartera()"); 
            $css->TabLabel("TabCuentas7", "<strong >Historial de Archivos Cargados</strong>", "Tab_7",0,"onclick=HistorialArchivosCargados()"); 
            $css->TabLabel("TabCuentas8", "<strong >Cartera por Edades</strong>", "Tab_8",0,"onclick=HistorialCarteraXEdades()"); 
            $css->TabLabel("TabCuentas10", "<strong >Historial Retenciones</strong>", "Tab_10",0,"onclick=HistorialRetenciones()"); 
            $css->TabLabel("TabCuentas9", "<strong >Cruce Realizado</strong>", "Tab_9",0,"onclick=CruceCartera()"); 
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            
            $css->CrearDiv("DivOpcionesCatCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivCatCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_2");
            
            
        
            $css->CrearDiv("DivOpcionesCatHisCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivCatHisCartera", "", "center", 1, 1);

            $css->CerrarDiv();
        
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_3");
            
             
            $css->CrearDiv("DivOpcionesSRAsmet", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivSRAsmet", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_4");
                         
            $css->CrearDiv("DivOpcionesControlCargue", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivControlCargue", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_5");
                         
            $css->CrearDiv("DivOpcionesHistorialNotas", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialNotas", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_6");
                         
            $css->CrearDiv("DivOpcionesActualizacion", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialActualizacion", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_7");
                         
            $css->CrearDiv("DivOpcionesTab7", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialTab7", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_8");
                         
            $css->CrearDiv("DivOpcionesTab8", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialTab8", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_9");
                         
            $css->CrearDiv("DivOpcionesTab9", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialTab9", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_10");
                         
            $css->CrearDiv("DivOpcionesTab10", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivHistorialTab10", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
    $css->Csection();
$css->PageFin();

print('<script src="jsPages/adminCarteraEPS.js"></script>');  //script propio de la pagina


$css->Cbody();
$css->Chtml();

?>