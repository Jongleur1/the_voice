<?php
session_start();
if(!$_SESSION["user"]){
    header("location: connexion.php");
}

$iduser = $_SESSION["user"]["id"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <nav>
        <li><a href='choixChanson.php'>Choix de la chanson</a></li>
        <li><a href='envoifichier.php'>Envoi du fichier</a></li>
        <li><a href="logout.php">Deconnexion</a></li>
    </nav>
    <h1>HELLO <?= $_SESSION["user"]["username"] ?></h1>
    
<?php
require_once 'bddConnect.php';
$req = $connexion->prepare("SELECT `statutChoixChanson`,`statutEnvoiChanson`,`statutPaiement` FROM `users`  WHERE id = $iduser");
$req->execute();
$result = $req->fetch();

if($result["statutEnvoiChanson"] === 1 && $result["statutChoixChanson"] === 1 && $result["statutPaiement"] === 1){
    echo "<h3> Félicitation votre paiement a bien été receptionné </h3> 
    <p> Veuillez cliquer sur ce <a href='#'> lien <a/> pour télécharger votre facture </p>";    


}else if($result["statutEnvoiChanson"] === 1 && $result["statutChoixChanson"] === 1){

echo "<h3> Félicitation votre bande sonore a été validé.</h3> 
<p> Veuillez nous envoyer votre paiement pour finaliser votre inscription </p>";
} ?>
    
</body>
</html>