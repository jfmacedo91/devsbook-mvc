<?php
namespace src\handlers;

use \src\models\Post;

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
}