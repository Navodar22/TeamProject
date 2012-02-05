<?php

class ModelLoader
{               
  public $context;

  public function __construct($container){
    $this->context = $container;
  }           
  
  public function getModel($name) { 
    return new $name($this->context);
  }
}
