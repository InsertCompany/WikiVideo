<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CourseController extends CI_Controller {

	/**
	 * Controlador que renderiza as telas de matricula.
	 *
	 * Mapeado para seguinte URL
	 * 		https://wikivideo.ga/course/*
	 */

    /* Construtor padrão do controlador, inicializando uma biblioteca de validação de formularios, de sessão
    * E também carregando a model User,Enrollment e Course.
	*/

    public function __construct(){
        parent::__construct();
		$this->load->library('session');
		$this->load->model('User','',TRUE);
		$this->load->model('Enrollment','',TRUE);
		$this->load->model('Course','',TRUE);
		$this->load->library('form_validation');

		$this->data['base_url'] = base_url();		
		
		$this->login = $this->User->verify_login();
		if(!$this->login){
			$this->session->set_flashdata('notauth','Você deve estar autenticado para realizar essa ação!');
			redirect(base_url().'course?id='.$this->input->get('id'));
		}
	}
	/* Realiza a matricula de um aluno */
	public function enroll(){
		$user_id = $this->login->user_id;
		$course_id = $this->input->get('id');

		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('id', 'id', 'required|integer');
		
		/* Verificar se é valido o id */
		if($this->form_validation->run()){
			/* Verifica se realmente existe um curso com este id */
			if($this->Course->exists_course($course_id)){
				/* Verifica se já existe uma matricula */
				if(!$this->Enrollment->exists_enroll($user_id,$course_id)){
					/* Realiza a matricula */
					if($this->Enrollment->insert_entry($user_id,$course_id)){
						$this->session->set_flashdata('enrollsuccess','Matriculado com sucesso!');
						redirect(base_url().'course?id='.$this->input->get('id'));
					}else{
						$this->session->set_flashdata('enrollerror','Erro desconhecido ao se matricular!');
						redirect(base_url().'course?id='.$this->input->get('id'));
					}
				}else{
					$this->session->set_flashdata('enrollerror','Matricula já existe!');
					redirect(base_url().'course?id='.$this->input->get('id'));
				}
			
			}else{
				redirect(base_url().'courses');
			}
			
		}else{
			redirect(base_url().'courses');
		}
	}
	
}