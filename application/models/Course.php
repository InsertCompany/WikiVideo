<?php

class Course extends CI_Model {

    public $id;
    public $title;
    public $user_id;
    public $description;
    public $category_id;
    public $status;
    public $created_at;


    public function exists_course($id){
        $query = $this->db->get_where('Course',array('id'=>$id));
		if(!empty($query->result_array())){
            return true;
        }else{
            return false;
        }
	}
    public function get_course($id_course){
        $query = $this->db->select('Course.*,User.name')->from('Course')->join('User','User.id=Course.user_id')->where(array('Course.id'=>$id_course))->get()->result();
		if(!empty($query)){
            return $query[0];
        }else{
            return false;
        }
    }

    public function get_all_course($quantity){
        $query = $this->db->select('Course.*,User.name,Category.name as CategoryName, count(Enrollment.id) as count',false)->join("Enrollment","Enrollment.course_id=Course.id","inner")->join("User","Course.user_id=User.id")->join("Category","Course.category_id=Category.id")->from("Course")->group_by('Course.id')->order_by('count','DESC')->limit($quantity)->get()->result();
        if(!empty($query)){
            return $query;
        }else{
            return false;
        }
    }
}