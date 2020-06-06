<?php

class Course extends CI_Model {

    public $id;
    public $title;
    public $user_id;
    public $description;
    public $category_id;
    public $status;
    public $created_at;

    
    /* Verifica se existe um curso cadastrado na plataforma com esse id */
    public function exists_course($id){
        $query = $this->db->get_where('Course',array('id'=>$id));
		if(!empty($query->result_array())){
            return true;
        }else{
            return false;
        }
    }
    /* Obtém todas informações de um curso pelo id */
    public function get_course($id_course){
        $query = $this->db->select('Course.*,User.name,User.profile_picture, count(Rating.id) as ratings')->from('Course')->join('User','User.id=Course.user_id')->join('Rating','Course.id=Rating.course_id')->where(array('Course.id'=>$id_course))->get()->result();
		if(!empty($query)){
            return $query[0];
        }else{
            return array();
        }
    }
    public function get_content($id_course){
        $query = $this->db->select("Content.*")->from("Content")->where("Content.course_id",$id_course)->get()->result();

        return $query;
    }
    public function get_video($content_id){
        $query = $this->db->select("Video.*")->from("Video")->where("Video.content_id",$content_id)->get()->result();
        if(!empty($query)){
            return $query;
        }else{
            return null;
        }
    }
    public function get_user_rating($course_id,$user_id){
        $query = $this->db->select("Rating.*")->from("Rating")->where(array("Rating.user_id"=>$user_id,"Rating.course_id"=>$course_id));
        $result = $query->get()->result();
        if(!empty($result)){
            return $result;
        }else{
            return null;
        }
    }
    public function insert_user_rating($user_id,$course_id,$ratingValue,$comment){
        $rating = array(
            "user_id"=>$user_id,
            "rating"=>$ratingValue,
            "comment"=>$comment,
            "course_id"=>$course_id
        );
        $query = $this->db->insert("Rating",$rating);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    public function update_user_rating($rating_id,$ratingValue,$comment){
        $query = $this->db->where("id",$rating_id)->update("Rating",array("rating"=>$ratingValue,"comment"=>$comment));
        if($query){
            return true;
        }else{
            return false;
        }

    }
    /*Lista os cursos baseado em alguns filtros */
    public function list_course($limit,$start,$category_id='Course.Category_id',$order_by='rating_average',$order='DESC',$title=""){
        
        $query = $this->db->select('Course.*,Category.name as CategoryName')->join("Category","Course.category_id=Category.id")->from("Course")->like('Course.title',$title)->limit($limit,$start);
        
        if($category_id != 0){
           $query->where('Course.category_id='.$category_id);
        }
        if($order_by==""){
            $order_by='rating_average';
        }
        if($order==""){
            $order='DESC';
        }
        $result = $query->order_by($order_by,$order)->get()->result();
       
        //Seleciona todos elementos e acrescenta a quantidade de avaliações e o nome do autor
        foreach  ($result as $element){
            $element->ratings = $this->count_ratings($element->id);
            $author = $this->get_author($element->user_id);
            $element->name = $author->name;
            $element->profile_picture = $author->profile_picture;
        }
       
        return $result;
    }
    public function list_rating($limit,$start,$course_id){
        
        $query = $this->db->select("Rating.*,User.name,User.profile_picture")->from("Rating")->join("User","Rating.user_id=User.id")->limit($limit,$start)->where("Rating.course_id",$course_id);
        $result = $query->get()->result();
       
        //Seleciona todos elementos e acrescenta a quantidade de avaliações e o nome do autor
      
       
        return $result;
    }
    /* Conta a quantidade de cursos baseado no mesmo filtro da pesquisa*/
    public function count_course($category_id='Course.Category_id',$title=""){
        if($category_id ==0){
            $category_id='Course.Category_id';
        }
        $quantity = $this->db->where('Course.category_id='.$category_id)->select('Course.*,Category.name as CategoryName')->join("Category","Course.category_id=Category.id")->from("Course")->like('Course.title',$title)->count_all_results();
       
        return $quantity;      
    }
    /* Obtem o autor de um curso a partir do id do autor */
    public function get_author($id_author){
        $author = $this->db->select("*")->from("User")->where(array('id'=>$id_author))->get();
        return $author->result()[0];
    }
    //Conta a quantidade de avaliações que o curso têm
    public function count_ratings($id_course){
        $quantity = $this->db->where(array('course_id'=>$id_course))->from('Rating')->count_all_results();
        return $quantity;
    }
    //Obtém as categorias cadastrada na plataforma
    public function get_category($limit = 0){
        $query = $this->db->from('Category')->select("Category.*");

        if($limit != 0){
            $query->limit($limit);
        }

        $result = $query->get()->result();
        if(!empty($result)){
            return $result;
        }else{
            return null;
        }
    }
}