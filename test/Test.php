<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Classe_Elaborazione_File
 *
 * @author Massi
 */
class Classe_Elaborazione_File {
    
    public $prop1 = "I'm a class property!";
    
    public function __construct(){                              //metodo magico costruttore
      echo 'The class "', __CLASS__, '" was initiated!<br />';  //__CLASS__ restituisce il nome della classe nella quale è chiamato
    }
    
    public function __destruct(){                               // metodo magico distruttore
      echo 'The class "', __CLASS__, '" was destroyed.<br />';  // richiamato quando l'oggetto viene distrutto
    }                                                           // viene eseguito al termine del file .php o usando la funzione unset()
    
    public function __toString(){             // metodo magico per convertire l'oggetto in stringa automaticamente se echo "$obj;"
      echo "Using the toString method: ";
      return $this->getProperty();
    }
    
    public function setProperty($newval){
          $this->prop1 = $newval;
    }

    public function getProperty(){
        return $this->prop1 . "<br />";
    }
}


class MyOtherClass extends Classe_Elaborazione_File{
    public function __construct(){
        parent::__construct(); // Call the parent class's constructor //operatore di risoluzione di visibilità
        echo "A new constructor in " . __CLASS__ . ".<br />";
    }
    
    public function newMethod(){
        echo "From a new method in " . __CLASS__ . ".<br />";
    }
}



$obj = new Classe_Elaborazione_File;
$obj1 = new MyOtherClass;
 
echo $obj->prop1; // Output the property;
echo $obj->getProperty(); // Get the property value
 
$obj->setProperty("I'm a new property value!"); // Set a new one
 
echo $obj->getProperty(); // Read it out again to show the change

// Destroy the object
unset($obj);

