<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class AjaxController extends Controller {
    
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if($this->loggedUser === false) {
            header("Content-Type: application/json");
            echo json_encode(['error' => 'Usuário não logado']);
            exit;
        }
    }

    public function like($atts) {
        $id = $atts['id'];

        if(PostHandler::isLiked($id, $this->loggedUser->id)) {
            PostHandler::deleteLike($id, $this->loggedUser->id);
        } else {
            PostHandler::addLike($id, $this->loggedUser->id);
        }
    }

    public function comment() {
        $array = ['error' => ''];

        $id = filter_input(INPUT_POST, 'id');
        $txt = filter_input(INPUT_POST, 'txt');

        if ($id && $txt) {
            PostHandler::addComment($id, $txt, $this->loggedUser->id);

            $array['link'] = '/perfil/'.$this->loggedUser->id;
            $array['avatar'] = '/media/avatars/'.$this->loggedUser->avatar;
            $array['name'] = $this->loggedUser->name;
            $array['body'] = $txt;
        }

        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }

    public function upload() {
        $array = ['error' => ''];

        if(isset($_FILES['photo']) && !empty($_FILES['photos']['tmp_name'])) {
            $photo = $_FILES['photo'];

            $maxWidth = 800;
            $maxHeigh = 800;

            if(in_array($photo['type'], ['image/png', 'image/jpg', 'image/jpeg'])) {
                
                list($widthOrig, $heigthOrig) = getimagesize($photo['tmp_name']);
                $ratio = $widthOrig / $heigthOrig;

                $newWidth = $maxWidth;
                $newHeigth = $maxHeigh;
                $ratioMax = $maxWidth / $maxHeigh;

                if($ratioMax > $ratio) {
                    $newWidth = $newWidth * $ratio;
                } else {
                    $newHeigth = $newWidth / $ratio; 
                }

                $finalImage = imagecreatetruecolor($newWidth, $newHeigth);
                switch($photo['type']) {
                    case 'image/png':
                        $image = imagecreatefrompng($photo['tmp_name']);
                    break;
                    case 'image/jpg':
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($photo['tmp_name']);
                    break;
                }

                imagecopyresampled(
                    $finalImage, $image,
                    0, 0, 0, 0,
                    $newWidth, $newHeigth, $widthOrig, $heigthOrig
                );

                $photoName = md5(time().rand(0,9999)).'.jpg';
                imagejpeg($finalImage, 'media/uploads/'.$photoName);

                PostHandler::addPost(
                    $this->loggedUser->id,
                    'photo',
                    $photoName
                );
            }



        } else {
            $array['error'] = 'Nenhum imagem enviada';
        }


        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }

}