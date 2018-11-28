<?php 

namespace Nocarefree\PageManager;

class Login extends Controller{
	public function __construct(){
		$this->middleware('admin.guest')->except('logout');
	}

	public function login(){

	}

	public function logout(){

	}
}