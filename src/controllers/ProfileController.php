<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {
    
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = LoginHandler::checkLogin();
        if($this->loggedUser === false) {
            $this->redirect('/login'); 
        }
    }

    public function index($atts = []) {
        $this->render('profile', [
            'loggedUser' => $this->loggedUser
        ]);
    }

}