<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

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

	public function __construct()
	{
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model("User",'',TRUE);
    }
    /*Metodo para verificar qual a requisição ajax */
	public function ajax()
	{
        if(isset($_POST['method'])){
            switch($_POST['method']){
                case "register":
                    echo json_encode($this->register());
                    break;
                case "login":
                    echo json_encode($this->login());
                    break;
            }
        }else{
            echo json_encode(["error"=>"forbiden"]);
        }
		
    }
    /*Metodo para login caso a requisição seja essa */
    private function login(){
        $return = array();
        if($this->validateLogin()){
            $email = $this->input->post("email");
            $password = $this->input->post("password");

            if($this->User->verifyEmail($email)){
                if($this->User->verifyPassword($email,$password)){
                    $loginHash = $this->User->setLoginHash($email);
                    if($loginHash){
                        $data = array('email'=>$email,'hash'=>$loginHash);
                        $this->session->set_userdata($data);
                        $result['result'] = 'success';
                    }                    
                }else{
                    $return['result'] = "error";
                    $return['errors'] = "Dados Invalidos";
                }
            }else{
                $return['result'] = "error";
                $return['errors'] = "Dados Invalidos";
            }
        }else{
            $return['result'] = "error";
            $return['errors'] = str_replace("\n","<br>",strip_tags(validation_errors()));
            
        }
        return $return;
    }
    private function register(){
        $return = array();
        if($this->validateRegister()){
            $firstname= $_POST['firstname'];
            $lastname= $_POST['lastname'];
            $email= $_POST['email'];
            $confirmEmail = $_POST['confirmEmail'];
            $password= $_POST['password'];
            $confirmPassword= $_POST['confirmPassword'];
            $birthday= $_POST['birthday'];

            $name = $firstname . " " . $lastname;
            $hashPassword = password_hash($password,PASSWORD_DEFAULT);
           
            if(!$this->User->verifyEmail($email)){
                $this->User->insert_entry($name,$email,$hashPassword,$birthday);
                $return["result"] = "success";
            }else{
                $return['result'] = "error";
                $return['errors'] = "Já existe uma conta cadastrada com esse email!";
            }


        }else{
            $return['result'] = "error";
            $return['errors'] = str_replace("\n","<br>",strip_tags(validation_errors()));
            
        }
        return $return;
    }
    private function verifyAge(){
        $year = date("Y");
        $yearSplit = explode("-",$_POST['birthday']);
        $minimum = $year-130;
        $maximum = $year-1;
        if($yearSplit[0] > maximum || $yearSplit[0] < $minimum){
            return false;
        }else{
            return true;
        }
    }
    private function validateLogin(){
        $config = array(
            array(
                'field'=>'email',
                'label'=>'Email',
                'rules'=>'required|valid_email|max_length[60]'
            ),
            array(
                'field'=>'password',
                'label'=>'Senha',
                'rules'=>'required|min_length[6]|max_length[60]'
            )
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() == FALSE){
            return false;
        }else{
            return true;
        }
    }
    private function validateRegister(){
        $config = array(
            array(
                'field'=>'firstname',
                'label'=>'Primeiro nome',
                'rules'=>'required|min_length[2]|max_length[25]|regex_match[/^([-a-zA-Z_ ])+$/]'
            ),
            array(
                'field'=>'lastname',
                'label'=>'Sobrenome',
                'rules'=>'required|min_length[6]|max_length[40]|regex_match[/^([-a-zA-Z_ ])+$/]'
            ),
            array(
                'field'=>'email',
                'label'=>'Email',
                'rules'=>'required|valid_email|max_length[60]'
            ),
            array(
                'field'=>'confirmEmail',
                'label'=>'Confirmar email',
                'rules'=>'required|matches[email]|max_length[60]'
            ),
            array(
                'field'=>'password',
                'label'=>'Senha',
                'rules'=>'required|min_length[6]|max_length[60]'
            ),
            array(
                'field'=>'confirmPassword',
                'label'=>'Confirmar senha',
                'rules'=>'required|matches[password]|min_length[6]|max_length[60]',
               
            ),
            array(
                'field'=>'birthday',
                'label'=>'Data de nascimento',
                'rules'=>'required|max_length[10]|min_length[10]',
                'message'=>'Informe uma data de nascimento valida!'
            )
        
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() == FALSE){
            return false;
        }else{
            return true;
        }
    }

}
