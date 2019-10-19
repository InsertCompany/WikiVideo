<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller {

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
		$this->load->model('Course','',TRUE);
		$this->load->model('Enrollment','',TRUE);

		$this->data['base_url'] = base_url();		


		$this->login = $this->User->verify_login();
		if($this->login){
			$this->login->firstname = ucfirst(explode(" ",$this->login->name)[0]);

			if($this->login->gender == 0){
				$this->login->gender = "Masculino";
			}else{
				$this->login->gender = "Feminino";
			}
			
			$this->data['user'] = $this->login;			
			if($this->login->administrator || $this->login->professor || $this->login->bolsonaro){
				$this->data['has_permission'] = true;
			}			
		}
	}
	
	public function index()
	{
		$courses = $this->Course->get_all_course(8);
		$this->data['bestcourses']=$courses;
	
		$this->twig->display('home/home.twig', $this->data);
	}
	public function courses(){
		$this->twig->display('home/courses.twig',$this->data);
	}
	/* Rota para retornar os dados de um curso*/
	public function course(){
		/* Pega o id do curso e verifica se existe esse curso*/
		$course_id = $this->input->get('id');
		if(!$this->Course->exists_course($course_id)){
			redirect(base_url().'courses');
		}

		/* verifica se existe erro de autenticação */
		$error = $this->session->flashdata('notauth');
		if($error){
			$this->data['autherror'] = $error;
		}
	
		/* Verifica se o usuario esta logado */
		if($this->login){
			/* verifica se existe alguma matricula*/
			if($this->Enrollment->exists_enroll($this->login->user_id,$course_id)){
				/*obtém e formata os dados do curso */
				$course = $this->Course->get_course($course_id);
				$course->created_at = date('d/m/Y',time($course->created_at));
				$this->data['course'] = $course;
				$this->data['enroll'] = true;
			}else{
				$this->data['enroll'] = false;
			}

		}
		$this->twig->display('home/course.twig',$this->data);
	}

}
