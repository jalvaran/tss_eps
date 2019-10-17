<?php
/**
 * Pagina para Subir los egresos
 * 2019-06-18, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="cargar_contrato_liquidado.php";
$myTitulo="Cargar Contratos Liquidados";
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
    
    $css->CrearDiv("", "col-md-12", "left", 1, 1); 
        $css->h3("", "", "", "");
                print("<strong>Cargar Contratos Liquidados</strong>");
        $css->Ch3();
    $css->CerrarDiv(); 
    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT NIT as idIPS, Nombre FROM ips";
        }else{
            $sql="SELECT r.idIPS,i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "", "");
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["idIPS"], "", "");
                    print($DatosIPS["Nombre"]." ".$DatosIPS["idIPS"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        $sql="SELECT * FROM eps ORDER BY ID";
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
        print("<strong>Seleccione el Contrato:</strong><br>");
        $css->input("file", "UpCartera", "form-control", "UpCartera", "", "", "", "", "", "style='line-height: 15px;'");
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        print("<strong>Seleccione el Soporte del contrato:</strong><br>");
        $css->input("file", "UpSoporte", "form-control", "UpSoporte", "", "", "", "", "", "style='line-height: 15px;'");
    $css->CerrarDiv();
    print("<br><br><br><br>");
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        
        print("<strong>Enviar:</strong><br>");
        $css->CrearBotonEvento("BtnSubir", "Ejecutar", 1, "onclick", "ConfirmarCarga()", "verde", "");
    $css->CerrarDiv();
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivProgress", "col-md-12", "center", 1, 1);
        $css->ProgressBar("PgProgresoUp", "LyProgresoUP", "", 0, 0, 100, 0, "0%", "", "");
    $css->CerrarDiv();
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivProcess", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    
$css->PageFin();
print('<script src="jsPages/cargar_contrato_liquidado.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>