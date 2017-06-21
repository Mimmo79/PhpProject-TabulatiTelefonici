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

            
            

            function scansionatore_abb($rubrica, $id_start = 04, $id_stop = 37){
             /* 
             * Argomenti:
             * 1 - array "rubrica"
             * 2 - codice identificativo della riga in cui compare il numero di cellulare
             *     che identifica l'inizio del tabulato
             * 3 - codice che identifica la fine del tabulato
             * 
             */
                $index=0;
                
                $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat');
                $linee  = explode("\n", $telecom_file);             // array delle righe                       
                foreach($linee as $n_linea => $riga){               // scansione riga x riga            
                                                                    //  echo $n_linea . " " . $riga. "<br />"; 
                    $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                                                                    //  echo "<br />" . count($elem_riga). "<br />";
                    for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale co i dati
                        $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                                                                    //  echo " $elem_riga[$x]"."[$x]";
                    }
                }   
                    
                    
                    
                    
                    if ($elem_riga[0] == $id_start){                // identifico la linea d'inizio report
                        $start_stop[$index][0]=$elem_riga[1];       // salvo il numero SIM
                        $start_stop[$index][1]=$n_linea;            // salvo la linea di inizio report
                                                                    //echo $start_stop[$index][1] ." ".$start_stop[$index][0]. " $index <br />";                                         
                    } 
                    else if ($elem_riga[0] == $id_stop){
                        $start_stop[$index][2]=$n_linea;            // salvo la linea di fine report
                                                                    //echo $start_stop[$index][1] ." ".$start_stop[$index][2]. " $index <br />";
                        ++$index; 
                    }
                
                
                
//                $start_stop
//                0 {numero SIM, riga inizio report, riga fine report}
//                1 {numero SIM, riga inizio report, riga fine report}
//                ecc.
                
                
                $n_SIM=count($start_stop);
                
               
                for ($x=0; $x<$n_SIM; $x++){                        // per ogni numero
                    $data[$x][0][0]=$start_stop[$x][0];
                                                                                //echo "numero SIM ".$data[$x][0][0]. "<br />"; 
                    for ($y=$start_stop[$x][1]; $y<$start_stop[$x][2]; $y++){   // per ogni linea del report
                        $data[$x][1][$y-$start_stop[$x][1]]=$linee[$y];
                                                                                //echo "dati ". $data[$x][1][$y-$start_stop[$x][1]]. "<br />";     
                    }
                            
                }
                
                
                
                
                
                   
                
                
            }
            
            scansionatore_abb($rubrica);
            
            
        ?>
    </body>
</html>
