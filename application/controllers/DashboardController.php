<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardController extends CI_Controller {
	
	/**
	 * Controlador que renderiza as telas do painel do usuario, onde estão todos cursos matriculados
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
        $this->load->library('form_validation');
		$this->load->model('Course','',TRUE);
		$this->load->model('Enrollment','',TRUE);
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
			$categories = $this->Course->get_category(3);
			$categories[0]->courses = $this->Course->list_course(4,0,$categories[0]->id);
			$categories[1]->courses = $this->Course->list_course(4,0,$categories[1]->id);
			$categories[2]->courses = $this->Course->list_course(4,0,$categories[2]->id);
			$this->data['categories'] = $categories;		
		}else{
			redirect(base_url());
		}
	}
	
	public function index(){
		$this->data['enrollments_quantity'] = $this->Enrollment->count_user_enrolls($this->data['user']->userId);
		$this->data['enrollments_complete'] = $this->Enrollment->count_user_completeenrolls($this->data['user']->userId);
		$this->data['enrollments_incomplete'] = $this->Enrollment->count_user_incompleteenrolls($this->data['user']->userId);


		$this->twig->display('dashboard/index.twig',$this->data);
	}

	public function my_courses(){
		$error = $this->session->flashdata('getCourseError');
		if($error){
			$this->data['getCourseError'] = $error;
		}
		$this->twig->display('dashboard/my_courses.twig',$this->data);
	}


	 /* Calcula o maximo de paginas e o elemento inicial */
	 public function pagination($config){
		$return['max_page'] = ceil($config['total_rows']/$config['per_page']);
		$return['initial'] = (($config['page']-1)*$config['per_page']);
		return $return;
	}

	public function my_course(){
		$nextVideo = $this->Enrollment->get_next_watch_video($this->data['user']->userId,$this->input->get('id'));
		if($nextVideo){
			$this->data['video'] = $nextVideo;
			$this->twig->display('dashboard/my_course.twig',$this->data);
		}else{
			$error = $this->session->set_flashdata('getCourseError',"Infelizmente esse curso não tem nenhum conteudo!");
			redirect(base_url()."dashboard/my-courses");
		}

		
	}
	public function my_profile(){
		$this->twig->display('dashboard/my_profile.twig',$this->data);

	}


	public function ajax_dashboard(){
		$method = $this->input->post('method');
		if($method){
			switch($method){
				case "getCourses":
					$page = $this->input->post('page');
					$title = $this->input->post('title');
					$progress = $this->input->post('progress');
					echo json_encode($this->getCourses($page,$title,$progress));
					break;
				case "makeWatch":
					$video_id = $this->input->post("videoId");
	   				$enrollment_id = $this->input->post("enrollmentId");
					echo json_encode($this->makeWatch($video_id,$enrollment_id));
					break;
				case "makeNoWatch":
					$video_id = $this->input->post("videoId");
	   				$enrollment_id = $this->input->post("enrollmentId");
					echo json_encode($this->makeNoWatch($video_id,$enrollment_id));
					break;
				case "sendImage":
					echo json_encode($this->sendImage());
					break;
				case "removePicture":
					echo json_encode($this->removePicture());
					break;
				case "getCurrentVideo":
					$enrollment_id = $this->input->post('enrollmentId');
					echo json_encode($this->getCurrentVideo($enrollment_id));
					break;
				case "getVideo":
					$enrollment_id = $this->input->post('enrollmentId');
					$video_id = $this->input->post('videoId');
					echo json_encode($this->getVideo($enrollment_id,$video_id));
					break;
				case "getRating":
					$course_id = $this->input->post("courseId");
					$user_id = $this->data['user']->user_id;
					echo json_encode($this->getRating($course_id,$user_id));
					break;
				case "sendRating":
					$course_id = $this->input->post("courseId");
					$user_id = $this->data['user']->user_id;
					$ratingValue = $this->input->post('rating');
					$comment = $this->input->post('comment');
					echo json_encode($this->sendRating($course_id,$user_id,$ratingValue,$comment));
					break;
				case "changePassword":
					$oldPassword = $this->input->post("oldPassword");
					$newPassword = $this->input->post("newPassword");
					$confirmPassword = $this->input->post("confirmPassword");
					$userId = $this->data['user']->user_id;
					echo json_encode($this->changePassword($oldPassword,$newPassword,$confirmPassword,$userId));
					break;
				default:
					echo json_encode(array('error'=>'forbiden'));
					break;
			}
		}else{
			echo json_encode(array('error'=>'forbiden'));
		}
		
	}
	public function changePassword($oldPassword,$newPassword,$confirmPassword,$user_id){
		$return = array();
		if($this->validateChangePassword()){
			$hashPassword = $this->User->get_password($user_id)->password_hash;
			$verifyPassword = password_verify($oldPassword,$hashPassword);
			if($verifyPassword){
				$newHash = password_hash($newPassword,PASSWORD_DEFAULT);
				$update = $this->User->update_password($user_id,$newHash);
				if($update){
					$return['status'] = "success";
				}else{
					$return['status'] = "failed";
					$return['errors'] = "Falha ao salvar senha!";
				}
			}else{
				$return['status'] = "failed";
				$return['errors'] = "Senha antiga invalida!";
			}
		}else{
			$return['status'] = "failed";
            $return['errors'] = str_replace("\n","<br>",strip_tags(validation_errors()));
		}
		return $return;
	}
	public function validateChangePassword(){
		$config = array(
            array(
                'field'=>'oldPassword',
                'label'=>'Senha Antiga',
                'rules'=>'required|max_length[60]'
            ),
            array(
                'field'=>'newPassword',
                'label'=>'Senha',
				'rules'=>'required|max_length[60]'
			),
			array(
                'field'=>'confirmPassword',
                'label'=>'Confirmar senha',
                'rules'=>'required|max_length[60]|matches[newPassword]'
            )
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() == FALSE){
            return false;
        }else{
            return true;
        }
	}
	public function getRating($course_id,$user_id){
		$rating = $this->Course->get_user_rating($course_id,$user_id);
		return $rating;
	}
	public function sendRating($course_id,$user_id,$ratingValue,$comment){
		$rating = $this->Course->get_user_rating($course_id,$user_id)[0];
		if($rating){
			if($this->Course->update_user_rating($rating->id,$ratingValue,$comment)){
				return true;
			}else{
				return false;
			}
		}else{
			if($this->Course->insert_user_rating($user_id,$course_id,$ratingValue,$comment)){
				return true;
			}else{
				return false;
			}
		}
		
	}
	public function getCurrentVideo($enrollment_id){
		$nextVideo = $this->Enrollment->get_next_watch_video($this->data['user']->userId,$enrollment_id);
		return $nextVideo;
	}
	public function getVideo($enrollment_id,$video_id){
		$video = $this->Enrollment->get_video($this->data['user']->userId,$enrollment_id,$video_id);
		return $video;
	}
	/* Obém os cursos cadastrados na plataforma utilizando um filtro */
	public function getCourses($page, $title="",$progress=0){
		$data = array();
	
		$config['per_page'] = 8;
		$config['total_rows']= $this->Enrollment->count_user_enrolls($title,$progress);
		$config['page'] = $page;
		$data['page'] = $page;
		$data+=$this->pagination($config);
		
		$courses = $this->Enrollment->get_user_enrolls($this->data['user']->userId,$config['per_page'],$data['initial'],$title,$progress);
		$data['courses'] = $courses;        
		$data['sucess'] = true;
		return $data;
	}
	public function makeWatch($video_id,$enrollment_id){
		$data = array();
		
		
		$result = $this->Enrollment->make_video_watched($video_id,$enrollment_id);
	
		if($result){
			$data["status"] = "success";
	   	}else{
			$data["status"] = "failed";
		}
		return $data;
	}
	public function makeNoWatch($video_id,$enrollment_id){
		$data = array();
		
		$result = $this->Enrollment->make_video_watched($video_id,$enrollment_id,0);
	
		if($result){
			$data["status"] = "success";
	   	}else{
			$data["status"] = "failed";
		}
		return $data;
	}
	public function sendImage(){
		$id_user = 	$this->data['user']->user_id;
		$return = array();
		try{
			$upload = $this->UploadImage($id_user);
			if($upload['status'] != 'error'){
				if($this->User->update_profile_picture($this->data['user']->user_id,$upload['imagepath'])){
					$return = $upload;
				}else{

				}
			}else{
				$return['status'] = "failed";
				$return['error'] = $upload['error'];
			}
		}catch(Exception $e){
			$return['status'] = "failed";
		}
		return $return;
	}
	public function removePicture(){
		$id_user = $this->data['user']->user_id;
		if($this->User->remove_profile_picture($this->data['user']->user_id)){
			return true;
		}else{
			return false;
		}
	}
	public function UploadImage($user_id)
    {
        /* Carrega a biblioteca de upload do codeigniter */
		$this->load->library('upload');
        /*Inicia o processo de uplaod com try catch */
        try {
			$path = "./public/img/profile/".$user_id;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            /*As configurações de upload do arquivo */
            $config = array(
				'file_name'=>'profile',
				'upload_path'=>$path,
                'allowed_types'=>'gif|jpg|png',
				'max_size'=>'2000',
				'overwrite'=>TRUE
            );
            /*Realizar o upload do arquivo */
			$this->upload->initialize($config);
			
            if($upload = $this->upload->do_upload('image')){
				$config['image_library'] = 'gd2';
				$config1['source_image'] = $this->upload->upload_path."/".$this->upload->file_name;
				$config1['new_image'] =  $path."/".$this->upload->file_name;
				$config1['maintain_ratio'] = FALSE;
				$config1['width'] = 200;
				$config1['height'] = 200; 

				$this->load->library('image_lib',$config1);
				if($this->image_lib->resize()){
					return array('status'=>'success','imagepath'=>$user_id."/".$this->upload->data('file_name'));
				}else{
					return array('status'=>'error','error'=>$this->upload->display_errors());
				}
            }else{
            }
        } catch (Exception $e) {
            throw $e;
            return null;
        }
    }


}
