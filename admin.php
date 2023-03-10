<?php

session_start();
if(!$_SESSION["user"] || $_SESSION["user"]["type"] !== "admin"){
    header("location: login.php");
}

require_once "bddConnect.php";

// Partie pour valider les actions
if(isset($_GET["confirmeChoix"]) && !empty($_GET["confirmeChoix"])){
    $confirmeChoix = (int) $_GET['confirmeChoix'];
    $req = $connexion->prepare("UPDATE `users` SET statutChoixChanson = '1' WHERE id = ?");
    $req->execute(array($confirmeChoix));

}else if(isset($_GET["confirmeEnvoi"]) && !empty($_GET["confirmeEnvoi"])){
    $confirmeEnvoi = (int) $_GET['confirmeEnvoi'];
    $req = $connexion->prepare("UPDATE `users` SET statutEnvoiChanson = '1' WHERE id = ?");
    $req->execute(array($confirmeEnvoi));

}else if(isset($_GET["confirmePaiement"]) && !empty($_GET["confirmePaiement"])){
    $confirmePaiement = (int) $_GET['confirmePaiement'];
    $req = $connexion->prepare("UPDATE `users` SET statutPaiement = '1' WHERE id = ?");
    $req->execute(array($confirmePaiement));
}

// Partie pour annuler les actions
if(isset($_GET["annuleChoix"]) && !empty($_GET["annuleChoix"])){
    $annuleChoix = (int) $_GET['annuleChoix'];
    $req = $connexion->prepare("UPDATE `users` SET statutChoixChanson = '0' WHERE id = ?");
    $req->execute(array($annuleChoix));
}else if(isset($_GET["annuleEnvoi"]) && !empty($_GET["annuleEnvoi"])){
    $annuleEnvoi = (int) $_GET['annuleEnvoi'];
    $req = $connexion->prepare("UPDATE `users` SET statutEnvoiChanson = '0' WHERE id = ?");
    $req->execute(array($annuleEnvoi));
}else if(isset($_GET["annulePaiement"]) && !empty($_GET["annulePaiement"])){
    $annulePaiement= (int) $_GET['annulePaiement'];
    $req = $connexion->prepare("UPDATE `users` SET statutPaiement = '0' WHERE id = ?");
    $req->execute(array($annulePaiement));
}

// partie pour supprimer le choix de la chanson  
if(isset($_GET["supprimeChoix"]) && !empty($_GET["supprimeChoix"])){
    $supprimeChoix = (int) $_GET['supprimeChoix'];
    $req = $connexion->prepare("DELETE FROM `choixMusique` WHERE id_user = ?");
    $req->execute(array($supprimeChoix));
    
// partie pour supprimer la bande sonore envoy?? (?? la fois dans la bdd et aussi dans le fichier archivage)
}else if(isset($_GET["supprimeEnvoi"]) && !empty($_GET["supprimeEnvoi"])){
    $supprimeEnvoi = (int) $_GET['supprimeEnvoi'];

// PARTIE POUR SUPPRIMER LE FICHIER DANS LE REPERTOIRE ARCHIVAGE
    $query = $connexion->prepare("SELECT `ChansonEnvoy??e` FROM `choixMusique` WHERE id_user= ?");
    $query->execute(array($supprimeEnvoi));
    $result = $query->fetch();
    $CheminFichier = $result["ChansonEnvoy??e"];
    if (file_exists($CheminFichier)) {
				if (!unlink($CheminFichier)){echo "Probl??me lors de l'effacement de $CheminFichier<br />\n";}
				else {echo "Fichier $CheminFichier effac??.<br />\n";}
			}
			else {echo "Le fichier $CheminFichier n'a pas ??t?? trouv??.<br />\n";}

// PARTIE POUR UPDATE LE CHEMIN DE CHANSON ENVOYEE
    $req = $connexion->prepare("UPDATE `choixMusique` SET `ChansonEnvoy??e` = 'NULL' WHERE id_user = ?");
    $req->execute(array($supprimeEnvoi));}

