<?php

use App\Modele\Modele_Entreprise;
use App\Modele\Modele_Salarie;
use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_Mail_Confirme;
use App\Vue\Vue_Mail_ReinitMdp;
use App\Vue\Vue_Mail_ReinitMdpToken;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;
use App\Vue\Vue_Mail_ChoisirNouveauMdp;

use PHPMailer\PHPMailer\PHPMailer;
//Ce contrôleur gère le formulaire de connexion pour les visiteurs

$Vue->setEntete(new Vue_Structure_Entete());

switch ($action) {
    case "reinitmdpconfirm":

          //comme un qqc qui manque... je dis ça ! je dis rien !
        $nouveauMDP=App\Fonctions\passgen1(10);
        App\Fonctions\envoyerMail($nouveauMDP);
        App\Modele\Modele_Utilisateur::Utilisateur_Modifier_motDePasse(App\Modele\Modele_Utilisateur::Utilisateur_Select_ParLogin($_POST["email"])["idUtilisateur"],$nouveauMDP);
        $Vue->addToCorps(new Vue_Mail_Confirme());
        $_SESSION["reinitMDP"]=true;
        break;
    case "reinitmdpconfirmtoken":

          //comme un qqc qui manque... je dis ça ! je dis rien !
        $token=App\Modele\Modele_Jeton::insertToken(App\Modele\Modele_Utilisateur::Utilisateur_Select_ParLogin($_POST["email"])["idUtilisateur"]);
        App\Fonctions\envoyerMailToken(\App\Modele\Modele_Jeton::researchToken($token)["valeur"]);
//        App\Modele\Modele_Utilisateur::Utilisateur_Modifier_motDePasse(App\Modele\Modele_Utilisateur::Utilisateur_Select_ParLogin($_POST["email"])["idUtilisateur"],$nouveauMDP);
        $Vue->addToCorps(new Vue_Mail_Confirme());
        $_SESSION["reinitMDP"]=true;
        $_SESSION["token"]=$token;
        break;
    case "reinitmdp":


        $Vue->addToCorps(new Vue_Mail_ReinitMdp());

        break;
    case "reinitmdptoken":


        $Vue->addToCorps(new Vue_Mail_ReinitMdpToken());

        break;
    case "token":
        $token=isset($_GET["token"])?$_GET["token"]:"";
        $Vue->addToCorps(new Vue_Mail_ChoisirNouveauMdp($token));
        break;
    case "choixmdp" :
        $token=isset($_GET["token"])?$_GET["token"]:"";
        if($_POST["mdp1"]===$_POST["mdp2"]){
            Modele_Utilisateur::Utilisateur_Modifier_motDePasse(\App\Modele\Modele_Jeton::researchToken($token)["idUtilisateur"],$_POST["mdp1"]);
        }
    case "Se connecter" :
        if (isset($_REQUEST["compte"]) and isset($_REQUEST["password"])) {
            //Si tous les paramètres du formulaire sont bons

            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($_REQUEST["compte"]);

            if ($utilisateur != null) {
                //error_log("utilisateur : " . $utilisateur["idUtilisateur"]);
                if ($utilisateur["desactiver"] == 0) {
                    if ($_REQUEST["password"] == $utilisateur["motDePasse"]) {
                        if (isset($_SESSION['reinitMDP']) && $_SESSION['reinitMDP'] === true) {
                            $Vue->addToCorps(new \App\Vue\Vue_Utilisateur_Changement_MDP());

                            return;
                        }
                        $_SESSION["idUtilisateur"] = $utilisateur["idUtilisateur"];
                        //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                        $_SESSION["idCategorie_utilisateur"] = $utilisateur["idCategorie_utilisateur"];
                        //echo "idCategorie_utilisateur : " . $_SESSION["idCategorie_utilisateur"];
                        //error_log("idCategorie_utilisateur : " . $_SESSION["idCategorie_utilisateur"]);
                      //  var_dump($utilisateur);

                        if(Modele_Utilisateur::Utilisateur_Select_RGPD($_SESSION["idUtilisateur"])==0) {
                            include "./Controleur/Controleur_AccepterRGPD.php";
                        }
                        else
                            switch ($utilisateur["idCategorie_utilisateur"]) {
                                case 1:
                                    $_SESSION["typeConnexionBack"] = "administrateurLogiciel"; //Champ inutile, mais bien pour voir ce qu'il se passe avec des étudiants !
                                    $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                    break;
                                case 2:
                                    $_SESSION["typeConnexionBack"] = "gestionnaireCatalogue";
                                    $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                    $Vue->addToCorps(new \App\Vue\Vue_AfficherMessage("Bienvenue " . $_REQUEST["compte"]));
                                    break;
                                case 3:
                                    $_SESSION["typeConnexionBack"] = "entrepriseCliente";
                                    //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                                    $_SESSION["idEntreprise"] = Modele_Entreprise::Entreprise_Select_Par_IdUtilisateur($_SESSION["idUtilisateur"])["idEntreprise"];
                                    include "./Controleur/Controleur_Gerer_Entreprise.php";
                                    break;
                                case 4:
                                    $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                                    $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                    $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                    include "./Controleur/Controleur_Catalogue_client.php";
                                    break;
                                case 5:
                                    $_SESSION["typeConnexionBack"] = "commercialCafe";
                                    $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                    break;
                            }
                    } else {//mot de passe pas bon
                        $msgError = "Mot de passe erroné";

                        $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                    }
                } else {
                    $msgError = "Compte désactivé";

                    $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                }
            } else {
                $msgError = "Identification invalide";

                $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
            }
        } else {
            $msgError = "Identification incomplete";

            $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
        }
    break;
    default:

        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());

        break;
}


$Vue->setBasDePage(new Vue_Structure_BasDePage());