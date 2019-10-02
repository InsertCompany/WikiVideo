<?php

class User extends CI_Model {

    public $name;
    public $email;
    public $password_hash;
    public $birthday;

    public function insert_entry($name,$email,$passwordHash,$birthday)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = $passwordHash;
        $this->birthday = $birthday;

        if($this->db->insert('User', $this)){
            return true;
        }else{
            return false;
        }
    }
    public function setLoginHash($email){
        $hashLogin = md5(uniqid(rand(),true));
        $user = array('login_hash' => $hashLogin);
        
        if($this->db->update('User',$user,array('email'=>$email))){
            return $hashLogin;
        }else{
            return null;
        }
    }
    
    public function verifyHashLogin($email,$hash){
        $user = $this->db->get_where('User',array('email'=>$email))->result()[0];
        if($user->login_hash == $hash){
            return $user;
        }else{
            return null;
        }
    }
    public function verifyPassword($email,$password){
        $user = $this->db->get_where('User',array('email'=>$email))->result()[0];
        if(password_verify($password,$user->password_hash)){
            return true;
        }else{
            return false;
        }
    }
    public function verifyEmail($email){
        $query = $this->db->get_where('User',array('email'=>$email));
        if(!empty($query->result())){
            return true;
        }else{
            return false;
        }
    }
    public function verifyLogin(){
        $data = array();
		if($this->session->userdata('email') && $this->session->userdata('hash')){
			$email = $this->session->userdata('email');
			$hash = $this->session->userdata('hash');

			$user = $this->User->verifyHashLogin($email,$hash);
			if($user){
                $data['firstname'] = ucfirst(explode(" ",$user->name)[0]);
                $data['fullname'] = ucwords($user->name);
                return $data;
			}else{
                return null;
            }
		}
	}

}