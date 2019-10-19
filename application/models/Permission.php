<?php

class Permission extends CI_Model {

    public $user_id;
    public $bolsonaro;
    public $administrator;
    public $professor;

    public function insert_entry($user_id,$bolsonaro,$administrator,$professor){
        $this->user_id = $user_id;
        $this->bolsonaro = $bolsonaro;
        $this->administrator = $administrator;
        $this->professor = $professor;

        if($this->db->insert('Permission', $this)){
            return true;
        }else{
            return false;
        }
    }


}