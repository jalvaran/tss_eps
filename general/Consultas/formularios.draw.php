<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/formularios.class.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new formularios($idUser);
    
    switch ($_REQUEST["Accion"]) {
       case 1://Dibuja formulario para crear un tercero de manera general
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 100, "", "", "", ""); //100 sirve para indicarle al sistema que debe guardar el formulario de crear un tercero
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Tipo de Documento</strong>", 1);
                    $css->ColTabla("<strong>Identificación</strong>", 1);
                    $css->ColTabla("<strong>Ciudad</strong>", 1);
                    $css->ColTabla("<strong>Teléfono</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("TipoDocumento", "form-control", "TipoDocumento", "", "", "", "style=width:300px");
                        $Consulta=$obCon->ConsultarTabla("cod_documentos", "");
                        while($DatosTipoDocumento=$obCon->FetchAssoc($Consulta)){
                            $sel=0;
                            if($DatosTipoDocumento["Codigo"]==13){
                                $sel=1;
                            }
                            $css->option("", "", "", $DatosTipoDocumento["Codigo"], "", "", $sel);
                                print($DatosTipoDocumento["Codigo"]." ".$DatosTipoDocumento["Descripcion"]);
                            $css->Coption();
                        }    
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Num_Identificacion", "form-control", "Num_Identificacion", "", "", "Identificación", "off", "", "onchange=VerificaNIT()");
                    print("</td>");
                    print("<td>");
                        $css->select("CodigoMunicipio", "form-control", "CodigoMunicipio", "", "", "", "");
                            $Consulta=$obCon->ConsultarTabla("cod_municipios_dptos", "");
                            while($DatosMunicipios=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($DatosMunicipios["ID"]==1011){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosMunicipios["ID"], "", "", $sel);
                                    print($DatosMunicipios["Ciudad"]." ".$DatosMunicipios["Cod_mcipio"]);
                                $css->Coption();
                            }    
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "Telefono", "form-control", "Telefono", "", "", "Teléfono", "off", "", "");
                    print("</td>");
                    
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Nombres</strong>", 4,"C");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "PrimerNombre", "form-control", "PrimerNombre", "Primer Nombre", "", "Primer Nombre", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "OtrosNombres", "form-control", "OtrosNombres", "Otros Nombres", "", "Otros Nombres", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "PrimerApellido", "form-control", "PrimerApellido", "Primer Apellido", "", "Primer Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "SegundoApellido", "form-control", "SegundoApellido", "Segundo Apellido", "", "Segundo Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    $css->FilaTabla(16);
                        print("<td colspan=4>");
                            $css->input("text", "RazonSocial", "form-control", "RazonSocial", "Razon Social", "", "RazonSocial", "off", "", "", "");
                        print("</td>");
                    $css->CierraFilaTabla(); 
                    
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Dirección</strong>", 1);
                    $css->ColTabla("<strong>Email</strong>", 1);
                    $css->ColTabla("<strong>Cupo</strong>", 1);
                    $css->ColTabla("<strong>Código Tarjeta</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "Direccion", "form-control", "Direccion", "Direccion", "", "Dirección", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "Email", "form-control", "Email", "Email", "", "Email", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Cupo", "form-control", "Cupo", "Cupo", 0, "Cupo Crédito", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "CodigoTarjeta", "form-control", "CodigoTarjeta", "Codigo Tarjeta", "", "Código Tarjeta", "off", "", "", "onchange=VerificaCodigoTarjeta()");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 1
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>