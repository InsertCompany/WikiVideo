<?php

class Enrollment extends CI_Model {

    public $id;
    public $user_id;
    public $course_id;
    public $created_at;

    public function insert_entry($user_id,$course_id){
        $this->user_id = $user_id;
        $this->course_id = $course_id;

        if($this->db->insert('Enrollment', $this)){
            return true;
        }else{
            return false;
        }
    }

    public function exists_enroll($user_id,$course_id){
        $query = $this->db->get_where('Enrollment',array('user_id'=>$user_id,'course_id'=>$course_id));
		if(!empty($query->result_array())){
            return true;
        }else{
            return false;
        }
    }


}