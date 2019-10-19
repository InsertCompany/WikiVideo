<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CourseController extends CI_Controller {

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
			$this->session->set_flashdata('notauth','Você deve esta autenticado para realizar essa ação!');
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
				if($this->Enrollment->exists_enroll($user_id,$course_id)){
					/* Realiza a matricula */
					if($this->Enrollment->insert_entry($user_id,$course_id)){
						echo "matriculado";
					}else{
						echo "erro";
					}
				}else{
					echo "Matricula ja existe";
				}
			
			}else{
				echo "Curso não existe";
			}
			
		}else{
			redirect(base_url());
		}
	}
	
}