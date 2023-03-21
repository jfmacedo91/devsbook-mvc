<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\PostComment;
use \src\models\PostLike;
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
    $perpage = 10;

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
    $perpage = 10;

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

      $postLikes = PostLike::select()->where('post_id', $postItem['id'])->get();

      $newPost->likeCount = count($postLikes);
      $newPost->liked = self::isLiked($postItem['id'], $loggedUserId);

      $newPost->comments = PostComment::select()->where('post_id', $postItem['id'])->get();
      foreach($newPost->comments as $key => $comment) {
        $commentUser = User::select()->where('id', $comment['user_id'])->one();
        $newPost->comments[$key]['userId'] = $commentUser['id'];
        $newPost->comments[$key]['userName'] = $commentUser['name'];
        $newPost->comments[$key]['userAvatar'] = $commentUser['avatar'];
      }

      $posts[] = $newPost;
    }

    return $posts;
  }

  public static function isLiked($postId, $loggedUserId) {
    $liked = PostLike::select()->where('post_id', $postId)->where('user_id', $loggedUserId)->one();

    if($liked) {
      return true;
    } else {
      return false;
    }
  }

  public static function addLike($postId, $loggedUserId) {
    PostLike::insert(['post_id' => $postId, 'user_id' => $loggedUserId, 'created_at' => date('Y-m-d H:i:s')])->execute();
  }

  public static function deleteLike($postId, $loggedUserId) {
    PostLike::delete()->where('post_id', $postId)->where('user_id', $loggedUserId)->execute();
  }

  public static function addComment($postId, $body, $userId) {
    PostComment::insert(['post_id' => $postId, 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s'), 'body' => $body])->execute();
  }

  public static function getPhotosFrom($userId) {
    $photosData = Post::select()->where('user_id', $userId)->where('type', 'photo')->orderBy('created_at', 'desc')->get();
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

  public static function delete($postId, $userId) {
    $post = Post::select()->where('id', $postId)->where('user_id', $userId)->one();

    if($post) {
      PostLike::delete()->where('post_id', $postId)->execute();
      PostComment::delete()->where('post_id', $postId)->execute();
      if($post['type'] === 'photo') {
        $img = 'media/uploads/'.$post['body'];
        if(file_exists($img)) {
          unlink($img);
        }
      }
      Post::delete()->where('id', $postId)->execute();
    }
  }
}