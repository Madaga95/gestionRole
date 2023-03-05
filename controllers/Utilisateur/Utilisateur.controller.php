<?php
require_once("./controllers/MainController.controller.php");
require_once("./models/Utilisateur/Utilisateur.model.php");

class UtilisateurController extends MainController
{
    private $utilisateurManager;

    public function __construct()
    {
        $this->utilisateurManager = new UtilisateurManager();
    }

    public function validation_login($login, $password)
    {
        if ($this->utilisateurManager->isCombinaisonValide($login, $password)) {
            // verifie si le compte est valide
            if ($this->utilisateurManager->estCompteActive($login)) {
                Toolbox::ajouterMessageAlerte("Bon retour sur le site", Toolbox::COULEUR_VERTE);
                $_SESSION['profil'] = [
                    'login' => $login,
                ];
                header('Location: ' . URL . 'compte/profil');
            } else {
                $msg = "Votre compte " . $login . " n'a pas été activé par mail.";
                $msg .= " <a href='renvoyerMailValidation/" . $login . "'>Renvoyer le mail de validation</a>";
                Toolbox::ajouterMessageAlerte($msg, Toolbox::COULEUR_ORANGE);
                header('Location: ' . URL . 'login');
            }
        } else {
            Toolbox::ajouterMessageAlerte("Combinaison login/mot de passe invalide", Toolbox::COULEUR_ROUGE);
            header('Location: ' . URL . 'login');
        }
    }

    // gestion de profil
    public function profil()
    {
        $datas = $this->utilisateurManager->getUserInformation($_SESSION['profil']['login']);
        $_SESSION['profil']['role'] = $datas['role'];

        $data_page = [
            "page_description" => "Page de profil",
            "page_title" => "Profil",
            "infos" => $datas,
            "view" => "views/Utilisateur/profil.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }

    public function deconnexion()
    {
        session_destroy();
        header('Location: ' . URL . 'accueil');
    }

    public function validation_creerCompte($login, $mail, $password)
    {
        if ($this->utilisateurManager->verifLoginDisponible($login)) {
            $passwordCrypte = password_hash($password, PASSWORD_DEFAULT);
            $clef = rand(0, 9999);
            if ($this->utilisateurManager->bdCreerCompte($login, $mail, $passwordCrypte, $clef)) {
                $this->sendMailValidation($login, $mail, $clef);
                Toolbox::ajouterMessageAlerte("Votre compte a bien été créé, vous allez recevoir un mail pour l'activer", Toolbox::COULEUR_VERTE);
                header('Location: ' . URL . 'login');
            } else {
                Toolbox::ajouterMessageAlerte("Erreur lors de la création du compte", Toolbox::COULEUR_ROUGE);
                header('Location: ' . URL . 'creerCompte');
            }
        } else {
            Toolbox::ajouterMessageAlerte("Le login " . $login . " est déjà utilisé", Toolbox::COULEUR_ROUGE);
            header('Location: ' . URL . 'creerCompte');
        }
    }

    // envoi d'email
    private function sendMailValidation($login, $mail, $clef)
    {
        $urlVerification = URL . "validationMail/" . $login . "/" . $clef;
        $sujet = "Création du compte sur le site xxx";
        $message = "Pour valider votre compte veuillez cliquer sur le lien suivant " . $urlVerification;
        Toolbox::sendMail($mail, $sujet, $message);
    }

    // renvoi de mail de validation
    public function renvoyerMailValidation($login){
        $utilisateur = $this->utilisateurManager->getUserInformation($login);
        $this->sendMailValidation($login,$utilisateur['mail'],$utilisateur['clef']);
        header("Location: ".URL."login" );
    }

    public function pageErreur($msg)
    {
        parent::pageErreur($msg);
    }
}
