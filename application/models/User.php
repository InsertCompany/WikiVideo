<?php

class User extends CI_Model {
    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $birthdate;
    public $gender;
    public $postalcode;

    public function insert_entry($name,$email,$passwordHash,$birthdate,$gender,$postalcode,$city,$state){
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = $passwordHash;
        $this->birthdate = $birthdate;
        $this->gender = $gender;
        $this->postalcode = $postalcode;
        $this->city = $city;
        $this->state = $state;

        if($this->db->insert('User', $this)){
            return true;
        }else{
            return false;
        }
    }
    
    public function set_login_hash($email){
        $hashLogin = md5(uniqid(rand(),true));
        $user = array('login_hash' => $hashLogin);
        
        if($this->db->update('User',$user,array('email'=>$email))){
            return $hashLogin;
        }else{
            return null;
        }
    }
    public function get_user_by_email($email){
        $user = $this->db->select('*')->from('User')->join('Permission','User.id=Permission.user_id')->where(array('email'=>$email))->get()->result();
        if($user != null){
            return $user[0];
        }else{
            return null;
        }
    }
    
    public function verifyHashLogin($email,$hash){
        $user = $this->get_user_by_email($email);
        if($user){
            if($user->login_hash == $hash){
                return $user;
            }else{
                return null;
            }
        }else{
            return null;
        }
        
    }
    public function verify_password($email,$password){
        $user = $this->db->get_where('User',array('email'=>$email))->result()[0];
        if(password_verify($password,$user->password_hash)){
            return true;
        }else{
            return false;
        }
    }
    public function verify_email($email){
        $query = $this->db->get_where('User',array('email'=>$email));
        if(!empty($query->result())){
            return true;
        }else{
            return false;
        }
    }
    public function verify_login(){
		if($this->session->userdata('email') && $this->session->userdata('hash')){
			$email = $this->session->userdata('email');
			$hash = $this->session->userdata('hash');

			$user = $this->User->verifyHashLogin($email,$hash);
			if($user){
                return $user;
			}else{
                return null;
            }
		}
	}

}