// partie pour udpate donn??e choix supprim??
if(isset($_GET["supprimeChoix"]) && !empty($_GET["supprimeChoix"])){
    $supprimeChoix = (int) $_GET['supprimeChoix'];
    $req = $connexion->prepare("UPDATE `users` SET `choixSupprime` = 1, `statutChoixChanson` = 0 WHERE id = ?");
    $req->execute(array($supprimeChoix));

// partie pour udpate donn??e envoi supprim??
}else if(isset($_GET["supprimeEnvoi"]) && !empty($_GET["supprimeEnvoi"])){
    $supprimeEnvoi = (int) $_GET['supprimeEnvoi'];
    $req = $connexion->prepare("UPDATE `users` SET `envoiSupprime` = 1 WHERE id = ?");
    $req->execute(array($supprimeEnvoi));			
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Panneau </title>
</head>
<body>
<a href="logout.php">Deconnexion</a>

<h2>Table des incrits</h2>

    <table class="table table-dark">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email utilisateur</th>
                <th>Type</th>
                <th>Statut choix chanson</th>
                <th>Choix Supprim??</th>
                <th>Statut Envoi chanson</th>
                <th>Envoi Supprim??</th>
                <th>Statut Paiement</th>                                  
            </tr>
        </thead>
        <tbody>
         
         <!-- PARTIE QUI REGROUPE TOUTES LES INFOS DES MEMBRES INSCRITS SAUF LE MDP -->
        <?php 
            $requete = $connexion->query("SELECT id,username,email,type,statutChoixChanson,choixSupprime,statutEnvoiChanson,envoiSupprime,statutPaiement FROM `users` ");
            while ($data = $requete->fetch()) {
                echo "<tr>";
                echo "<td>" . $data['id'] . "</td>";
                echo "<td>" . $data["username"] . "</td>";
                echo "<td>" . $data["email"] . "</td>";
                echo "<td>" . $data["type"] . "</td>";
                echo "<td>" . $data['statutChoixChanson'] . "</td>";
                echo "<td>" . $data['choixSupprime'] . "</td>";
                echo "<td>" . $data['statutEnvoiChanson'] . "</td>";
                echo "<td>" . $data['envoiSupprime'] . "</td>";
                echo "<td>" . $data['statutPaiement'] . "</td>";
                echo "</tr>";
             };
        ?>
        </tbody>
    </table>

<h2>Table des participants</h2>
    <table class="table table-dark ">
        <thead>
            <tr style='text-align:center'>
                <th>ID</th>
                <th>Email utilisateur</th>
                <th>validation choix de la chanson</th>
                <th>Choixdeezer</th>
                <th>Validation envoi de la chanson</th>  
                <th>Fichier envoy??</th>   
                <th>Validation Paiement</th>                  
            </tr>
        </thead>
        <tbody>

 <!-- PARTIE QUI REGROUPE TOUTES LES INFOS DES MEMBRES PARTICIPANTS AU CONCOURS ET QUI PERMET DE VALIDER LES DIFFERENTES ETAPES -->

        <?php 

            $requete = $connexion->query("SELECT users.id,email,statutChoixChanson,statutEnvoiChanson,statutPaiement,artiste,titre,img,ChansonEnvoy??e FROM `users` INNER JOIN `choixMusique` ON users.id = choixMusique.id_user");
           
            while ($data = $requete->fetch()) {
                echo "<tr>";
                echo "<td>" . $data['id'] . "</td>";
                echo "<td>" . $data["email"] . "</td>";

                // on v??rifie si le statut pour le choix de la chanson n'est pas d??j?? diff??rent de 1 qui est valid??
                if($data['statutChoixChanson'] === 0){
                 echo "<td><a href='admin.php?confirmeChoix=". $data['id'] ."' >Valider choix </a></td>";}
                // Si le choix est valid?? on affiche le message 'Choix de la chanson valid??' avec une possibilit?? d'annul?? en cas d'erreur
                else if($data['statutChoixChanson'] === 1){
                 echo "<td style='color:red;fontSize:1.5em;'>Choix de la chanson valid?? ou <br> <a href='admin.php?annuleChoix=". $data['id'] ."' >Annuler</a></td>";
                     } 
                // Partie pour supprimer le choix du participant en cas de non-conformit?? ou erreur
                echo "<td style='text-align:center'> <img style='width:200px' src='" .$data["img"]. "' <br><br>" .$data["artiste"]. "<br>" .$data["titre"] ." <br>
                <a href='admin.php?supprimeChoix=" .$data['id'] . "'>Supprimer</a></td>";

                 // on v??rifie si le statut pour l'envoi de la chanson n'est pas d??j?? diff??rent de 1 et que le choix de la chanson est valid?? (1)
                if($data['statutEnvoiChanson'] === 0 && $data['statutChoixChanson'] === 1 ){
                 echo "<td> <a href='admin.php?confirmeEnvoi=". $data['id'] ."' >Valider choix </a></td>";

                        // si le choix de la chanson n'a pas encore ??t?? valid?? on affiche le message 'Attente validation choix de la chanson'
                }else if($data['statutChoixChanson'] === 0){ 
                 echo "<td> Attente validation choix de la chanson </td>";

                    // si le choix de la chanson et l'envoi est valid?? on affiche le message 'Chanson receptionn??e et valid??' avec une possibilit?? d'annul?? en cas d'erreur
                }else{
                 echo "<td style='color:red;fontSize:1.5em;'>Chanson r??ceptionn?? et valid?? ou <br> <a href='admin.php?annuleEnvoi=". $data['id'] ."'>Annuler</a></td>";         
                   }

                // Partie pour supprimer la chanson envoy??e par le participant en cas de non-conformit?? ou erreur
                echo "<td><audio controls='controls'> <source src=". $data["ChansonEnvoy??e"] . "></audio> <br>";
                 if($data['ChansonEnvoy??e'] !== 'NULL'){ echo "<a href='admin.php?supprimeEnvoi=" .$data['id'] . "'>Supprimer</a>";}"</td>";    
                // on v??rifie si le statut le paiement de l'inscription n'est pas d??j?? diff??rent de 1 ,que le choix de la chanson est valid?? (1) et que l'envoi est valid?? (1)
                
                if($data['statutEnvoiChanson'] === 1 && $data['statutChoixChanson'] === 1  && $data['statutPaiement'] === 0){
                    echo "<td> <a href='admin.php?confirmePaiement=". $data['id'] ."' >Valider choix </a></td>";

                   // si le choix de la chanson ou l'envoi de la chanson n'est pas valid?? on affiche le message ' Attente validation choix de la chanson et/ou envoi de la chanson'
                }else if($data['statutChoixChanson'] === 0 ||  $data['statutEnvoiChanson'] === 0){ 
                echo "<td> Attente validation choix de la chanson et/ou envoi de la chanson </td>";

                // si le paiement est valid?? on affiche  'Paiement effectu?? et valid??' avec une possibilit?? d'annul?? en cas d'erreur
                }else{ echo "<td style='color:red;fontSize:1.5em;'>Paiement effectu?? et valid?? ou <br> <a href='admin.php?annulePaiement=". $data['id'] ."' >Annuler</a></td>";         
                     }
                echo "</tr>";
             }
        ?>
        </tbody> 
          </table>
</body>
</html>

