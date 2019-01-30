<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParserAnalitico
 *
 * @author SenMa
 */

class ParserAnalitico      extends Parser {

    protected $data;    

    public function __construct(){
        $array_4d_result=null;
        $data_array=null;
        $data=null;
    }

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
        for ($n=0; $n<count($this->array_4d_result); $n++) {    
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

