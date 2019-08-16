    function number_format(numero){

        // Variable que contendra el resultado final

        var resultado = "";
        // Si el numero empieza por el valor "-" (numero negativo)

        if(numero[0]=="-")
        {

            nuevoNumero=numero.replace(/\./g,'').substring(1);

        }else{

           
            nuevoNumero=numero.replace(/\./g,'');

        }

        if(numero.indexOf(",")>=0)

            nuevoNumero=nuevoNumero.substring(0,nuevoNumero.indexOf(","));

        for (var j, i = nuevoNumero.length - 1, j = 0; i >= 0; i--, j++)

            resultado = nuevoNumero.charAt(i) + ((j > 0) && (j % 3 == 0)? ".": "") + resultado;

         if(numero.indexOf(",")>=0)

            resultado+=numero.substring(numero.indexOf(","));

        if(numero[0]=="-")

        {

            return "-"+resultado;

        }else{

            return resultado;

        }

    }