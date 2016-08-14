<?php

namespace Bixie\Cart\Helper;

use Bixie\Cart\CartModule;
use Pagekit\Application as App;
use Pagekit\User\Model\User;
use Pagekit\Application\Exception;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\Auth\Exception\BadCredentialsException;

class UserHelper {

	/**
	 * @var CartModule
	 */
	protected $cart;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * MailHelper constructor.
	 * @param User $user
	 */
	public function __construct (User $user = null) {
		$this->user = $user;
		$this->cart = App::module('bixie/cart');
	}

	/**
	 * @param $data
	 * @param $status
	 * @return User static
	 */
	public function createUser ($data, $status) {

		$userModule = App::module('system/user');

		if (App::user()->isAuthenticated() || $userModule->config('registration') == 'admin') {
			throw new Exception('Registration not allowed');
		}

		$password = @$data['password'];
		if (trim($password) != $password || strlen($password) < 3) {
			throw new Exception(__('Invalid Password.'));
		}

		$token = App::get('auth.random')->generateString(32);

		try {

			$user = User::create([
				'registered' => new \DateTime,
				'name' => @$data['name'],
				'username' => @$data['username'],
				'email' => @$data['email'],
				'password' => App::get('auth.password')->hash($password),
				'activation' => $token,
				'status' => $status
			]);

			$user->validate();
			$user->save();

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $this->user = $user;
	}

	public function login ($credentials) {

		if (App::user()->isAuthenticated()) {
			throw new Exception(__('Already logged in'));
		}

		try {

			App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

			return App::auth()->login($user);

		} catch (BadCredentialsException $e) {
			throw new Exception(__('Invalid username or password.'), $e->getCode(), $e);
		} catch (AuthException $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

}