<?php



namespace App\Modele;

use App\Utilitaire\Singleton_ConnexionPDO;
use PDO;


class Modele_Jeton
{
    /**
     * @param $connexionPDO : connexion à la base de données
     * @return mixed : le tableau des étudiants ou null (something went wrong...)
     */
    public static function insertToken(int $idUtilisateur, $codeAction = 1)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $date = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $octetsAleatoires = openssl_random_pseudo_bytes(256);
        $jeton = sodium_bin2base64($octetsAleatoires, SODIUM_BASE64_VARIANT_ORIGINAL);
        $requetePreparee = $connexionPDO->prepare(
            'INSERT INTO token (valeur, codeAction, idUtilisateur, dateFin) 
         VALUES ( :valeur, :codeAction, :idUtilisateur, :dateFin);');
        $requetePreparee->bindValue(':valeur', $jeton);
        $requetePreparee->bindValue(':codeAction', $codeAction);
        $requetePreparee->bindValue(':idUtilisateur', $idUtilisateur);
        $requetePreparee->bindValue(':dateFin', $date);
        if ($requetePreparee->execute()) {
            return $jeton;

        }
        throw new \Exception("Non");
    }

}