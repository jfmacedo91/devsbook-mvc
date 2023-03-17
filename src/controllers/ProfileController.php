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
}