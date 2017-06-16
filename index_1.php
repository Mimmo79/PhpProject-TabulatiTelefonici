<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        
            function leggiRubrica(){
                /*
                 * Leggo il file e lo salvo in una variabile(testo consecutivo senza alcuna tabulazione)
                 * 
                 * Esplode il testo in un array i cui campi vengono definiti da "\n"
                 * in questo caso ogni elemento contiene una riga
                 */

                $txt_file    = file_get_contents('Rubrica-31-05-17.csv');
                $linee        = explode("\n", $txt_file);


                foreach($linee as $n_linea => $data){
                    //assorbo i dati
                    $row_data = explode(';', $data);

                    $info[$n_linea]['num_SIM']  = $row_data[0];
                    $info[$n_linea]['nome']     = $row_data[1];
                    $info[$n_linea]['servizio'] = $row_data[2];

                    //visualizzo i dati
                    //echo 'Row ' . $n_linea . ' Numero SIM: '. $info[$n_linea]['num_SIM'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Nome: '      . $info[$n_linea]['nome'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Servizio: '  . $info[$n_linea]['servizio'] . '<br />';       
                }
                
                // PHP non può ritornare valori multipli, ma può ritornare array
                return $info;
            }
            
            //$info=leggiRubrica();
            //echo $info[3]['servizio'];

            
            
            
            function scansionatore_abb(){
                $telecom_file = file_get_contents('file Telecom\Abbonamento\201701888011111046AF.dat');
                $linee  = explode("\n", $telecom_file);
                
                echo $linee[0] . '<br />' ;
                echo $linee[1] . '<br />' ;
                $row_data = explode(' ', $linee[0]);
                
                
                foreach($linee as $n_linea => $data){
                    //assorbo i dati
                    $row_data = explode(' ', $data);

                    $info[$n_linea]['num_SIM']  = $row_data[0];
                    $info[$n_linea]['nome']     = $row_data[1];
                    $info[$n_linea]['servizio'] = $row_data[2];

                    //visualizzo i dati
                    //echo 'Row ' . $n_linea . ' Numero SIM: '. $info[$n_linea]['num_SIM'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Nome: '      . $info[$n_linea]['nome'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Servizio: '  . $info[$n_linea]['servizio'] . '<br />';       
                }
                
              
                
                $n_linee = count($linee);
                
            }
            
            scansionatore_abb();
            
            
        ?>
    </body>
</html>
