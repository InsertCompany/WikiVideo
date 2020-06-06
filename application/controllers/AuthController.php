<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

	/**
	 * Controlador que renderiza as telas de login,registro,logout
	 *
	 * Mapeado para seguinte URL
	 * 		https://wikivideo.ga/auth/*
	 */

    /* Construtor padrão do controlador, inicializando uma biblioteca de validação de formularios, de sessão
    * E também carregando a model User e Course.
	*/
	
	private $data = array();
	public function __construct()
	{
        parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');	
		$this->load->model('User','',TRUE);
		$this->load->model('Course','',TRUE);


		$this->data['base_url'] = base_url();		

		$this->login = $this->User->verify_login();

		$categories = $this->Course->get_category(3);
		$categories[0]->courses = $this->Course->list_course(4,0,$categories[0]->id);
		$categories[1]->courses = $this->Course->list_course(4,0,$categories[1]->id);
		$categories[2]->courses = $this->Course->list_course(4,0,$categories[2]->id);
		$this->data['categories'] = $categories;

	}
	
	
	/* rota para realização de logout */
	public function logout(){
		$this->session->sess_destroy();
		delete_cookie('email');
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
