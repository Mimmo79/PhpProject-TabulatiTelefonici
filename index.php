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
                 * ogni elemento contiene una riga
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
            //leggiRubrica();
            //echo $rubrica[3]['num_SIM'] . '<br />';

            
            
             /* 
             * Argomenti:
             * 
             * 1 - codice identificativo della riga in cui compare il numero di cellulare
             *     che identifica l'inizio del tabulato
             * 2 - codice che identifica la fine del tabulato
             * 3 - codice che identifica una linea voce
             * 4 - codice che identifica una linea dati
             * 
             */
            function scansionatore_abb($id_start = 04, $id_stop = 37, $id_voce = 05, $id_dati = 06){

                                
                $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat');
                $linee  = explode("\n", $telecom_file);             // array delle righe                       
                foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                    $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                    for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                        $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                    }
                }   
                

                
                //super_data_array[n][d][y][x]
                //----------------------------
                //
                //[n=0][d=0][y=0][x=0] n° SIM 1
                //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
                //
                //[n=1][d=0][y=0][x=0] n° SIM 2
                //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne           
                
                $n=0;                                                       // indice SIM
                $id_array_voce=0;
                $id_array_dati=0;
                for ($x=0; $x<count($linee); $x++) {                        // per ogni riga del "data_array"
                    if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                        $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM  
                    } 
                    if ($data_array[$x][0] == $id_voce){                    // identifico la linea di traf. voce
                        for ($e=0; $e<9; $e++) {                            // trasferisco la riga voce
                            $super_data_array[$n][1][$id_array_voce][$e] = $data_array[$x][$e];
                        }
                        $id_array_voce++;
                    }
                    if ($data_array[$x][0] == $id_dati){                    // identifico la linea di traf. dati
                        for ($e=0; $e<9; $e++) {                            // trasferisco la riga dati
                            $super_data_array[$n][2][$id_array_dati][$e] = $data_array[$x][$e];
                        }
                        $id_array_dati++;
                    }
                    if ($data_array[$x][0] == $id_stop){                     // identifico la linea di fine report              
                        $n++;
                        $id_array_voce=0;
                        $id_array_dati=0;
                    }
                }     
        
            $GLOBALS['dati']=$super_data_array;
                                 
            }
            //scansionatore_abb();
            
            function scansionatore_ric($id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63){

                                
                $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Ricaricabile\201701888011111046A.dat');
                $linee  = explode("\n", $telecom_file);             // array delle righe                       
                foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                    $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                    for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                        $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                    }
                }   
                

                
                //super_data_array[n][d][y][x]
                //----------------------------
                //
                //[n=0][d=0][y=0][x=0] n° SIM 1
                //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
                //
                //[n=1][d=0][y=0][x=0] n° SIM 2
                //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne           
                
                $n=0;                                                       // indice SIM
                $id_array_voce=0;
                $id_array_dati=0;
                for ($x=0; $x<count($linee); $x++) {                        // per ogni riga di "data_array"
                    if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                        $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM  
                    } 
                    if ($data_array[$x][0] == $id_voce){                    // identifico la linea di traf. voce
                        for ($e=0; $e<8; $e++) {                            // trasferisco la riga voce
                            $super_data_array[$n][1][$id_array_voce][$e] = $data_array[$x][$e];
                        }
                        $id_array_voce++;
                    }
                    if ($data_array[$x][0] == $id_dati){                    // identifico la linea di traf. dati
                        for ($e=0; $e<8; $e++) {                            // trasferisco la riga dati
                            $super_data_array[$n][2][$id_array_dati][$e] = $data_array[$x][$e];
                        }
                        $id_array_dati++;
                    }
                    if ($data_array[$x][0] == $id_stop){                     // identifico la linea di fine report              
                        $n++;
                        $id_array_voce=0;
                        $id_array_dati=0;
                    }
                }     
        
            $GLOBALS['dati']=$super_data_array;
                                 
            }
            //scansionatore_ric();
            
            function scansionatore_ric_riep($id_start = 05, $id_stop = 25, $id_pers = 15, $id_voce = 61, $id_dati = 63){

                                
                $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Ricaricabile\201701888011111046A.dat');
                $linee  = explode("\n", $telecom_file);             // array delle righe                       
                foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                    $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                    for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                        $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                    }
                }   
                

                
                //super_data_array[n][d][y][x]
                //----------------------------
                //
                //[n=0][d=0][y=0][x=0] n° SIM 1
                //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
                //
                //[n=1][d=0][y=0][x=0] n° SIM 2
                //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
                //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne           
                
                $n=0;                                                       // indice SIM
                $id_array_voce=0;
                $id_array_dati=0;
                for ($x=0; $x<count($linee); $x++) {                        // per ogni riga di "data_array"
                    if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                        $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM  
                    } 
                    if ($data_array[$x][0] == $id_voce){                    // identifico la linea di traf. voce
                        for ($e=0; $e<8; $e++) {                            // trasferisco la riga voce
                            $super_data_array[$n][1][$id_array_voce][$e] = $data_array[$x][$e];
                        }
                        $id_array_voce++;
                    }
                    if ($data_array[$x][0] == $id_dati){                    // identifico la linea di traf. dati
                        for ($e=0; $e<8; $e++) {                            // trasferisco la riga dati
                            $super_data_array[$n][2][$id_array_dati][$e] = $data_array[$x][$e];
                        }
                        $id_array_dati++;
                    }
                    if ($data_array[$x][0] == $id_stop){                     // identifico la linea di fine report              
                        $n++;
                        $id_array_voce=0;
                        $id_array_dati=0;
                    }
                }     
        
            $GLOBALS['dati']=$super_data_array;
                                 
            }
            //scansionatore_riep();
            
            
            function apri_collegamentoDB(){
                $servername = "x023";
                $username = "";
                $password = "";
                $dbname = "";

                // creo la connessione
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                // verifico la connessione
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                
                // sql per creare una tabella
                $sql = "CREATE TABLE MyGuests (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                firstname VARCHAR(30) NOT NULL,
                lastname VARCHAR(30) NOT NULL,
                email VARCHAR(50),
                reg_date TIMESTAMP
                )";

                //esegueo query e verifico esito
                if (mysqli_query($conn, $sql)) {
                    echo "Table MyGuests created successfully";
                } else {
                    echo "Error creating table: " . mysqli_error($conn);
                }
                
                //sql per inserimento dati
                $sql = "INSERT INTO MyGuests (firstname, lastname, email)
                        VALUES ('John', 'Doe', 'john@example.com')";

                //esegueo query e verifico esito
                if (mysqli_query($conn, $sql)) {
                    echo "New record created successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }

                
                
            }
            
            //apri_collegamentoDB();
            
            
           
        ?>
    </body>
</html>
