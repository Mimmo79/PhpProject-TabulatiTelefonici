<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>


    <?php

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of ElaboratoreOOP
     *
     * @author Massi
     */

    class ElaboratoreOOP {
        
        // @data_array contiene i dati estrapolati dal file NON FORMATTATI
        protected $data_array;
        // @array_4d_result contiene i dati estrapolati dal file FORMATTATI 
        protected $array_4d_result="";


        /**
         *
         * path dei file
         */
        public $nome_file_ric = 'K:\U_Telematica\TIM\TIM TABULATI CELLULARI\2018_12_ok\Ricaricabile\201812888011111046A.dat';
        public $nome_file_ric_riep = 'K:\U_Telematica\TIM\TIM TABULATI CELLULARI\ARCHIVIO TIM ANNO 2016\2016_09\Ricaricabile\Riepilogativo x personali\201609888011111046R.dat';
        public $nome_file_abb = 'C:\Users\senma\Desktop\File Telecom\Abbonamento\201701888011111046AF.dat';

        /**
         *  parametri DB
         *  -------------
         */
        private $servername = "lnx023";
        private $username = "telefonia";
        private $password = "telefonia";
        private $dbname = "telefonia";

        /**
         *  nomi tabelle
         *  -------------
         * @var $tab_ric_voce è il nome della tabella in cui verranno salvati i dati voce delle ricaricabili
         * @var $tab_ric_dati è il nome della tabella in cui verranno salvati i report dati delle ricaricabili
         * protected e non private perchè devono essere accessibili dai metodi della classe estesa
         */
        protected $tab_ric_voce = "mobile_ric_voce";
        protected $tab_ric_dati = "mobile_ric_dati";
        protected $tab_ric_riep = "mobile_ric_riep";
        protected $tab_abb_voce = "mobile_abb_voce";
        protected $tab_abb_dati = "mobile_abb_dati";

        // ritorna i dati 4d
        public      function getProperty(){
            return $this->array_4d_result;
        }
        
        protected   function mysql($sql){
            $i=0;
            $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

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
                echo "<br> Comando mysqli interrotto prematuramente. Istruzione n°= ". $i."<br>";
                var_dump($i, mysqli_error($conn));
            } 


            mysqli_close($conn);
        } 
        
        //crea le tabelle
        public      function creaTabelle(){

    //        $mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    //        /* check connection */
    //        if (mysqli_connect_errno()) {
    //            printf("Connect failed: %s\n", mysqli_connect_error());
    //            exit();
    //        }
    //        $mysqli->query("SHOW TABLES FROM telefonia");
    //        if ($result = $mysqli->store_result()) {
    //            while ($row = $result->fetch_row()) {
    //                printf("%s\n ciao", $row[0]);
    //            }
    //            $result->free();
    //        }
    //        $mysqli->close();


            $sql = "CREATE TABLE $this->tab_ric_voce (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nSIM` VARCHAR(50) NOT NULL,
        `cod` INT(11) NOT NULL,
        `data_chiamata` DATE NOT NULL,
        `ora` TIME NOT NULL,
        `numeroChiamato` VARCHAR(50) NOT NULL,
        `durata` TIME NOT NULL,
        `costo` DOUBLE NOT NULL,
        `direttrice` VARCHAR(50) NOT NULL,
        `tipo` VARCHAR(50) NOT NULL,
        INDEX `Indice 1` (`id`)
        );";


            $sql .=  "CREATE TABLE $this->tab_ric_dati (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nSIM` VARCHAR(50) NOT NULL,
        `cod` INT(11) NOT NULL,
        `data_conn` DATE NOT NULL,
        `durata` TIME NOT NULL,
        `direttrice` VARCHAR(50) NOT NULL,
        `byte` INT(11) NOT NULL,
        `costo` DOUBLE NOT NULL,
        `tipo` VARCHAR(50) NOT NULL,
        `apn` VARCHAR(50) NOT NULL,
        INDEX `Indice 1` (`id`)
        );";

            $sql .= "CREATE TABLE $this->tab_abb_voce (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nSIM` VARCHAR(50) NOT NULL,
        `cod` INT(11) NOT NULL,
        `tipo` VARCHAR(50) NOT NULL,
        `direttrice` VARCHAR(50) NOT NULL,
        `numeroChiamato` VARCHAR(50) NOT NULL,
        `data_chiamata` DATE NOT NULL,
        `ora` TIME NOT NULL,
        `durata` TIME NOT NULL,
        `costo` DOUBLE NOT NULL,
        INDEX `Indice 1` (`id`)
        );";

            $sql .=  "CREATE TABLE $this->tab_abb_dati (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nSIM` VARCHAR(50) NOT NULL,
        `cod` INT(11) NOT NULL,
        `tipo` VARCHAR(50) NOT NULL,
        `apn` VARCHAR(50) NOT NULL,
        `data_conn` DATE NOT NULL,
        `ora` TIME NOT NULL,
        `durata` TIME NOT NULL,
        `byte` INT(11) NOT NULL,
        `costo` DOUBLE NOT NULL,
        `bundle` VARCHAR(50) NOT NULL,
        INDEX `Indice 1` (`id`)
        );";

            $sql .=  "CREATE TABLE $this->tab_ric_riep (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nSIM` VARCHAR(50) NOT NULL,
        `cod` INT(11) NOT NULL,
        `direttrice` VARCHAR(50) NOT NULL,
        `numeroChiamate` INT(11) NOT NULL,
        `durata` TIME NOT NULL,
        `nonUsato` VARCHAR(50) NOT NULL,
        `costo` DOUBLE NOT NULL,
        `data_chiamata` DATE NOT NULL,
        INDEX `Indice 1` (`id`)
        );";



        $this->mysql($sql);

        }
     
        // visualizza i dati estrapolati
        public      function var_dump_pre() {
            echo '<pre>';
            var_dump($this->array_4d_result );
            echo '</pre>';
            return null;
        }
        
        //legge il file
        public      function leggiFile($nome_file){
            //trasferisco il contenuto del file in un array
            $telecom_file = file_get_contents($nome_file);    //trasforma il file in una stringa 
            $linee  = explode("\n", $telecom_file);                     // array delle righe                       
            foreach($linee as $n_linea => $riga){               // scansione riga x riga   
                $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                    $this->data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                }
            }
        }

    }

    class Elaboratore   extends ElaboratoreOOP {

        protected $data;    

        public function __construct(){
            $array_4d_result=null;
            $data_array=null;
            $data=null;
        }

        // formatta l'array
        // viene usato per tutti i file analitici di tutte le SIM 
        public function scansionatore($id_start, $id_stop, $id_voce, $id_dati){

            $data_array= $this->data_array;         // array con i dati grezzi
            $n_linee=count($this->data_array);
            $this->data=substr($data_array[0][3],-4)."01";    // ricavo la data 201701 -> 170101
            $nSIM=0;                                             
            $id_array_voce=0;
            $id_array_dati=0;

            // imposto le caratteristiche del file in base al codice di inizio
            if ($id_start==04){         //abb
                $n_campi_voce=8;
                $n_campi_dati=9;
            } else if ($id_start==60) { //ric
                $n_campi_voce=8;
                $n_campi_dati=8;

            } else {
                echo "Codici di inizio non riconosciuto" ;
                return;
            }


            // formatto i dati da $data_array -> $array_4d_result
            for ($x=0; $x<$n_linee-1; $x++) {                                       // per ogni riga del "data_array" N.B. -1 perchl'ultima riga del file è vuota
                //echo "nSIM=".$nSIM." ".$data_array[$x][0]."+++";
                if ($data_array[$x][0] == $id_start) {                              // identifico la linea d'inizio report, dove compare il numero della SIM
                    $super_data_array[$nSIM][0][0][0] = trim($data_array[$x][1]);   // salvo il numero SIM

                // traffico voce    
                } elseif ($data_array[$x][0] == $id_voce) {                         // identifico la linea di traf. voce
                    for ($e=0; $e<$n_campi_voce; $e++) {                            // trasferisco la riga voce
                        $super_data_array[$nSIM][1][$id_array_voce][$e] = trim($data_array[$x][$e]);
                    }
                    $id_array_voce++;
                    $super_data_array[$nSIM][1][$id_array_voce][0]="***";          // carattere di fine

                // traffico dati
                } elseif ($data_array[$x][0] == $id_dati){                      // identifico la linea di traf. dati
                    for ($e=0; $e<$n_campi_dati; $e++) {                                    // trasferisco la riga dati
                        $super_data_array[$nSIM][2][$id_array_dati][$e] = trim($data_array[$x][$e]);
                    }
                    $id_array_dati++;
                    $super_data_array[$nSIM][2][$id_array_dati][0]="***";          // carattere di fine

                // fine report
                } elseif ($data_array[$x][0] == $id_stop){                      // identifico la linea di fine report              

                    if ($id_array_voce==0) {                                    // SIM senza traffico voce
                        for ($v=0; $v<$n_campi_voce; $v++){
                            $super_data_array[$nSIM][1][0][$v] = "0";
                        }
                        if ($id_start==04){
                            $super_data_array[$nSIM][1][0][4] = $this->data;
                        } else if ($id_start==60){
                            $super_data_array[$nSIM][1][0][1] = $this->data;
                        }
                    }    

                    if ($id_array_dati==0) {                                    //SIM senza traffico dati
                        for ($v=0; $v<$n_campi_dati; $v++){
                            $super_data_array[$nSIM][2][0][$v] = "0";
                        }
                        if ($id_start==04){
                            $super_data_array[$nSIM][2][0][3] = $this->data;
                        } else if ($id_start==60){
                            $super_data_array[$nSIM][2][0][1] = $this->data;
                        }
                    } 

                    $nSIM++;
                    $id_array_voce=0;
                    $id_array_dati=0;
                }
            }     

            $this->array_4d_result=$super_data_array;

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
            //[n=1][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne       

        }

        public function sql_ric() { 
            //61	170101	00:05:49	3355224xxx        	00:00:00	00000000,0000	AZ SMS ORIGINATO                                  	Aziendale            
            // VOCE
            $sql ="";
            echo count($this->array_4d_result) . " <br>";

            for ($n=0; $n<count($this->array_4d_result)-1; $n++) {
                $num=$this->array_4d_result[$n][0][0][0];    //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if ($this->array_4d_result[$n][1][$i][0]==="***"){
                        break;
                    }

                    $campo_1=(int)$this->array_4d_result[$n][1][$i][0];   //cod
                    $campo_2="20".$this->array_4d_result[$n][1][$i][1];   //data_chiamata
                    $campo_3=$this->array_4d_result[$n][1][$i][2];        //ora
                    $campo_4=$this->array_4d_result[$n][1][$i][3];        //numero chiamato
                    $campo_5=$this->array_4d_result[$n][1][$i][4];        //durata
                    $campo_6=str_replace(",", ".",$this->array_4d_result[$n][1][$i][5]);  //costo
                    $campo_7=$this->array_4d_result[$n][1][$i][6];       //tipo es. AZ SMS ORIGINATO
                    $campo_8=$this->array_4d_result[$n][1][$i][7];       //tipo es. Aziendale


                    //sql per inserimento dati voce              
                    $sql .= "INSERT INTO $this->tab_ric_voce (nSIM, cod, data_chiamata, ora, numeroChiamato, durata, costo, direttrice, tipo) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', '$campo_5', $campo_6, '$campo_7', '$campo_8' );";

                }

            }

            //63	170113	17:03:18	AZ DATI NAZIONALE                                 	00020971813	00000000,0000	Aziendale	APN IBOX
            // DATI
            for ($n=0; $n<count($this->array_4d_result)-1; $n++) {    
                $num=$this->array_4d_result[$n][0][0][0];                 //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if (!isset($this->array_4d_result[$n][2][$i][0]))     //se non ci sono dati esci
                        break;
                    if ($this->array_4d_result[$n][2][$i][0]==="***"){    //fine dati
                        break;
                    }

                    $campo_1=(int)$this->array_4d_result[$n][2][$i][0];   //cod
                    $campo_2="20".$this->array_4d_result[$n][2][$i][1];   //data_conn
                    $campo_3=$this->array_4d_result[$n][2][$i][2];        //durata
                    $campo_4=$this->array_4d_result[$n][2][$i][3];        //direttrice es. AZ DATI NAZIONALE
                    $campo_5=(int)$this->array_4d_result[$n][2][$i][4];   //byte
                    $campo_6=str_replace(",", ".",$this->array_4d_result[$n][2][$i][5]);        //costo
                    $campo_7=$this->array_4d_result[$n][2][$i][6];        //tipo es. Aziendale
                    $campo_8=$this->array_4d_result[$n][2][$i][7];        //APN

                    //sql per inserimento dati
                    $sql .= "INSERT INTO $this->tab_ric_dati (nSIM, cod, data_conn, durata, direttrice, byte, costo, tipo, apn) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', '$campo_3', '$campo_4', $campo_5, $campo_6, '$campo_7', '$campo_8' );";

                }

            }

            $istruzioni_sql = explode(';', $sql);                       // creo un array con le istruzioni sql
            $comando_sql="";                                            // inizializzo la stringa di comando
            foreach($istruzioni_sql as $n_istruzione => $istruzione){ 
                // echo $istruzione . " <br>";
                $comando_sql .= $istruzione . ";";                      // aggiungo il ; al termine di ogni istruzione
                if (!($n_istruzione % 1000)and !($n_istruzione===0) or  // raggruppo le istruzioni
                        $n_istruzione===count($istruzioni_sql)-1 ){     // sono all'ultima istruzione                       
                    // echo $comando_sql;
                    $this->mysql($comando_sql);                     
                    $comando_sql="";
                }
            }

        }    
    }

    class ElaboratoreRicRiep    extends ElaboratoreOOP {

        protected $data;


        public function __construct(){
            $this->scansionatore_ric_riep();
        }

        public function scansionatore_ric_riep($id_start = 05, $id_stop = 25, $id_pers = 15){

            $telecom_file = file_get_contents($this->nome_file_ric_riep);
            $linee  = explode("\n", $telecom_file);             // array delle righe                       
            foreach($linee as $n_linea => $riga){               // scansione riga x riga (n_linea parte da 0) 
                $elem_riga = preg_split("/[\t]/", "$riga");     // array degli elementi di ogni riga es. "/[\s,]+/"
                for ($x=0; $x<count($elem_riga); $x++) {        // creo un array bidimensionale con i dati
                    $data_array[$n_linea][$x]=$elem_riga[$x];   // "data_array" matrice dei dati
                }
            //echo $n_linea . " " . $elem_riga[0] . '<br />' ;  //linea debug                    
            }   

            $this->data=substr($data_array[0][3],-4)."01";
            $n=0;                                                       // indice SIM
            $id_array_riep=0;
            for ($x=0; $x<count($linee)-1; $x++) {                      // per ogni riga di "data_array" N.B. -1 perchl'ultima riga del file è vuota
                if ($data_array[$x][0] == $id_start){                   // identifico la linea d'inizio report
                    $super_data_array[$n][0][0][0] = trim($data_array[$x][1]);// salvo il numero SIM  
                }
                //if ($data_array[$x][0] == $id_pers and (strpos($data_array[$x][1], 'PERSONALE')===0)){
                if ($data_array[$x][0] == $id_pers){                    // identifico la linea di trafico
                    for ($e=0; $e<6; $e++) {                            // trasferisco ogni elemento della riga
                        $super_data_array[$n][2][$id_array_riep][$e] = trim($data_array[$x][$e]);
                    }
                    $id_array_riep++;                                   // incremento il puntatore nell'array di destinazione
                    $super_data_array[$n][2][$id_array_riep][0]="***";  // inserisco identificativo di chiusura
                }
                if ($data_array[$x][0] == $id_stop){                    // identifico la linea di fine report              
                    $n++;
                    $id_array_riep=0;
                }
            }     

            $this->array_4d_result=$super_data_array;


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
            //[n=1][d=3][y][x] array bidimensionale riepilogo personali Y=righe x=colonne

            }

        public function sql_ric_riep() { 
            //15	PERSONALE VS RETE FISSA    	000025	0000002:24:21	000000000	00000000000002,2542        // VOCE
            $sql ="";
            for ($n=0; $n<count($this->array_4d_result); $n++) {    
                $num=$this->array_4d_result[$n][0][0][0];    //numero SIM, trim elimina gli spazi es. (float)trim
                for ($i=0; $i<100000; $i++){
                    if (!isset($this->array_4d_result[$n][2][$i][0])){     //se non ci sono dati esci
                        break;
                    }
                    if ($this->array_4d_result[$n][2][$i][0]==="***"){
                        break;
                    }

                    $campo_1=(int)$this->array_4d_result[$n][2][$i][0];     //cod
                    $campo_2=$this->array_4d_result[$n][2][$i][1];          //direttrice
                    $campo_3=(int)$this->array_4d_result[$n][2][$i][2];     //numero chiamate
                    $campo_4=$this->array_4d_result[$n][2][$i][3];          //durata
                    $campo_5=$this->array_4d_result[$n][2][$i][4];          //nonUsato
                    $campo_6=str_replace(",", ".",$this->array_4d_result[$n][2][$i][5]);  //costo
                    $campo_7=$this->data;                                   //data_chiamata



                    //sql per inserimento dati voce              
                    $sql .= "INSERT INTO $this->tab_ric_riep (nSIM, cod, direttrice, numeroChiamate, durata, nonUsato, costo, data) "
                            . "VALUES ( '$num' , $campo_1, '$campo_2', $campo_3, '$campo_4', '$campo_5', $campo_6, '$campo_7');";

                }

            }

            $istruzioni_sql = explode(';', $sql);                       // creo un array con le istruzioni sql
            $comando_sql="";                                            // inizializzo la stringa di comando
            foreach($istruzioni_sql as $n_istruzione => $istruzione){ 
                //echo $istruzione . " <br>";
                $comando_sql .= $istruzione . ";";                      // aggiungo il ; al termine di ogni istruzione
                if (!($n_istruzione % 1000)and !($n_istruzione===0) or  // raggruppo le istruzioni
                        $n_istruzione===count($istruzioni_sql)-1 ){     // sono all'ultima istruzione                       
                    $this->mysql($comando_sql);                     
                    $comando_sql="";
                }
            }

        }

    }

    /*
    $obj = new ElaboratoreRicRiep();
    $obj->leggiFile($obj->nome_file_ric_riep);
    //ABB $id_start = 04, $id_stop = 37, $id_voce = 05, $id_dati = 06
    //RIC $id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63
    $obj->scansionatore_ric_riep();
    $obj->sql_ric_riep();
    //$obj->var_dump_pre();
    */

    $obj = new Elaboratore();
    $obj->leggiFile($obj->nome_file_ric);
    // ABB $id_start = 04, $id_stop = 37, $id_voce = 05, $id_dati = 06
    // RIC $id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63
    $obj->scansionatore($id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63);
    $obj->sql_ric();
    //$obj->var_dump_pre();
    
    
    ?>

    
        

    <p> Done </p>

    </body>
    
</html>
