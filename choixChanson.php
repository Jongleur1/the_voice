<?php
session_start();
if(!$_SESSION["user"]){
    header("location: login.php");
    exit;
}

 // récupération id de l'utilisateur
 $iduser = $_SESSION["user"]["id"];

require_once "bddConnect.php";
    // $requete = $connexion->prepare("SELECT * FROM `users` WHERE id = $iduser");
    // $requete->execute();
    // $user = $requete->fetch();
    // if($user["statutChoixChanson"] === 1){
    //     header("location: index.php");
    // }

if(!empty($_GET)){
    if(isset($_GET["artiste"],$_GET["titre"],$_GET["img"]) && !empty($_GET["artiste"]) && !empty($_GET["titre"]) && !empty($_GET["img"])){
        // insertion du nom de l'artiste
        $artiste = ucfirst(strip_tags(strtolower($_GET["artiste"])));
       // insertion du titre de la chanson
        $titre = ucfirst(strip_tags(strtolower($_GET["titre"])));
                
        // Vérif si chanson déjà envoyé
       
        $req = $connexion->prepare("SELECT `img` FROM `choixMusique` WHERE id_user = $iduser");
        $req->execute();
        $musiquePresente = $req->fetch();
        if($musiquePresente){
            die("Chanson déjà selectionné, veuillez attendre que l'admin valide votre choix");
        } 
            
        $sql = "INSERT INTO `choixMusique` (`id_user`,`artiste`,`titre`,`img`,`chansonEnvoyée`) VALUES( $iduser ,:artiste,:titre,:img,'NULL') ";
        $requete = $connexion->prepare($sql);
        $requete->bindValue(":artiste",$artiste,PDO::PARAM_STR);
        $requete->bindValue(":titre",$titre,PDO::PARAM_STR);
        $requete->bindValue(":img",$_GET["img"]);
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


    $req = $connexion->prepare("SELECT `img` FROM `choixMusique` WHERE id_user = $iduser");
    $req->execute();
    $musiquePresente = $req->fetch();
   
    if($musiquePresente["img"] !==NULL && $etatChoix["statutChoixChanson"] === 0){ ?>
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
                  
            <label for="artiste">Artiste</label>
            <input type="text" name="artiste" id="artiste">
            <label for="titre">Titre</label>
            <input type="text" name="titre" id="titre">
            <button type="submit">Rechercher</button>

            </form>  
            </div>
    <?php }else if($etatChoix["choixSupprime"] === 0 && !$musiquePresente){ ?>

        <h3>Choisi la chanson que tu souhaites chanter ! </h3>
     
     <div class="container">

        <form method="POST" action="#">        
        <label for="artiste">Artiste</label>
        <input type="text" name="artiste" id="artiste">
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre">
        <button type="submit">Rechercher</button>
 
       </form>  
       </div>
       <?php } ?>

       <?php 
$url = "https://shazam.p.rapidapi.com/search?term=".str_replace(' ', '%20',$_POST['titre'])."%20".str_replace(' ', '%20',$_POST['artiste'])."&locale=fr-FR&offset=0&limit=5";
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: shazam.p.rapidapi.com",
        "X-RapidAPI-Key: 5a0bb38e4emsh68c798a81b128acp1f9d4bjsn1e92f0153710"
    ],
]);

$response = curl_exec($curl);
$parsee=json_decode(curl_exec($curl), true);
$data = $parsee["tracks"]["hits"];

foreach($data as $dat){
    $artiste = $dat["track"]["subtitle"];
    $titre = $dat["track"]["title"];
    $img = $dat["track"]["share"]["image"];
    $extrait = $dat["track"]["hub"]["actions"]["1"]["uri"];
    
    echo
    "
    <div class='text-center my-3'>
    <img src='$img'  style='width:200px' class='card-img-top'>
    <div class='card-body '>
    <h5 style='margin:0' name ='artiste' class='card-title  '>$artiste</h5>
    <p class='card-text'> $titre</p>
    <audio controls='controls'> <source src='$extrait'></audio><br>
    <a href='choixChanson.php?img=".$img."&titre=".$titre."&artiste=".$artiste."' class='btn btn-primary'>Valider</a>
    </div>
    </div>
    </div>
    ";
}
$err = curl_error($curl);

curl_close($curl);

?>


</body> 
</html>
 
