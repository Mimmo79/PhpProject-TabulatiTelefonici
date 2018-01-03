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
        /*
         *  tempo script
         *  -------------
         */
        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $start = $time;


        /*
         *  parametri DB
         *  -------------
         */
        $servername = "lnx023";
        $username = "telefonia";
        $password = "telefonia";
        $dbname = "telefonia";
         /*
         *  NOMI TABELLE
         *  -------------
         */
        $tab_ric_voce = "ric_voce";
        $tab_ric_dati = "ric_dati";
        $tab_abb_voce = "abb_voce";
        $tab_abb_dati = "abb_dati";
        



        //creaTabelle();
        //leggiRubrica();
        //scansionatore_abb();
        scansionatore_ric();
        //scansionatore_ric_riep();
        //sql_abb();
        sql_ric();
        //testDB();
        //echo "ciao";

        

        
        
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



        /* 
         * @argument
         * 
         * 1 - codice identificativo della riga in cui compare il numero di cellulare
         *     che identifica l'inizio del tabulato
         * 2 - codice che identifica la fine del tabulato
         * 3 - codice che identifica una linea voce
         * 4 - codice che identifica una linea dati
         * 
         */
        function scansionatore_abb($id_start = 04, $id_stop = 37, $id_voce = 05, $id_dati = 06){
        //fonia 05	AZIENDALE SMS              	Numero Altro Operatore      	3385877xxx        	170102	11:47:21	00:00:00	00000000,0280	    
        //dati  06	AZIENDALE DATI             	I-Box                       	170127	11:35:01	00:14:11	000000003	00000000,0000	Interc 2014 2GB
            $telecom_file = file_get_contents('C:\Users\massi\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat');
            $linee  = explode("\n", $telecom_file);             // array delle righe                       
            foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                    $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                }
            }   

            $n=0;                                                       // indice SIM
            $id_array_voce=0;
            $id_array_dati=0;
            for ($x=0; $x<count($linee)-1; $x++) {                        // per ogni riga del "data_array" N.B. -1 perchl'ultima riga del file è vuota
                if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                    $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM  
                } 
                if ($data_array[$x][0] == $id_voce){                    // identifico la linea di traf. voce
                    for ($e=0; $e<8; $e++) {                            // trasferisco la riga voce
                        $super_data_array[$n][1][$id_array_voce][$e] = $data_array[$x][$e];
                    }
                    $id_array_voce++;
                    $super_data_array[$n][1][$id_array_voce][0]="***";
                }
                if ($data_array[$x][0] == $id_dati){                    // identifico la linea di traf. dati
                    for ($e=0; $e<9; $e++) {                            // trasferisco la riga dati
                        $super_data_array[$n][2][$id_array_dati][$e] = $data_array[$x][$e];
                    }
                    $id_array_dati++;
                    $super_data_array[$n][2][$id_array_dati][0]="***";
                }
                if ($data_array[$x][0] == $id_stop){                     // identifico la linea di fine report              
                    $n++;
                    $id_array_voce=0;
                    $id_array_dati=0;
                }
            }     

        $GLOBALS['dati']=$super_data_array;
        
        //super_data_array[n][d][y][x]
        //----------------------------
        //
        //[n=0][d=0][y=0][x=0] n° SIM 1
        //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
        //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
        //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne
        //
        //[n=1][d=0][y=0][x=0] n° SIM 2
        //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
        //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne
        //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne       
        
        }



        

        function scansionatore_ric($id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63){
        //61	170101	00:05:49	3355224xxx        	00:00:00	00000000,0000	AZ SMS ORIGINATO                                  	Aziendale
        //63	170113	17:03:18	AZ DATI NAZIONALE                                 	00020971813	00000000,0000	Aziendale	APN IBOX
            $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Ricaricabile\201701888011111046A.dat');
            $linee  = explode("\n", $telecom_file);             // array delle righe                       
            foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                    $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                }
            //echo $n_linea . " " . $elem_riga[0] . '<br />' ;  //linea debug
            }   

            $n=0;                                                       // indice SIM
            $id_array_voce=0;
            $id_array_dati=0;
            for ($x=0; $x<count($linee)-1; $x++) {                      // per ogni riga di "data_array" N.B. -1 perchl'ultima riga del file è vuota

                if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                    $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM  
                }
                if ($data_array[$x][0] == $id_voce){                    // identifico la linea di traf. voce
                    for ($e=0; $e<8; $e++) {                            // trasferisco ogni elemento della riga
                        $super_data_array[$n][1][$id_array_voce][$e] = $data_array[$x][$e];
                    }
                    $id_array_voce++;                                   // incremento il puntatore nell'array di destinazione
                    $super_data_array[$n][1][$id_array_voce][0]="***";  // inserisco identificativo di chiusura
                }
                if ($data_array[$x][0] == $id_dati){                    // identifico la linea di traf. dati
                    for ($e=0; $e<8; $e++) {                            // trasferisco ogni elemento della riga
                        $super_data_array[$n-1][2][$id_array_dati][$e] = $data_array[$x][$e];   //-1 necessario perchè id_stop fra voce e dati
                    }
                    $id_array_dati++;                                   // incremento il puntatore nell'array di destinazione
                    $super_data_array[$n-1][2][$id_array_dati][0]="***";  // inserisco identificativo di chiusura
                }
                if ($data_array[$x][0] == $id_stop){                    // identifico la linea di fine report              
                    $n++;
                    $id_array_voce=0;
                    $id_array_dati=0;
                }
            }     

        $GLOBALS['dati']=$super_data_array;

        //super_data_array[n][d][y][x]
        //----------------------------
        //
        //[n=0][d=0][y=0][x=0] n° SIM 1
        //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
        //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
        //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne
        //
        //[n=1][d=0][y=0][x=0] n° SIM 2
        //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
        //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne
        //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne
        
        }





        function scansionatore_ric_riep($id_start = 05, $id_stop = 25, $id_pers = 15){


            $telecom_file = file_get_contents('C:\Users\senma\Desktop\File Telecom\Ricaricabile\Riepilogo_Personali\201701888011111046R.dat');
            $linee  = explode("\n", $telecom_file);             // array delle righe                       
            foreach($linee as $n_linea => $riga){               // scansione riga x riga (n_linea parte da 0) 
                $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                    $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                }
            //echo $n_linea . " " . $elem_riga[0] . '<br />' ;  //linea debug                    
            }   



            //super_data_array[n][d][y][x]
            //----------------------------
            //
            //[n=0][d=0][y=0][x=0] n° SIM 1
            //[n=0][d=1][y][x] array bidimensionale voce Y=righe x=colonne
            //[n=0][d=2][y][x] array bidimensionale dati Y=righe x=colonne
            //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne
            //
            //[n=1][d=0][y=0][x=0] n° SIM 2
            //[n=1][d=1][y][x] array bidimensionale voce Y=righe x=colonne
            //[n=1][d=2][y][x] array bidimensionale dati Y=righe x=colonne
            //[n=0][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne



            $n=0;                                                       // indice SIM
            $id_array=0;
            for ($x=0; $x<count($linee)-1; $x++) {                      // per ogni riga di "data_array" N.B. -1 perchl'ultima riga del file è vuota
                if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                    $super_data_array[$n][0][0][0] = $data_array[$x][1];// salvo il numero SIM
                } 
                if (strpos($data_array[$x][1],"PERSONALE")=== TRUE){    // identifico la linea di traf. voce
                    for ($e=0; $e<6; $e++) {                            // trasferisco la riga
                        $super_data_array[$n][3][$id_array][$e] = $data_array[$x][$e];
                    }
                    $id_array++;
                }
                if ($data_array[$x][0] == $id_stop){                     // identifico la linea di fine report              
                    $n++;
                    $id_array=0;
                }
            }


        $GLOBALS['dati']=$super_data_array;

        }




        function sql_abb() {
            //fonia 05	AZIENDALE SMS              	Numero Altro Operatore      	3385877xxx        	170102	11:47:21	00:00:00	00000000,0280	    
            // VOCE           
            $sql ="";
            for ($n=0; $n<count($GLOBALS['dati']); $n++) {    
                $num=$GLOBALS['dati'][$n][0][0][0];    //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if (!isset($GLOBALS['dati'][$n][1][$i][0])){     //se non ci sono dati esci
                        break;
                    }
                    if ($GLOBALS['dati'][$n][1][$i][0]==="***"){
                        break;
                    }
                    
                    $campo_1=(int)$GLOBALS['dati'][$n][1][$i][0];   //cod
                    $campo_2=$GLOBALS['dati'][$n][1][$i][1];        //tipo
                    $campo_3=str_replace("'", "",$GLOBALS['dati'][$n][1][$i][2]);        //direttrice
                    $campo_4=$GLOBALS['dati'][$n][1][$i][3];        //numero chiamato
                    $campo_5="20".$GLOBALS['dati'][$n][1][$i][4];   //data
                    $campo_6=$GLOBALS['dati'][$n][1][$i][5];        //ora
                    $campo_7=$GLOBALS['dati'][$n][1][$i][6];        //durata
                    $campo_8=str_replace(",", ".",$GLOBALS['dati'][$n][1][$i][7]);        //costo


                    //sql per inserimento dati
                    global $tab_abb_voce;
                    $sql .= "INSERT INTO `$tab_abb_voce` (`nSIM`, `cod`, `tipo`, `direttrice`, `numeroChiamato`, `data`, `ora`, `durata`, `costo`) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', '$campo_5', '$campo_6', '$campo_7', $campo_8 );";

                }

            }

            //dati  06	AZIENDALE DATI             	I-Box                       	170127	11:35:01	00:14:11	000000003	00000000,0000	Interc 2014 2GB
            // DATI
            for ($n=0; $n<count($GLOBALS['dati']); $n++) {    
                $num=$GLOBALS['dati'][$n][0][0][0];                 //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if (!isset($GLOBALS['dati'][$n][2][$i][0]))     //se non ci sono dati esci
                        break;
                    if ($GLOBALS['dati'][$n][2][$i][0]==="***"){    //fine dati
                        break;
                    }

                    $campo_1=(int)$GLOBALS['dati'][$n][2][$i][0];   //cod
                    $campo_2=$GLOBALS['dati'][$n][2][$i][1];        //tipo
                    $campo_3=$GLOBALS['dati'][$n][2][$i][2];        //apn
                    $campo_4="20".$GLOBALS['dati'][$n][2][$i][3];   //data
                    $campo_5=$GLOBALS['dati'][$n][2][$i][4];        //ora
                    $campo_6=$GLOBALS['dati'][$n][2][$i][5];        //durata
                    $campo_7=(int)$GLOBALS['dati'][$n][2][$i][6];   //byte
                    $campo_8=str_replace(",", ".",$GLOBALS['dati'][$n][2][$i][7]);        //costo
                    $campo_9=$GLOBALS['dati'][$n][2][$i][8];        //bundle

                    //sql per inserimento dati
                    global $tab_abb_dati;
                    $sql .= "INSERT INTO `$tab_abb_dati` (nSIM, cod, tipo, apn, data, ora, durata, byte, costo, bundle) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', '$campo_5', '$campo_6', $campo_7, $campo_8, '$campo_9' );";

                    
                }

            }
                    
            $istruzioni_sql = explode(';', $sql);
            $comando_sql="";                                            // inizializzo la stringa di comando
            foreach($istruzioni_sql as $n_istruzione => $istruzione){
                $comando_sql .= $istruzione . ";";                      // aggiungo il ; al termine di ogni istruzione
                if (!($n_istruzione % 1000)and !($n_istruzione===0) or  // raggruppo le istruzioni
                        $n_istruzione===count($istruzioni_sql)-1 ){     // sono all'ultima istruzione                       
                    mysql($comando_sql);                     
                    $comando_sql="";
                }
            }
 
        }       



        
        function sql_ric() { 
            //61	170101	00:05:49	3355224xxx        	00:00:00	00000000,0000	AZ SMS ORIGINATO                                  	Aziendale            
            // VOCE
            $sql ="";
            for ($n=0; $n<count($GLOBALS['dati']); $n++) {    
                $num=$GLOBALS['dati'][$n][0][0][0];    //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if ($GLOBALS['dati'][$n][1][$i][0]==="***"){
                        break;
                    }

                    $campo_1=(int)$GLOBALS['dati'][$n][1][$i][0];   //cod
                    $campo_2="20".$GLOBALS['dati'][$n][1][$i][1];   //data
                    $campo_3=$GLOBALS['dati'][$n][1][$i][2];        //ora
                    $campo_4=$GLOBALS['dati'][$n][1][$i][3];        //numero chiamato
                    $campo_5=$GLOBALS['dati'][$n][1][$i][4];        //durata
                    $campo_6=str_replace(",", ".",$GLOBALS['dati'][$n][1][$i][5]);  //costo
                    $campo_7=$GLOBALS['dati'][$n][1][$i][6];       //tipo es. AZ SMS ORIGINATO
                    $campo_8=$GLOBALS['dati'][$n][1][$i][7];       //tipo es. Aziendale


                    //sql per inserimento dati
                    global $tab_ric_voce;
                    $sql .= "INSERT INTO $tab_ric_voce (nSIM, cod, data, ora, numeroChiamato, durata, costo, direttrice, tipo) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', '$campo_5', $campo_6, '$campo_7', '$campo_8' );";

                }

            }

            //63	170113	17:03:18	AZ DATI NAZIONALE                                 	00020971813	00000000,0000	Aziendale	APN IBOX
            // DATI
            for ($n=0; $n<count($GLOBALS['dati']); $n++) {    
                $num=$GLOBALS['dati'][$n][0][0][0];                 //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if (!isset($GLOBALS['dati'][$n][2][$i][0]))     //se non ci sono dati esci
                        break;
                    if ($GLOBALS['dati'][$n][2][$i][0]==="***"){    //fine dati
                        break;
                    }

                    $campo_1=(int)$GLOBALS['dati'][$n][2][$i][0];   //cod
                    $campo_2="20".$GLOBALS['dati'][$n][2][$i][1];   //data
                    $campo_3=$GLOBALS['dati'][$n][2][$i][2];        //durata
                    $campo_4=$GLOBALS['dati'][$n][2][$i][3];        //direttrice es. AZ DATI NAZIONALE
                    $campo_5=(int)$GLOBALS['dati'][$n][2][$i][4];   //byte
                    $campo_6=str_replace(",", ".",$GLOBALS['dati'][$n][2][$i][5]);        //costo
                    $campo_7=$GLOBALS['dati'][$n][2][$i][6];        //tipo es. Aziendale
                    $campo_8=$GLOBALS['dati'][$n][2][$i][7];        //APN

                    //sql per inserimento dati
                    global $tab_ric_dati;
                    $sql .= "INSERT INTO $tab_ric_dati (nSIM, cod, data, durata, direttrice, byte, costo, tipo, apn) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', $campo_5, $campo_6, '$campo_7', '$campo_8' );";

                }

            }
            
            $istruzioni_sql = explode(';', $sql);                       // creo un array con le istruzioni sql
            $comando_sql="";                                            // inizializzo la stringa di comando
            foreach($istruzioni_sql as $n_istruzione => $istruzione){   
                $comando_sql .= $istruzione . ";";                      // aggiungo il ; al termine di ogni istruzione
                if (!($n_istruzione % 1000)and !($n_istruzione===0) or  // raggruppo le istruzioni
                        $n_istruzione===count($istruzioni_sql)-1 ){     // sono all'ultima istruzione                       
                    mysql($comando_sql);                     
                    $comando_sql="";
                }
            }

        }       




        function mysql($sql){
            
            global $servername,$username,$password,$dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            // verifico la connessione
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            if (mysqli_connect_errno()){
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            } 

            //eseguo la query
            if (mysqli_multi_query($conn, $sql)) {
                $i = 0;
                do {
                    $i++;
                } while (mysqli_next_result($conn)); 
            } 
            if (mysqli_errno($conn)) {
                echo "Batch execution prematurely ended on statement $i.\n";
                var_dump($i, mysqli_error($conn));
            } 
                
            
            mysqli_close($conn);
        }


        
        
        function testDB() { 

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "telefonia";

            // creo la connessione
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            // verifico la connessione
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());

            }
            $sql = "INSERT INTO prova (provadata)
                            VALUES ( '20171014' )";

            //esegueo query e verifico esito
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }


        }

        

        
        function creaTabelle(){
            global $tab_ric_voce;
            $sql = "CREATE TABLE $tab_ric_voce (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nSIM` VARCHAR(50) NOT NULL,
	`cod` INT(11) NOT NULL,
	`data` DATE NOT NULL,
	`ora` TIME NOT NULL,
	`numeroChiamato` VARCHAR(50) NOT NULL,
	`durata` TIME NOT NULL,
	`costo` DOUBLE NOT NULL,
	`direttrice` VARCHAR(50) NOT NULL,
	`tipo` VARCHAR(50) NOT NULL,
	INDEX `Indice 1` (`id`)
        );";
                        
            global $tab_ric_dati;
            $sql .=  "CREATE TABLE $tab_ric_dati (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nSIM` VARCHAR(50) NOT NULL,
	`cod` INT(11) NOT NULL,
	`data` DATE NOT NULL,
	`durata` TIME NOT NULL,
	`direttrice` VARCHAR(50) NOT NULL,
        `byte` INT(11) NOT NULL,
	`costo` DOUBLE NOT NULL,
	`tipo` VARCHAR(50) NOT NULL,
        `apn` VARCHAR(50) NOT NULL,
	INDEX `Indice 1` (`id`)
        );";

            global $tab_abb_voce;
            $sql .= "CREATE TABLE $tab_abb_voce (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nSIM` VARCHAR(50) NOT NULL,
	`cod` INT(11) NOT NULL,
	`tipo` VARCHAR(50) NOT NULL,
	`direttrice` VARCHAR(50) NOT NULL,
        `numeroChiamato` VARCHAR(50) NOT NULL,
	`data` DATE NOT NULL,
	`ora` TIME NOT NULL,
	`durata` TIME NOT NULL,
	`costo` DOUBLE NOT NULL,
	INDEX `Indice 1` (`id`)
        );";
       
            global $tab_abb_dati;
            $sql .=  "CREATE TABLE $tab_abb_dati (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nSIM` VARCHAR(50) NOT NULL,
	`cod` INT(11) NOT NULL,
	`tipo` VARCHAR(50) NOT NULL,
        `apn` VARCHAR(50) NOT NULL,
	`data` DATE NOT NULL,
	`ora` TIME NOT NULL,
	`durata` TIME NOT NULL,
        `byte` INT(11) NOT NULL,
	`costo` DOUBLE NOT NULL,
	`bundle` VARCHAR(50) NOT NULL,
	INDEX `Indice 1` (`id`)
        );";

        mysql($sql);

        }
        
        


        
        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $end = $time;
        
        $elapsed = $end-$start;
        echo "Tempo di esecuzione : ".$elapsed;

           
        ?>
    </body>
</html>
