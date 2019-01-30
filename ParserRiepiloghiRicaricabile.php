<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParserRiepiloghiRicaricabile
 *
 * @author SenMa
 */

class ParserRiepiloghiRicaricabile    extends Parser {

    protected $data;

    public function __construct(){
        $this->scansionatore();
    }

    public function scansionatore($id_start = 05, $id_stop = 25, $id_pers = 15){

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

