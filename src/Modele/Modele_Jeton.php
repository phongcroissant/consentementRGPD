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
    public static function updateToken($jeton)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('
            UPDATE token 
            SET codeAction = 0 
            WHERE valeur = :valeurJeton AND dateFin > NOW()'
        );

        $requetePreparee->bindParam(':valeurJeton', $jeton);

        if (!$requetePreparee->execute()) {
            throw new \Exception("Échec de la mise à jour du jeton.");
        }
    }

    // Méthode Search pour rechercher un jeton selon sa valeur
    public static function researchToken($jeton)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('
            SELECT * FROM token 
            WHERE valeur = :valeurJeton AND dateFin > NOW()'
        );

        $requetePreparee->bindParam(':valeurJeton', $jeton);
        $requetePreparee->execute();

        return $requetePreparee->fetch(PDO::FETCH_ASSOC);
    }
}