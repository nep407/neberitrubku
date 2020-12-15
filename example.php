<?php

namespace nep;
require_once 'neberitrubku.php';

use Neberitrubku\Checker;

$spam = new Checker();
if ($spam->findByPhone('84996811624')){
    if ($spam->getRatingValue() > 3 && $spam->getRatingCount() > 3) {
        // send an order
        true;
    }else{
        // send a spammer on an erotic travel
        false;
    }
}