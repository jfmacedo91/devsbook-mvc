<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class AjaxController extends Controller {
	private $loggedUser;

	public function __construct() {
		$this->loggedUser = UserHandler::checkLogin();
		if(UserHandler::checkLogin() == false) {
			header('Content-Type: application/json');
      echo json_encode(['error' => 'O usuário não está logado!']);
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

		if($id && $txt) {
			PostHandler::addComment($id, $txt, $this->loggedUser->id);

			$array['userLink'] = '/perfil/'.$this->loggedUser->id;
			$array['userAvatar'] = '/media/avatars/'.$this->loggedUser->avatar;
			$array['userName'] = $this->loggedUser->name;
			$array['body'] = $txt;
		}

		header('Content-Type: application/json');
		echo json_encode($array);
		exit;
	}

	public function upload() {
		$array = ['error' => ''];

		if(isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])) {
			$photo = $_FILES['photo'];
			$maxWidth = 800;
			$maxHeight = 800;
			$maxRatio = $maxWidth / $maxHeight;

			if(in_array($photo['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
				[$originalWidth, $originalHeight] = getimagesize($photo['tmp_name']);
				$ratio = $originalWidth / $originalHeight;
				$newWidth = $maxWidth;
				$newHeight = $maxHeight;

				if($maxRatio > $ratio) {
					$newWidth = $newHeight * $ratio;
				} else {
					$newHeight = $newWidth / $ratio;
				}

				$finalImage = imagecreatetruecolor($newWidth, $newHeight);
				switch($photo['type']) {
					case 'image/jpeg':
					case 'image/jpg':
						$image = imagecreatefromjpeg($photo['tmp_name']);
					break;
					case 'image/png':
						$image = imagecreatefrompng($photo['tmp_name']);
					break;
				}

				imagecopyresampled(
					$finalImage, $image,
					0, 0, 0, 0,
					$newWidth, $newHeight, $originalWidth, $originalHeight
				);

				$photoName = md5(time().rand(0, 9999)).'.jpg';
				imagejpeg($finalImage, 'media/uploads/'.$photoName);

				PostHandler::addPost($this->loggedUser->id, 'photo', $photoName);
			}
		} else {
			$array['error'] = 'Nenhuma imagem enviada!';
		}

		header('Content-Type: application/json');
		echo json_encode($array);
		exit;
	}
}