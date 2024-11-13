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

function CalculComplexiteMdp($mdp) : int {
        $complexite = 0;

        // Vérifie la longueur du mot de passe
        $longueur = strlen($mdp);
        if ($longueur >= 8) {
            $complexite += 2;
        }
        if ($longueur >= 12) {
            $complexite += 2;
        }

        // Vérifie la présence de majuscules
        if (preg_match('/[A-Z]/', $mdp)) {
            $complexite += 2;
        }

        // Vérifie la présence de minuscules
        if (preg_match('/[a-z]/', $mdp)) {
            $complexite += 2;
        }

        // Vérifie la présence de chiffres
        if (preg_match('/[0-9]/', $mdp)) {
            $complexite += 2;
        }

        // Vérifie la présence de caractères spéciaux
        if (preg_match('/[\W_]/', $mdp)) {
            $complexite += 2;
        }

        return $complexite;
    }
