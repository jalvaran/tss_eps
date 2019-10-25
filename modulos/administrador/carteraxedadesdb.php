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
$myPage="carteraxedadesdb.php";
$myTitulo="Plataforma TSS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];

$css->PageInit($myTitulo);
    print("<br>");
    $css->CrearDiv("", "col-md-12", "left", 1, 1); 
        $css->h3("", "", "", "");
                print("<strong>Cartera por edades</strong>");
        $css->Ch3();
    $css->CerrarDiv(); 
    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT NIT as idIPS, Nombre, `DataBase` FROM ips";
        }else{
            $sql="SELECT r.idIPS,i.Nombre,i.`DataBase` FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS<br>", "", "", "onchange=SeleccionarTablaTsEPS('carteraxedades')");
            $css->option("", "", "", '', "", "");
                    print('Seleccione un IPS');
                $css->Coption();
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                
                $css->option("", "", "", $DatosIPS["DataBase"], "", "");
                    print($DatosIPS["Nombre"]." ".$DatosIPS["idIPS"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();    
    $css->CrearDiv("", "col-md-6", "center", 1, 1);
        $sql="SELECT * FROM eps ORDER BY ID";
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbEPS", "form-control", "CmbEPS", "EPS<br>", "", "", "");
            while($DatosEPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosEPS["NIT"], "", "");
                    print($DatosEPS["Nombre"]." ".$DatosEPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
    print("<br><br><br><br><br><br><br>");
    $css->CrearDiv("DivOpcionesTablaTSEPS", "container", "left", 1, 1);
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivTablaTSEPS", "container", "left", 1, 1);
    
    $css->CerrarDiv();
    
$css->PageFin();

$css->Cbody();
print('<script src="jsPages/tablas_ts_eps.js"></script>');  //script propio de la pagina

$css->Chtml();

?>