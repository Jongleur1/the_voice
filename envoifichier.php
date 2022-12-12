<?php

// !empty($_post) permet de pouvoir lancer la page sinon cela afficherai une erreur
session_start();
if(!$_SESSION["user"]){
    header("location: connexion.php");
}

$iduser = $_SESSION["user"]["id"];
require_once "bddConnect.php";
$req = $connexion->prepare("SELECT `statutChoixChanson`,`statutEnvoiChanson`,`envoiSupprime` FROM `users` WHERE id=$iduser");
$req->execute();
$infos = $req->fetch();

if($infos["statutChoixChanson"]=== 1 && $infos["statutEnvoiChanson"]=== 0){
 if(!empty($_POST)){
    if (isset($_FILES["fichier"], $_POST["nom"]) && $_FILES["fichier"]["error"] === 0 && !empty($_FILES["fichier"]) && !empty($_POST["nom"])) {
        
        // On protége le nom envoyé
        $newname = strtolower(strip_tags($_POST["nom"]));
        $newnamenettoye = str_replace(' ', '-', $newname);
        
        // on a reçu le fichier
        // on procéde aux vérifications
        // on vérifie toujours l'extension et le type MIME
        $allowed = [
            "mp3" => "audio/mpeg",
        ];

        $filename = $_FILES["fichier"]["name"];
        $filetype = $_FILES["fichier"]["type"];
        $filesize = $_FILES["fichier"]["size"];

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        // on vérifie l'absence de l'extension dans la ou les clés de $allowed ou l'absence du type MIME
        // la fonction array_key_exists vérifie si la clés mp3 existe dans le fichier extension
        // in_array vérifie si le type et présent dans le tableau allowed
        if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed))
            // ici soit l'extension soit le type est incorrect ou les deux
            die("Le fichier sélectionné n'est pas au bon format !");

        //Ici le type est correct
        // Limite de taille 5mo
        // if($filesize > 5242880){
        //     die("Le fichier sélectionné est trop volumineux!");
        // }
        // on génére un nom
        // $newname = md5(uniqid());
        //on génére le chemin complet
        $newfilename = "archivage/$newnamenettoye.$extension";
        if (!move_uploaded_file($_FILES["fichier"]["tmp_name"],$newfilename)) {
            die("L'upload a échoué");
        }
        // premier chiffre aprés le zéro "le propiétaire",le deuxième chiffre c'est le groupe,le troisième chiffre c'est "le visiteur" , 0644 c'est 6 lecture ecriture et 4 lecture seulement
        chmod($newfilename, 0644);
        require "bddConnect.php";
        $sql = "UPDATE`choixMusique` SET `ChansonEnvoyée` = :url  WHERE `id_user` = $iduser";
        $requete = $connexion->prepare($sql);
        $requete->bindValue(":url", $newfilename);
        $requete->execute();

        $req = $connexion->prepare("UPDATE `users` SET `envoiSupprime` = 0 WHERE id = $iduser");
        $req->execute();

    }else{
    die("Les champs ne sont pas correctement remplies!");
    }

 }
    
}else if($infos["statutChoixChanson"]=== 1 && $infos["statutEnvoiChanson"]=== 1){
    header("location: index.php");
}else{
    header("location: choixChanson.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<title></title>
</head>

<body>
   
<a href="logout.php">Deconnexion</a>
<?php 
$req = $connexion->prepare("SELECT `ChansonEnvoyée` FROM `choixMusique`  WHERE id_user = $iduser");
$req->execute();
$result = $req->fetch();
$info = $result["ChansonEnvoyée"];

if($info != 'NULL'){
    echo "<h3> Votre chanson a bien été transmise, l'administrateur va la controler et vous pourrez passer à l'étape suivante si votre chanson est conforme</h3>";
}elseif($infos["envoiSupprime"] === 1){ ?>

    <p style="color:red">Votre chanson n'est pas conforme, veuillez en envoyer une autre</p>
    <form class="row g-3 py-5" action="#" method="post" enctype="multipart/form-data">

        <div class="col-auto ">
            <label class="form-label  " for="nom">Nom du fichier</label>
        </div>
        <div class="col-auto">
            <input class="form-control" type="text" name="nom" id="nom">
        </div>
        <div class="col-auto">
            <input class="form-control" type="file" name="fichier" id="fichier" accept="audio/mp3">
        </div>
        <button class="col-auto btn btn-primary" name="submit" type="submit">Valider</button>
    </form>

  <?php }else{ ?>
    <form class="row g-3 py-5" action="#" method="post" enctype="multipart/form-data">

<div class="col-auto ">
    <label class="form-label  " for="nom">Nom du fichier</label>
</div>
<div class="col-auto">
    <input class="form-control" type="text" name="nom" id="nom">
</div>
<div class="col-auto">
    <input class="form-control" type="file" name="fichier" id="fichier" accept="audio/mp3">
</div>
<button class="col-auto btn btn-primary" name="submit" type="submit">Valider</button>
</form>

<?php } ?>
    
</body>
</html>