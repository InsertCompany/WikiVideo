<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

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
		$this->load->library('Twig');
		$this->load->helper('url');
		
		$this->data = array(
			'base_url'=>base_url()
		);
		$login = $this->User->verifyLogin();
		if($login){
			$this->data += $login;
		}
	}
	public function confirm()
	{
        $this->data['base_url'] = base_url();
        if(isset($_GET['email'])){
			$this->data['email'] = $_GET['email'];
            $this->data['showMessage'] = "true";
        }
		echo $this->twig->render('auth/confirm',$this->data);
    }
	public function logout(){
		$this->session->sess_destroy();
		redirect('', 'refresh');
	}
	public function recovery(){
		echo $this->twig->render('auth/recovery',$this->data);
	}
}
