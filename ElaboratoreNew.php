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
     * Description of Parser
     *
     * @author Massi
     */

    
    require './Parser.php';

    require './ParserAnalitico.php';

    require './ParserRiepiloghiRicaricabile.php';



    $obj = new ParserRiepiloghiRicaricabile();
    $obj->leggiFile($obj->nome_file_ric_riep);

    //ABB $id_start = 04, $id_stop = 37, $id_voce = 05, $id_dati = 06
    //RIC $id_start = 60, $id_stop = 72, $id_voce = 61, $id_dati = 63
    $obj->scansionatore();
    $obj->sql_ric_riep();
    //$obj->var_dump_pre();
    ?>


    <p> Done </p>

    </body>
    
</html>
