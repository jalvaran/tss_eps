<?php

class numeros_letras{
    
    function meses($NumeroMes){
        $valor = array ('enero','febrero','marzo','abril','mayo','junio','julio','agosto',
            'septiembre','octubre', 'noviembre','diciembre');

        return $valor[$NumeroMes - 1];
    }
   
    function basico($numero){
        $valor = array ('un','dos','tres','cuatro','cinco','seis','siete','ocho',
            'nueve','diez', 'once','doce','trece','catorce','quince','dieciseis','diecisiete',
            'dieciocho','diecinueve','veinte','veintiuno','veintidos', 'veintitres',
            'veinticuatro','veinticinco','veintiseis','veintisiete','veintiocho','veintinueve');

            return $valor[$numero - 1];
    }
    
    function decenas($n){
        $decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',70=>'setenta',80=>'ochenta',90=>'noventa');
            if( $n <= 29) return $this->basico($n);
                    $x = $n % 10;
            if ( $x == 0 ) {
                    return $decenas[$n];
            } else return $decenas[$n - $x].' y '.$this->basico($x);
    }
	
    function centenas($n){
        $cientos = array (100 =>'cien',200 =>'doscientos',300=>'trescientos',
                        400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
                        700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
        if( $n >= 100) {
        if ( $n % 100 == 0 ) {
                return $cientos[$n];
        } else {
        $u = (int) substr($n,0,1);
        $d = (int) substr($n,1,2);
                return (($u == 1)?'ciento':$cientos[$u*100]).' '.$this->decenas($d);
        }
        } else return $this->decenas($n);
    }
	
    function miles($n) {
        if($n > 999) {
        if( $n == 1000) {return 'mil';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-3);
            $x = (int)substr($n,-3);
            if($c == 1) {$cadena = 'mil '.$this->centenas($x);}
            else if($x != 0) {$cadena = $this->centenas($c).' mil '.$this->centenas($x);}
            else $cadena = $this->centenas($c). ' mil';
            return $cadena;
        }
        } else return $this->centenas($n);
    }
	
    function millones($n) {
        $n=round($n);
        if($n == 1000000) {return 'un millon';}
        else {
            $l = strlen($n);
            //print("$l");
            $c = (int)substr($n,0,$l-6);
            $x = (int)substr($n,-6);
        if($c == 1) {
                $cadena = ' millon ';
        } else {
                $cadena = ' millones ';
        }
        return $this->miles($c).$cadena.(($x > 0)?$this->miles($x):'');
        }
    }

    function convertir($n) {
        /*
        if($n==1){
            return (' uno ');
        }
         * 
         */
        switch (true) {
            case ( $n == 0 ) : return 'CERO'; break;
            case ( $n >= 1 && $n <= 29) : return $this->basico($n); break;
            case ( $n >= 30 && $n < 100) : return $this->decenas($n); break;
            case ( $n >= 100 && $n < 1000) : return $this->centenas($n); break;
            case ($n >= 1000 && $n <= 999999): return $this->miles($n); break;
            case ($n >= 1000000): return $this->millones($n); break;
        }
    }
	
	
    /**
     * Fin Clase
     */
}
