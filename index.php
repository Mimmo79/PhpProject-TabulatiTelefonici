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
                $linee        = explode("\n", $txt_file);       //array di linee
                foreach($linee as $n_linea => $data){
                    $row_data = explode(';', $data);            //array con gli elementi di ogni riga della rubrica
                    $info[$n_linea]['num_SIM']  = $row_data[0]; //array bidimensionale 
                    $info[$n_linea]['nome']     = $row_data[1]; // [0]-[num_SIM][nome][servizio]
                    $info[$n_linea]['servizio'] = $row_data[2]; // [1]-[num_SIM][nome][servizio]

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

            
            

            function scansionatore_abb($rubrica, $id = 04){
             /* 
             * Argomenti:
             * 1 - array "rubrica"
             * 2 - codice identificativo della riga in cui compare il numero di cellulare
             *     che identifica l'inizio del tabulato
             * 
             */
                
                $telecom_file = file_get_contents('' 
                . 'C:\Users\senma\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat');
                $linee  = explode("\n", $telecom_file);             //array delle righe                       
                foreach($linee as $n_linea => $riga){               //scansione riga x riga                                         
                    $elem_riga = preg_split("/[\s,]+/", "$riga");   //array degli elementi di ogni riga
                    if ($elem_riga[0] == $id){                      //identifico la linea d'inizio report
                        for ($x = 0; $x < count($rubrica); $x++) {          //confronto il numero con tutta la rubrica
                            if ($elem_riga[1] == $rubrica[$x]['num_SIM']){  //il numero SIM è nella rubrica
                                echo "Alla linea " . $n_linea . " ho riconosciuto il numero " 
                                . $rubrica[$x]['num_SIM'] . "<br />" ;                           
                                break;
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
