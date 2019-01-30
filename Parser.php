<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parser
 *
 * @author SenMa
 */
class Parser {
    
    /**
     *
     * @array_4d_result contiene i dati estrapolati dal file divisi per SIM/tipo 
     */
    protected $array_4d_result="";
    protected $data_array;

    /**
     *
     * path dei file
     */
    public $nome_file_ric = 'C:\Users\senma\Desktop\File Telecom\mobile\Ricaricabile\201711888011111046A.dat';
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
    public      function var_dump_pre() {
        echo '<pre>';
        var_dump($this->array_4d_result );
        echo '</pre>';
        return null;
    }
    /**
     *  acquisizione dati
     *  -------------
     *  crea una matrice dei dati "data_array"
     */
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
