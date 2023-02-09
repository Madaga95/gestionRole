<?php
session_start();

define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http").
"://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));

require_once("./controllers/Visiteur/Visiteur.controller.php");
require_once("./controllers/Securite.class.php");
require_once("./controllers/Toolbox.class.php");
require_once("./controllers/Utilisateur/Utilisateur.controller.php");
$visiteurController = new VisiteurController();
$utilisateurController = new UtilisateurController();

try {
    if(empty($_GET['page'])){
        $page = "accueil";
    } else {
        $url = explode("/", filter_var($_GET['page'],FILTER_SANITIZE_URL));
        $page = $url[0];
    }

    switch($page){
        case "accueil" : $visiteurController->accueil();
        break;
        case "login" : $visiteurController->login();
        break;
        case "validation_login" :
            if(!empty($_POST['login']) && !empty($_POST['password'])){
                $login = Securite::secureHTML($_POST['login']);
                $password = Securite::secureHTML($_POST['password']);
                $utilisateurController->validation_login($login, $password);
            }else{
                Toolbox::ajouterMessageAlerte("Login ou mot de passe non renseigné", Toolbox::COULEUR_ROUGE);
                // effectuer redirection vers la page de login
                header('Location: '.URL.'login');
            }
        break;
        case "creerCompte" : $visiteurController->creerCompte();
        break;
        case "validation_creerCompte" : 
            if(!empty($_POST['login']) && !empty($_POST['mail']) && !empty($_POST['password'])){
                $login = Securite::secureHTML($_POST['login']);
                $mail = Securite::secureHTML($_POST['mail']);
                $password = Securite::secureHTML($_POST['password']);
                $utilisateurController->validation_creerCompte($login, $mail, $password);
            }else{
                Toolbox::ajouterMessageAlerte("Tous les champs doivent être renseignés", Toolbox::COULEUR_ROUGE);
                // effectuer redirection vers la page de création de compte
                header('Location: '.URL.'creerCompte');
            }
        break;
        case "compte" :
            // faire la vérifation de l'authentification
            if(!Securite::estConnecte()){
                Toolbox::ajouterMessageAlerte("Vous devez être connecté pour accéder à cette page", Toolbox::COULEUR_ROUGE);
                header('Location: '.URL.'login');
            }else{
                switch($url[1]){
                    case "profil": $utilisateurController->profil();
                    break;
                    case "deconnexion" : $utilisateurController->deconnexion();
                    default : throw new Exception("La page n'existe pas");
                }
            } 
        break;
        default : throw new Exception("La page n'existe pas");
    }
} catch (Exception $e){
    $visiteurController->pageErreur($e->getMessage());
}