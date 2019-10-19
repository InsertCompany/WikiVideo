<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AjaxController extends CI_Controller {

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
        $this->load->model("Permission",'',TRUE);

    }
    /*Metodo para verificar qual a requisição ajax */
	public function ajax()
	{
        if($this->input->post('method')){
            switch($this->input->post('method')){
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
        if($this->validate_login()){
            $email = $this->input->post("email");
            $password = $this->input->post("password");

            if($this->User->verify_email($email)){
                if($this->User->verify_password($email,$password)){
                    $loginHash = $this->User->set_login_hash($email);
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
    /*Função para registrar o usuario caso a acao seja registrar */
    private function register(){
        $return = array();
        $email= $this->input->post('email');
        if(!$this->User->verify_email($email)){
            /* Valida os campos informado pelo usuario */
            if($this->validate_register()){
                /* Salva em uma variavel todos campos */
                $firstname= $this->input->post('firstname');
                $lastname= $this->input->post('lastname');
                $confirmEmail = $this->input->post('confirmEmail');
                $password= $this->input->post('password');
                $confirmPassword= $this->input->post('confirmPassword');
                $birthdate= $this->input->post('birthdate');
                $gender= $this->input->post('gender');
                $postalcode = str_replace('-','',$this->input->post('postalcode'));
    
                /*Junta o nome e realizar o hash da senha */
                $name = $firstname . " " . $lastname;
                $hashPassword = password_hash($password,PASSWORD_DEFAULT);
              
                /* Consulta nome da cidade e estado pelo cep */
                $location = $this->consult_postal_code($postalcode);
                $city = $location->localidade;
                $state = $location->uf;

                /* Valida se a idade é maior e inferior ao limite */
                if(!$this->verify_age()){
                    $return['result'] = "error";
                    $return['errors'] = "Data de nascimento invalida!";
                    return $return;
                }
                /* Com todos dados certos inicia a transação para banco de dados */
                $this->db->trans_begin();
                if($this->User->insert_entry($name,$email,$hashPassword,$birthdate,$gender,$postalcode,$city,$state)){
                    $last_id = $this->db->insert_id();
                    /* Cadastra as permissões padrões de usuario */
                    if($this->Permission->insert_entry($last_id,0,0,0)){
                        $this->db->trans_commit();
                        $return["result"] = "success";
                    }else{
                        /* se ocorrer algum erro realizar um rollback */ 
                        $this->db->trans_rollback();
                    }
                }else{
                    /* se ocorrer algum erro realizar um rollback */ 
                    $this->db->trans_rollback();
                } 
            }else{
                $return['result'] = "error";
                $return['errors'] = str_replace("\n","<br>",strip_tags(validation_errors()));
            }
        }else{
            $return['result'] = "error";
            $return['errors'] = "Já existe uma conta cadastrada com esse email!";
        }
        return $return;
    }
    /* Função para consultar cidade e estado pelo CEP */
    private function consult_postal_code($postalcode){
        $url = "https://viacep.com.br/ws/".$postalcode."/json/";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($curl));
        return $result;
    }
    /* Função para verificar se a data de nascimento é valida */
    private function verify_age(){
        $year = date("Y");
        $yearSplit = explode("-",$this->input->post('birthdate'));

        $minimum = $year-130;
        $maximum = $year-1;

        if($yearSplit[0] > $maximum || $yearSplit[0] < $minimum){
            return false;
        }else{
            return true;
        }
    }
    /* Função para validar o email e senha para realização do login */
    private function validate_login(){
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
    /* Função para validar  os campos do registro */ 
    private function validate_register(){
        $config = array(
            array(
                'field'=>'firstname',
                'label'=>'Primeiro nome',
                'rules'=>'required|min_length[2]|max_length[25]|regex_match[/^([-a-zA-Z_ ])+$/]'
            ),
            array(
                'field'=>'lastname',
                'label'=>'Sobrenome',
                'rules'=>'required|min_length[4]|max_length[40]|regex_match[/^([-a-zA-Z_ ])+$/]'
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
                'field'=>'birthdate',
                'label'=>'Data de nascimento',
                'rules'=>'required|max_length[10]|min_length[10]',
                'message'=>'Informe uma data de nascimento valida!'
            ),
            array(
                'field'=>'postalcode',
                'label'=>'CEP',
                'rules'=>'required|max_length[9]|min_length[9]',
                'message'=>'Informe o seu CEP.'
            ),
            array(
                'field'=>'gender',
                'label'=>'Gênero',
                'rules'=>'regex_match[/[01]$/]|required',
                'message'=>'Informe o seu gênero'
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
