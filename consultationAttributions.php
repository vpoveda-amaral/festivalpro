<!DOCTYPE html>
<html>
<head>
   <title>Festival - Attributions des chambres</title>
</head>
<body>

</body>
</html>
<?php //ICI

include("_debut.inc.php");
include("_gestionBase.inc.php"); 
include("_controlesEtGestionErreurs.inc.php");

// CONNEXION AU SERVEUR MYSQL PUIS SÉLECTION DE LA BASE DE DONNÉES festival

$connexion=connect();
/*if (!$connexion)
{
   ajouterErreur("Echec de la connexion au serveur MySql");
   afficherErreurs();
   exit();
}*/

echo "<br><br>";
// CONSULTER LES ATTRIBUTIONS DE TOUS LES ÉTABLISSEMENTS

// IL FAUT QU'IL Y AIT AU MOINS UN ÉTABLISSEMENT OFFRANT DES CHAMBRES POUR  
// AFFICHER LE LIEN VERS LA MODIFICATION
$nbEtab=obtenirNbEtabOffrantChambres($connexion);
if ($nbEtab!=0)
{
   echo "
   <table width='75%' cellspacing='0' cellpadding='0' align='center'>
   <tr>
      <tr><td>
      <td  align='center'>
      <a href='modificationAttributions.php?action=demanderModifAttrib'>
      Effectuer ou modifier les attributions</a>
      </td>
   </tr>
   </table>
   <br><br>";
   
   // POUR CHAQUE ÉTABLISSEMENT : AFFICHAGE D'UN TABLEAU COMPORTANT 2 LIGNES 
   // D'EN-TÊTE ET LE DÉTAIL DES ATTRIBUTIONS
    $req=obtenirReqEtablissementsAyantChambresAttribuées();
   $rsEtab=$connexion->query($req);
   $lgEtab=$rsEtab->fetchALL(PDO::FETCH_ASSOC);
   foreach ($lgEtab as $row)
   {
      $idEtab=$row['id'];
      $nomEtab=$row['nom'];
   
      echo "
      <table width='75%' cellspacing='0' cellpadding='0' align='center' 
      class='tabQuadrille'>";
      
      $nbOffre=$row["nombreChambresOffertes"];
      $nbOccup=obtenirNbOccup($connexion, $idEtab);
      // Calcul du nombre de chambres libres dans l'établissement
      $nbChLib = $nbOffre - $nbOccup;
      
      // AFFICHAGE DE LA 1ÈRE LIGNE D'EN-TÊTE 
      echo "
      <tr class='enTeteTabQuad'>
         <td colspan='2' align='left'><strong>$nomEtab</strong>&nbsp;
         (Offre : $nbOffre&nbsp;&nbsp;Disponibilités : $nbChLib)
         </td>
      </tr>";
          
      // AFFICHAGE DE LA 2ÈME LIGNE D'EN-TÊTE 
      echo "
      <tr class='ligneTabQuad'>
         <td width='65%' align='left'><i><strong>Nom groupe</strong></i></td>
         <td width='35%' align='left'><i><strong>Chambres attribuées</strong></i>
         </td>
      </tr>";
        
      // AFFICHAGE DU DÉTAIL DES ATTRIBUTIONS : UNE LIGNE PAR GROUPE AFFECTÉ 
      // DANS L'ÉTABLISSEMENT       
       $req=obtenirReqGroupesEtab($idEtab);
      $rsEquipe=$connexion->query($req);
      $lgEquipe=$rsEquipe->fetchALL(PDO::FETCH_ASSOC);
      foreach($lgEquipe as $row)
      {
         $idGroupe=$row['id'];
         $nomGroupe=$row['nom'];
         $nomPays=$row['nomPays'];
         echo "
         <tr class='ligneTabQuad'>
            <td width='65%' align='left'>$nomGroupe ($nomPays)</td>";
         // On recherche si des chambres ont déjà été attribuées à ce groupe
         // dans l'établissement
         $nbOccupGroupe=obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe);
         echo "
            <td width='35%' align='left'>$nbOccupGroupe</td>
         </tr>";
         
      } // Fin de la boucle sur les groupes
      
      echo "
      </table><br>";
   
   } // Fin de la boucle sur les établissements
}

?>
