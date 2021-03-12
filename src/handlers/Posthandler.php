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

    public static function getHomeFeed($idUser, $page) {
    $perPage = 2;

        //pegar lista de usuarios que EU sigo
        $usersList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach($usersList as $userItem) {
            $users [] = $userItem['user_to'];
        }
        $users[] = $idUser;

        //pegar o post das pessoas que sigo de forma ordenada
        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, 2)
        ->get();

        $total = Post::select()
            ->where('id_user', 'in', $users)
        ->count();
        $pageCount = ceil($total / $perPage);

        //Transformar o resultado em objtos dos models
        $posts = [];
        foreach($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];

            if($postItem['id_user'] == $idUser) {
                $newPost->mine = true;
            }
            
        //Preencher as informacoes adicionais no post
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

        //Preencher informacoes de Like
            $newPost->likeCount = 0;
            $newPost->liked = false;

        //Preencher informacoes de Comments
            $newPost->comments = [];

            $posts[] = $newPost;
        }
        //retornar o resultado
            return [
                'posts' => $posts,
                'pageCount' => $pageCount,
                'currentPage' => $page
            ];
    }

}

 