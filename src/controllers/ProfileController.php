<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {
	private $loggedUser;
	public function __construct() {
		$this->loggedUser = UserHandler::checkLogin();
		if(UserHandler::checkLogin() == false) {
			$this->redirect('/login');
		}
	}

	public function index($atts = []) {
		$id = $this->loggedUser->id;
		$page = intval(filter_input(INPUT_GET, 'page'));

		if(!empty($atts['id'])) {
			$id = $atts['id'];
		}

		$user = UserHandler::getUser($id, true);

		if(!$user) {
			$this->redirect('/');
		}

		$dateFrom = new \DateTime($user->birthdate);
		$dateTo = new \DateTime('today');
		$user->age = $dateFrom->diff($dateTo)->y;

		$feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

		$isFollowing = false;

		if($user->id != $this->loggedUser->id) {
			$isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
		}

		$this->render('profile', [
      'loggedUser' => $this->loggedUser,
			'user' => $user,
			'feed' => $feed,
			'isFollowing' => $isFollowing
    ]);
	}

	public function follow($atts) {
		$to = intval($atts['id']);
		if(UserHandler::idExists($to)) {
			if(UserHandler::isFollowing($this->loggedUser->id, $to)) {
				UserHandler::unfollow($this->loggedUser->id, $to);
			} else {
				UserHandler::follow($this->loggedUser->id, $to);
			}
		}

		$this->redirect('/perfil/'.$to);
	}

	public function friends($atts = []) {
		$id = $this->loggedUser->id;
		$tab = filter_input(INPUT_GET, 'tab');

		if(!empty($atts['id'])) {
			$id = $atts['id'];
		}

		$user = UserHandler::getUser($id, true);

		if(!$user) {
			$this->redirect('/');
		}

		$dateFrom = new \DateTime($user->birthdate);
		$dateTo = new \DateTime('today');
		$user->age = $dateFrom->diff($dateTo)->y;

		$isFollowing = false;

		if($user->id != $this->loggedUser->id) {
			$isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
		}

		$this->render('profile_friends', [
			'loggedUser' => $this->loggedUser,
			'user' => $user,
			'isFollowing' => $isFollowing,
			'tab' => $tab
		]);
	}

	public function photos($atts = []) {
		$id = $this->loggedUser->id;

		if(!empty($atts['id'])) {
			$id = $atts['id'];
		}

		$user = UserHandler::getUser($id, true);

		if(!$user) {
			$this->redirect('/');
		}

		$dateFrom = new \DateTime($user->birthdate);
		$dateTo = new \DateTime('today');
		$user->age = $dateFrom->diff($dateTo)->y;

		$isFollowing = false;

		if($user->id != $this->loggedUser->id) {
			$isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
		}

		$this->render('profile_photos', [
			'loggedUser' => $this->loggedUser,
			'user' => $user,
			'isFollowing' => $isFollowing
		]);
	}

	public function config() {
    $flash = '';
    if(!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';
    }

		$user = UserHandler::getUser($this->loggedUser->id);

		$this->render('config', [
			'loggedUser' => $this->loggedUser,
			'user' => $user,
			'flash' => $flash
		]);
	}

	public function configAction() {
    $name = filter_input(INPUT_POST, 'name');
    $birthdate = filter_input(INPUT_POST, 'birthdate');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $city = filter_input(INPUT_POST, 'city');
    $work = filter_input(INPUT_POST, 'work');
    $password = filter_input(INPUT_POST, 'password');
    $passwordConfirm = filter_input(INPUT_POST, 'passwordConfirm');

		if($name && $email) {
			$updateFields = [];

			$updateFields['name'] = $name;

			$birthdate = explode('/', $birthdate);
			if(count($birthdate) != 3) {
				$_SESSION['flash'] = 'Data de nascimento inválida!';
				$this->redirect('/config');
			}
			$birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
			if(strtotime($birthdate) === false) {
				$_SESSION['flash'] = 'Data de nascimento inválida!';
				$this->redirect('/config');
			}
			$updateFields['birthdate'] = $birthdate;

			$user = UserHandler::getUser($this->loggedUser->id);
			if($user->email != $email) {
				if(!UserHandler::emailExists($email)) {
					$updateFields['email'] = $email;
				} else {
					$_SESSION['flash'] = 'E-mail já cadastrado!';
					$this->redirect('/config');
				}
			}

			$updateFields['city'] = $city;

			$updateFields['work'] = $work;

			if(!empty($password)) {
				if($password === $passwordConfirm) {
					$updateFields['password'] = $password;
				} else {
					$_SESSION['flash'] = 'As senhas digitadas não são iguais!';
					$this->redirect('/config');
				}
			}

			if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
				$newAvatar = $_FILES['avatar'];

				if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
					$avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
					$updateFields['avatar'] = $avatarName;
				} else {
					$_SESSION['flash'] = 'Os tipos de imagens suportados são JPEG, JPG e PNG!';
					$this->redirect('/config');
				}
			}

			if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
				$newCover = $_FILES['cover'];

				if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
					$coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
					$updateFields['cover'] = $coverName;
				} else {
					$_SESSION['flash'] = 'Os tipos de imagens suportados são JPEG, JPG e PNG!';
					$this->redirect('/config');
				}
			}

			UserHandler::updateUser($updateFields, $this->loggedUser->id);
		}

		$this->redirect('/config');
	}

	private function cutImage($file, $width, $height, $folder) {
		[$originalWidth, $originalHeight] = getimagesize($file['tmp_name']);
		$ratio = $originalWidth / $originalHeight;
		
		$newWidth = $width;
		$newHeight = $newWidth / $ratio;

		if($newHeight < $height) {
			$newHeight = $height;
			$newWidth = $newHeight * $ratio;
		}

		$xPosition = ($width - $newWidth) / 2;
		$yPosition = ($height - $newHeight) / 2;

		$finalImage = imagecreatetruecolor($width, $height);
		switch($file['type']) {
			case 'image/jpeg':
			case 'image/jpg':
				$image = imagecreatefromjpeg($file['tmp_name']);
			break;
			case 'image/png':
				$image = imagecreatefrompng($file['tmp_name']);
			break;
		}

		imagecopyresampled(
			$finalImage, $image,
			$xPosition, $yPosition, 0, 0,
			$newWidth, $newHeight,
			$originalWidth, $originalHeight
		);

		$fileName = md5(time().rand(0,9999)).'.jpg';
		imagejpeg($finalImage, $folder.'/'.$fileName);

		return $fileName;
	}
}