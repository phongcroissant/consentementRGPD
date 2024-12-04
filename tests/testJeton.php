<?php
include "./vendor/autoload.php";
use App\Modele\Modele_Jeton;

try {
    $jeton2=Modele_Jeton::insertToken(1);
    echo "Oui";
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    $resultat = Modele_Jeton::researchToken($jeton2);
    if ($resultat) {
        echo "Succès : Jeton trouvé : " . print_r($resultat, true) . "\n\n";
    } else {
        echo $jeton2;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    Modele_Jeton::updateToken($jeton2);
    echo "Succès : Jeton mis à jour : $jeton2\n\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
