<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller {

	/**
	 * Controlador que renderiza as principais telas da plataforma
	 *
	 * Mapeado para seguinte URL
	 * 		https://wikivideo.ga/my-dashboard/*
	 */

    /* Construtor padrão do controlador, inicializando uma biblioteca de validação de formularios, de sessão
    * E também carregando a model User,Enrollment e Course.
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
		$categories = $this->Course->get_category(3);
		if($categories != null){
			$categories[0]->courses = $this->Course->list_course(4,0,$categories[0]->id);
			$categories[1]->courses = $this->Course->list_course(4,0,$categories[1]->id);
			$categories[2]->courses = $this->Course->list_course(4,0,$categories[2]->id);
		}

		$this->data['categories'] = $categories;

	}
	public function chat(){
		$this->twig->display("home/chat.twig",$this->data);
	}
	
	public function index()
	{
		$courses = $this->Course->list_course(8,0);
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

		/*obtém e formata os dados do curso */
		$course = $this->Course->get_course($course_id);
		$this->data['course'] = $course;
		$this->data['course']->content = $this->Course->get_content($course_id);
		foreach($this->data['course']->content as $content){
			$content->video = $this->Course->get_video($content->id);
		}
		/* Verifica se o usuario esta logado */
		if($this->login){
			$success = $this->session->flashdata('enrollsuccess');
			$error = $this->session->flashdata('enrollerror');

			$this->data['enrollsuccess'] = $success;
			$this->data['enrollerror'] = $error;
			/* verifica se existe alguma matricula*/
			$enroll = $this->Enrollment->exists_enroll($this->login->user_id,$course_id);
			if($enroll != null){
				$this->data['enroll'] = $enroll[0];
				$this->data['existenroll'] = true;
			}else{
				$this->data['existenroll'] = false;
			}

		}
		$this->twig->display('home/course.twig',$this->data);
	}
	public function error_404(){
		header("Location:".$this->data['base_url']);
	}
}
