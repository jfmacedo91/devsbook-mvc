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

    $userList = Relationship::select()->where('user_from', $userId)->get();
    $users = [];
    foreach($userList as $userItem) {
      $users[] = $userItem['user_to'];
    }
    $users[] = $userId;

    $postList = Post::select()->where('user_id', 'in', $users)->orderBy('created_at', 'desc')->page($page, $perpage)->get();

    $postsCount = Post::select()->where('user_id', 'in', $users)->count();

    $pagesCount = ceil($postsCount / $perpage);

    $posts = self::_postListToObject($postList, $userId);

    return [
      'posts'=>$posts,
      'pagesCount'=>$pagesCount,
      'currentPage'=>$page
    ];
  }

  public static function getUserFeed($userId, $page, $loggedUserId) {
    $perpage = 2;

    $postList = Post::select()->where('user_id', $userId)->orderBy('created_at', 'desc')->page($page, $perpage)->get();

    $postsCount = Post::select()->where('user_id', $userId)->count();

    $pagesCount = ceil($postsCount / $perpage);

    $posts = self::_postListToObject($postList, $loggedUserId);

    return [
      'posts'=>$posts,
      'pagesCount'=>$pagesCount,
      'currentPage'=>$page
    ];
  }

  public static function _postListToObject($postList, $loggedUserId) {
    $posts = [];
    foreach($postList as $postItem) {
      $newPost = new Post();
      $newPost->id = $postItem['id'];
      $newPost->type = $postItem['type'];
      $newPost->created_at = $postItem['created_at'];
      $newPost->body = $postItem['body'];
      $newPost->mine = false;

      if($postItem['user_id'] == $loggedUserId) {
        $newPost->mine = true;
      }

      $newUser = User::select()->where('id', $postItem['user_id'])->one();

      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      $newPost->likeCount = 0;
      $newPost->liked = false;

      $newPost->comments = [];

      $posts[] = $newPost;
    }

    return $posts;
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