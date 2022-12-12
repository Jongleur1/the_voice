<?php
define("server","mysql:host=localhost;dbname=concoursChant");
define("login","justinD");
define("pass","chajuoli");

try{
    $connexion = new PDO(server,login,pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
       
}
catch(PDOException $e){
    die("Erreur de connexion" .$e->getMessage());
}