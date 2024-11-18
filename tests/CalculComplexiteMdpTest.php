<?php
use PHPUnit\Framework\TestCase;
use function App\Fonctions\CalculComplexiteMdp;

require_once ("src/Fonctions/fonctions.php");
class CalculComplexiteMdpTest extends TestCase
{
    public function testComplexite20()
    {
        // Mot de passe court sans diversité
        $this->assertEquals(20, \App\Fonctions\CalculComplexiteMdp("aubry")); // Complexité faible (longueur seulement)
    }

    public function testComplexiteMoyenne()
    {
        // Mot de passe avec majuscule, minuscule et longueur suffisante
        $this->assertEquals(51, \App\Fonctions\CalculComplexiteMdp("super@ubry")); // Longueur et majuscules/minuscules
    }

    public function testComplexite86()
    {
        // Mot de passe avec chiffres
        $this->assertEquals(8, \App\Fonctions\CalculComplexiteMdp("Super@ubry2022")); // Ajout des chiffres
    }

    public function testComplexiteAvecCaracteresSpeciaux()
    {
        // Mot de passe avec caractères spéciaux
        $this->assertEquals(140, \App\Fonctions\CalculComplexiteMdp("Giroud-Président||2027")); // Chiffres et caractères spéciaux
    }


}
echo CalculComplexiteMdp("aubry");