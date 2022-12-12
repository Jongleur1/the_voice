<?php
session_start();
if(!$_SESSION["user"]){
    header("location: login.php");
    exit;
}
 // récupération id de l'utilisateur
 $iduser = $_SESSION["user"]["id"];

var_dump($_POST);
require_once "bddConnect.php";
    // $requete = $connexion->prepare("SELECT * FROM `users` WHERE id = $iduser");
    // $requete->execute();
    // $user = $requete->fetch();
    // if($user["statutChoixChanson"] === 1){
    //     header("location: index.php");
    // }

if(!empty($_POST)){
    if(isset($_POST["artiste"],$_POST["titre"],$_POST["chanson"]) && !empty($_POST["chanson"]) && !empty($_POST["artiste"]) && !empty($_POST["titre"])){
        // insertion du nom de l'artiste
        $artiste = ucfirst(strip_tags(strtolower($_POST["artiste"])));
       // insertion du titre de la chanson
        $titre = ucfirst(strip_tags(strtolower($_POST["titre"])));
        
        // insertion de la chanson choisie
        $chansonChoisie = strip_tags($_POST["chanson"]);

        // Vérif si chanson déjà envoyé
       
        $req = $connexion->prepare("SELECT `choix` FROM `choixMusique` WHERE id_user = $iduser");
        $req->execute();
        $musiquePresente = $req->fetch();
        if($musiquePresente){
            die("Chanson déjà selectionné, veuillez attendre que l'admin valide votre choix");
        } 
            
        $sql = "INSERT INTO `choixMusique` (`id_user`,`artiste`,`titre`,`choix`,`chansonEnvoyée`) VALUES( $iduser ,:artiste,:titre,:choix,'NULL') ";
        $requete = $connexion->prepare($sql);
        $requete->bindValue(":artiste",$artiste,PDO::PARAM_STR);
        $requete->bindValue(":titre",$titre,PDO::PARAM_STR);
        $requete->bindValue(":choix",$chansonChoisie,PDO::PARAM_STR);
        $requete->execute();
        
        $req = $connexion->prepare("UPDATE `users` SET `choixSupprime` = 0  WHERE id = $iduser");
        $req->execute();
        
    }else{
        echo "Erreur, veuillez selectionner une chanson";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="app.js" defer></script>
    <title>Document</title>
</head>
<body>
<a href="logout.php">Deconnexion</a>
<p>hello</p>
    <h1>hello <?= $_SESSION["user"]["username"] ?> </h1>


<?php 
    $req = $connexion->prepare("SELECT `choixSupprime`,`statutChoixChanson`FROM `users` WHERE id = $iduser");
    $req->execute();
    $etatChoix = $req->fetch();


    $req = $connexion->prepare("SELECT `choix`FROM `choixMusique` WHERE id_user = $iduser");
    $req->execute();
    $musiquePresente = $req->fetch();
   
    if($musiquePresente["choix"] !==NULL && $etatChoix["statutChoixChanson"] === 0){ ?>
    <h3>Votre musique est en cours de validation ...</h3>
    <p>Un admin va étudier votre demande, revenez ultérieurement pour vérifier l'avancé de votre demande.</p>
   <?php }else if($etatChoix["statutChoixChanson"] === 1 && $musiquePresente != NULL ){?>
     
    <h3>Félicitation votre chanson a été validé</h3>
    <p>Cliquer sur ce <a href="envoifichier.php">lien</a> pour nous envoyé votre bande son  et continuer votre processus d'inscription ! </p>
    
    <?php } ?>      
                 


<?php
if($etatChoix["choixSupprime"] === 1 && !$musiquePresente){ ?>

          <h3 style="color:red">Votre chanson n'a pas été validé, veuillez en selectionner une autre. </h3>
     
          <div class="container">
             <form method="POST" action="#">
                    <label class="form-label" for="artiste">Artiste</label>
                    <input type="text" name="artiste" id="artiste">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre">
                    <input type="text" name="chanson" id="chanson">
                    <!-- <select name="chanson" id="search"></select>                     -->
                    <button type="submit">Valider</button>
            </form>  
            </div>
    <?php }else if($etatChoix["choixSupprime"] === 0 && !$musiquePresente){ ?>

        <h3>Choisi la chanson que tu souhaites chanter ! </h3>
     
     <div class="container">
        <form method="POST" action="#">
               <label class="form-label" for="artiste">Artiste</label>
               <input type="text" name="artiste" id="artiste">
               <label for="titre">Titre</label>
               <input type="text" name="titre" id="titre">
               <input type="text" name="chanson" id="chanson">
               <!-- <select name="chanson" id="search"></select>                -->
               <button type="submit">Valider</button>
       </form>  
       </div>
       <?php } ?>

    
</body> 
</html>
 
