<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\Relationship;

class PostHandler {
  public static function addPost($userId, $type, $body) {
    $body = trim($body);
    if(!empty($userId) && !empty($body)) {
      Post::insert([
        'user_id'=>$userId,
        'type'=>$type,
        'created_at'=>date('Y-m-d H:i:s'),
        'body'=>$body
      ])->execute();
    }
  }

  public static function getHomeFeed($userId, $page) {
    $perpage = 2;

    //1. Pegar lista de usuários que eu sigo.
    $userList = Relationship::select()->where('user_from', $userId)->get();
    $users = [];
    foreach($userList as $userItem) {
      $users[] = $userItem['user_to'];
    }
    $users[] = $userId;

    //2. Pegar os posts dessa galera ordenado pela data.
    $postList = Post::select()->where('user_id', 'in', $users)->orderBy('created_at', 'desc')->page($page, $perpage)->get();

    $postsCount = Post::select()->where('user_id', 'in', $users)->count();

    $pagesCount = ceil($postsCount / $perpage);

    //3. Transformar o resultado em objetos dos models.
    $posts = [];
    foreach($postList as $postItem) {
      $newPost = new Post();
      $newPost->id = $postItem['id'];
      $newPost->type = $postItem['type'];
      $newPost->created_at = $postItem['created_at'];
      $newPost->body = $postItem['body'];
      $newPost->mine = false;

      if($postItem['user_id'] == $userId) {
        $newPost->mine = true;
      }

      //4. Preencher as informações adicionais no post.
      $newUser = User::select()->where('id', $postItem['user_id'])->one();

      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      //4.1 Preencher informações de likes
      $newPost->likeCount = 0;
      $newPost->liked = false;

      //4.2 Preencher informações de comentários
      $newPost->comments = [];

      $posts[] = $newPost;
    }
    //5. Retornar o resultado.
    return [
      'posts'=>$posts,
      'pagesCount'=>$pagesCount,
      'currentPage'=>$page
    ];
  }

  public static function getPhotosFrom($userId) {
    $photosData = Post::select()->where('user_id', $userId)->where('type', 'photo')->get();
    $photos = [];
    foreach($photosData as $photo) {
      $newPost = new Post();
      $newPost->id = $photo['id'];
      $newPost->type = $photo['type'];
      $newPost->created_at = $photo['created_at'];
      $newPost->body = $photo['body'];

      $photos[] = $newPost;
    }

    return $photos;
  }
}