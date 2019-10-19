<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardController extends CI_Controller {

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
		
		$login = $this->User->verify_login();
		if($login){
			$login->firstname = ucfirst(explode(" ",$login->name)[0]);
			
			if($login->gender == 0){
				$login->gender = "Masculino";
			}else{
				$login->gender = "Feminino";
			}
			$login->birthdate = date('d/m/Y',strtotime($login->birthdate));
			
			$this->data['user'] = $login;			
			if($login->administrator || $login->professor || $login->bolsonaro){
				$this->data['has_permission'] = true;
			}			
		}else{
			redirect(base_url());
		}
	}
	
	public function index(){
		$this->twig->display('dashboard/index.twig',$this->data);
	}

	public function my_courses(){
		$this->twig->display('dashboard/my_courses.twig',$this->data);
	}
	public function my_course(){
		$this->twig->display('dashboard/my_course.twig',$this->data);
		
	}
	public function my_profile(){
		$this->twig->display('dashboard/my_profile.twig',$this->data);

	}

}
