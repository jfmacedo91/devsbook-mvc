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

			UserHandler::updateUser($updateFields, $this->loggedUser->id);
		}

		$this->redirect('/config');
	}
}