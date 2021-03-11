<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler {
    public static function addPost($idUser, $type, $body) {
        if(!empty($idUser) && !empty($body)) {
            $body = trim($body);
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public static function getHomeFeed($idUser) {
        //pegar lista de usuarios que EU sigo
        $usersList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach($usersList as $userItem) {
            $users [] = $userItem['user_to'];
        }
        $users[] = $idUser;
    }

}

 