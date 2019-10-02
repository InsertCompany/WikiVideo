<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

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
	private $data;

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
			$this->data['firstname'] = $login['firstname'];
		}
	}
	
	public function index()
	{
		echo $this->twig->render('home/home.php',$this->data);
	}
	public function courses(){
		echo $this->twig->render('home/courses.php',$this->data);
	}
	public function course(){
		echo $this->twig->render('home/course.php',$this->data);
	}
	public function register(){
		$this->load->model('User');
	}
}
