<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	private $data = array();
	public function __construct()
	{
        parent::__construct();
		$this->load->library('session');	
		$this->load->model('User','',TRUE);

		$this->data['base_url'] = base_url();		

		$this->login = $this->User->verify_login();

	}
	/* Rota para confirmação do email */
	public function confirm()
	{
        if(isset($_GET['email'])){
			$this->data['email'] = $_GET['email'];
            $this->data['showMessage'] = "true";
        }
		$this->twig->display('auth/confirm.twig',$this->data);
	}
	/* rota para realização de logout */
	public function logout(){
		$this->session->sess_destroy();
		redirect(base_url(), 'refresh');
	}
	/* Rota para recuperação de senha */
	public function recovery(){
		$this->twig->display('auth/recovery.twig',$this->data);
	}
	/* Rota de exibição da tela de login e registro */
	public function login(){
		if($this->login){
			redirect($this->data['base_url']);
		}else{
			$this->twig->display('auth/login.twig',$this->data);
		}
	}
}
