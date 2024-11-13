<?php
namespace App\Fonctions;
    function Redirect_Self_URL():void{
        unset($_REQUEST);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

function GenereMDP($nbChar) :string{

    return "secret";
}

    function CalculComplexiteMdp($mdp): float {
        $longueur = strlen($mdp);
        $ensemble = 0;

        // Vérifie les différents types de caractères et ajuste la taille de l'ensemble
        if (preg_match('/[a-z]/', $mdp)) {
            $ensemble += 26; // Lettres minuscules
        }
        if (preg_match('/[A-Z]/', $mdp)) {
            $ensemble += 26; // Lettres majuscules
        }
        if (preg_match('/[0-9]/', $mdp)) {
            $ensemble += 10; // Chiffres
        }
        if (preg_match('/[\W_]/', $mdp)) {
            $ensemble += 32; // Caractères spéciaux (approximatif)
        }

        // Calcul de l'entropie
        $entropie = $longueur * log($ensemble, 2);

        return $entropie;
    }
