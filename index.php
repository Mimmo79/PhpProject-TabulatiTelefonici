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
        
<!--        <form action="upload.php" method="post" enctype="multipart/form-data">
           Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit">
        </form>-->
        
        <?php
        
        
            function leggiRubrica(){
                /*
                 * Leggo il file e lo salvo in una variabile(testo consecutivo senza alcuna tabulazione)
                 * 
                 * Esplode il testo in un array i cui campi vengono definiti da "\n"
                 * in questo caso ogni elemento contiene una riga
                 */

                $txt_file    = file_get_contents('C:\Users\senma\Desktop\File Telecom\Rubrica-31-05-17.csv');
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
                //return $info;
                
                // rubrica" diventa una super global variable
                $GLOBALS['rubrica']=$info;
                
            }
            
            leggiRubrica();
            //echo $rubrica[3]['num_SIM'] . '<br />';

            
            
            /* 
             * Argomenti:
             * 1 - array "rubrica"
             * 2 - codice identificativo della riga in cui compare il numero di cellulare
             *     che identifica l'inizio del tabulato
             * 
             */
            function scansionatore_abb($rubrica, $id = 04){
                
                $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat');
                $linee  = explode("\n", $telecom_file);                       
                foreach($linee as $n_linea => $riga){                                         
                    $elem_riga = preg_split("/[\s,]+/", "$riga");
                    if ($elem_riga[0] == $id){
                        //echo $elem_riga[0] . " " . $elem_riga[1] . "";
                        for ($x = 0; $x < count($rubrica); $x++) {
                            if ($elem_riga[1] == $rubrica[$x]['num_SIM']){
                                echo "beccato" . "&nbsp" . $n_linea . "<br />" ;
                            }
                        } 
                    }
                    
                    
                    //&nbsp
                    //echo $row_data[0]. '<br />';
                    //echo $row_data[1]. '<br />';
                    //echo $row_data[2]. '<br />';
                    
                    //$info[$n_linea]['num_SIM']  = $row_data[0];
                    //$info[$n_linea]['nome']     = $row_data[1];
                    //$info[$n_linea]['servizio'] = $row_data[2];

                    //visualizzo i dati
                    //echo 'Row ' . $n_linea . ' Numero SIM: '. $info[$n_linea]['num_SIM'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Nome: '      . $info[$n_linea]['nome'] . '<br />';
                    //echo 'Row ' . $n_linea . ' Servizio: '  . $info[$n_linea]['servizio'] . '<br />';       
                }
                   
                $n_linee = count($linee);
                
            }
            
            scansionatore_abb($rubrica);
            
            
        ?>
    </body>
</html>
