<?php
session_start();
if($_SESSION["user"]){
    header("location: index.php");
}
if(!empty($_POST)){
    if(isset($_POST["email"],$_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])){
    
        

    // Vérification si c'est bien un email
        if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
        die("Erreur de l'email!!");
      }

    //Vérification si l'email existe

        require "bddConnect.php";
        $requete = $connexion->prepare("SELECT * FROM `users` WHERE email = :email");
        $requete->bindParam(":email",$_POST["email"]);
        $requete-> execute();
        $user = $requete->fetch();
        if(!$user || !password_verify($_POST["password"],$user["password"])){
            die("erreur de l'email et/ou du mot de passe");
        }

        if($user["type"] === "admin"){
            header("location: admin.php");
            $_SESSION["user"] = [
                "id" => $user["id"],
                "username" => $user["username"],
                "type" => $user["type"]
            ];
        }else{
            header("location: index.php");
            $_SESSION["user"] = [
                "id" => $user["id"],
                "username" => $user["username"],
                "type" => $user["type"],
                "statutChoixChanson" => $user["statutChoixChanson"],
                "statutEnvoiChanson" => $user["statutEnvoiChanson"],
                "statutPaiement" => $user["statutPaiement"],
            ];

        }


    }else{
        die("Les champs ne sont pas correctement remplis !");
    }
}      
?>


<!DOCTYPE html>
<html lang="fr">

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
                
                <label class="form-label" for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" >

                <label class="form-label" for="password">Mot de passe</label>
                <input class="form-control" type="password" name="password" id="password">

                <input class="form-control btn btn-primary mx5" type="submit" value="Connexion">
            </div>

        </form>
    </div>

</body>

</html>