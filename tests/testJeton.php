<?php
include "./vendor/autoload.php";
use App\Modele\Modele_Jeton;

try {
    $jeton2=Modele_Jeton::insertToken(1);
    echo "Oui";
} catch (Exception $e) {
    echo $e->getMessage();
}

