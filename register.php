<?php
session_start();
if($_SESSION["user"]){
    header("location: index.php");
}
if(!empty($_POST)){
    if(isset($_POST["username"],$_POST["email"],$_POST["password"]) && !empty($_POST["username"])&& !empty($_POST["email"]) && !empty($_POST["password"])){
      
        //Protection username + tout en minuscule sauf la premier lettre
        $username = strip_tags(strtolower(ucfirst($_POST["username"])));

        // protection email
        $email = strip_tags(strtolower($_POST["email"]));
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                die("Erreur l'email n'est pas valide !! ");
        };

        // on vérifie si l'adresse n'est pas déjà dans la bdd
        require "bddConnect.php";
        $requete = $connexion->prepare("SELECT * FROM `users` WHERE email = :email");
        $requete-> bindValue(":email",$email,PDO::PARAM_STR);
        $requete->execute();
        $user = $requete->fetch();
        if($user){
            die ("L'email ou l'username existe déjà !!");
        }

        //hashage du mdp
        $password = password_hash($_POST["password"],PASSWORD_ARGON2ID);

        // enregistrement des informations dans la base de données
        
        $sql = ("INSERT INTO `users` (`username`,`email`,`type`,`password`,`statutChoixChanson`,`choixSupprime`,`statutEnvoiChanson`,`envoiSupprime`,`statutPaiement`) VALUES(:username,:email,'user','$password', 0 , 0 , 0,0,0)");
        $requete = $connexion->prepare($sql);
        $requete->bindValue(":username",$username);
        $requete->bindValue(":email",$email);
        $requete->execute();
        var_dump($user);
       
    }else{
        die("Le formulaire n'est pas correctement remplie");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
    <form action="#" method="post">
        <h1>Inscription</h1>
        <div class="row">
           
                <label class="form-label" for="nom">Nom d'utilisateur</label>
                <input class="form-control" type="text" name="username" id="username">
           

           
                <label class="form-label" for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" >
           
                <label class="form-label" for="password">Mot de passe</label>
                <input class="form-control" type="password" name="password" id="password">
            
              
                <input class="form-control btn btn-primary mx5" type="submit" value="Valider incription">
            </div>
        
    </form>
    </div>
</body>

</html>