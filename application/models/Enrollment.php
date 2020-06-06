<?php

class Enrollment extends CI_Model {

    public $id;
    public $user_id;
    public $course_id;
    public $created_at;

    public function insert_entry($user_id,$course_id){
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        try{
            if($this->db->insert('Enrollment', $this)){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){

        }
       
    }
    public function exists_enroll($user_id,$course_id){
        $query = $this->db->select('Enrollment.*')->from("Enrollment")->where(array('user_id'=>$user_id,'course_id'=>$course_id));
        $result = $query->get()->result();
		if(!empty($result)){
            return $result;
        }else{
            return null;
        }
    }
    
    public function get_user_enrolls($user_id,$limit,$start,$title="",$progress=0){
        $field = "Enrollment.progress_percentage";
        $query = $this->db->select('Enrollment.id as enrollment_id,Enrollment.progress_percentage,Course.*,Category.name, User.name as author, User.profile_picture')->from('Enrollment')->join('Course','Course.id=Enrollment.course_id')->join("User","User.id=Course.user_id")->join("Category","Category.id=Course.category_id")->where(array('Enrollment.user_id'=>$user_id))->like('Course.title',$title)->limit($limit,$start);
        switch($progress){
            case "1":
                $query->where('Enrollment.progress_percentage',100);
                break;
            case "2":
                $query->where('Enrollment.progress_percentage!=',100);
                break;
        }

        $result = $query->get()->result();
        foreach($result as $enroll){
            $query = $this->db->select("Rating.*")->from("Rating")->where(array("Rating.user_id"=>$user_id,"Rating.course_id"=>$enroll->id))->get()->result();
            if(!empty($query)){
                $enroll->rating = $query[0]->rating;
            }else{
                $enroll->rating = 0;
            }
        }
        return $result;
    }
    public function get_next_watch_video($user_id,$enrollment_id){
        $query = $this->db->select("Video.*, CourseProgress.complete,Course.title as course_title, CourseProgress.enrollment_id, CourseProgress.video_id,Enrollment.progress_percentage,Enrollment.course_id, Content.title as content_title");
        $query->from("CourseProgress")->where(array('CourseProgress.complete'=>0,"CourseProgress.enrollment_id"=>$enrollment_id,"Enrollment.user_id"=>$user_id));
        $query->join("Enrollment","Enrollment.id=CourseProgress.enrollment_id");
        $query->join("Course","Course.id=Enrollment.course_id");
        $query->join("Video","Video.id=CourseProgress.video_id");
        $query->join("Content","Video.content_id=Content.id")->order_by('CourseProgress.video_id','ASC');
        $result = $query->get()->result();

        if(!empty($result)){
           $result = $result[0];
            $contents = $this->db->select("Content.*")->from("Content")->where("Content.course_id",$result->course_id)->get()->result();
            foreach($contents as $content){
                $videos = $this->db->select("Video.*")->from("Video")->join('Content','Content.id=Video.content_id')->where('Content.id',$content->id)->get()->result();
               
                foreach($videos as $video){
                    $complete = $this->db->select("CourseProgress.complete")->from("CourseProgress")->where(array("video_id"=>$video->id,"enrollment_id"=>$enrollment_id))->get()->result()[0]->complete;
                    $video->complete = $complete;
                }

                $content->videos = $videos;
            }
            $result->contents = $contents;
            return $result;
        }else{
            $query = $this->db->select("Video.*, CourseProgress.complete,Course.title as course_title, CourseProgress.enrollment_id, CourseProgress.video_id,Enrollment.progress_percentage, Content.title as content_title,Enrollment.course_id")->from("CourseProgress");
            $query->where(array("CourseProgress.enrollment_id"=>$enrollment_id,"Enrollment.user_id"=>$user_id));
            $query->join("Enrollment","Enrollment.id=CourseProgress.enrollment_id");
            $query->join("Course","Course.id=Enrollment.course_id");
            $query->join("Video","Video.id=CourseProgress.video_id");
            $query->join("Content","Video.content_id=Content.id")->order_by('CourseProgress.video_id','DESC');
            $result = $query->get()->result();
            if(!empty($result)){
                $result = $result[0];
                $contents = $this->db->select("Content.*")->from("Content")->where("Content.course_id",$result->course_id)->get()->result();
               
                foreach($contents as $content){
                    $videos = $this->db->select("Video.*")->from("Video")->join('Content','Content.id=Video.content_id')->where('Content.id',$content->id)->get()->result();
                    foreach($videos as $video){
                        $complete = $this->db->select("CourseProgress.complete")->from("CourseProgress")->where(array("video_id"=>$video->id,"enrollment_id"=>$enrollment_id))->get()->result()[0]->complete;
                        $video->complete = $complete;
                    }
                
                    $content->videos = $videos;
                }
                $result->contents = $contents;
                return $result;
            }else{

            }
        }
    }
    public function get_video($user_id,$enrollment_id,$video_id){
        $query = $this->db->select("Video.*, CourseProgress.complete,Course.title as course_title, CourseProgress.enrollment_id, CourseProgress.video_id,Enrollment.progress_percentage,Enrollment.course_id, Content.title as content_title")->from("Video")->where(array("Video.id"=>$video_id,"CourseProgress.enrollment_id"=>$enrollment_id,"Enrollment.user_id"=>$user_id))->join("Content","Content.id=Video.content_id")->join("Course","Course.id=Content.course_id")->join("CourseProgress","CourseProgress.video_id=Video.id")->join("Enrollment","Enrollment.course_id=Course.id");

        $result = $query->get()->result();
        if(!empty($result)){
            $result = $result[0];
            $contents = $this->db->select("Content.*")->from("Content")->where("Content.course_id",$result->course_id)->get()->result();
            foreach($contents as $content){
                $videos = $this->db->select("Video.*")->from("Video")->join('Content','Content.id=Video.content_id')->where('Content.id',$content->id)->get()->result();
                
                foreach($videos as $video){
                    $complete = $this->db->select("CourseProgress.complete")->from("CourseProgress")->where(array("video_id"=>$video->id,"enrollment_id"=>$enrollment_id))->get()->result()[0]->complete;
                    $video->complete = $complete;
                }
            
                $content->videos = $videos;
            }
            $result->contents = $contents;

        }
        return $result;
    }

    public function make_video_watched($video_id,$enrollment_id,$complete=1){
        $this->db->trans_start();
        $query = $this->db->update("CourseProgress",array('CourseProgress.complete'=>$complete),array('CourseProgress.video_id'=>$video_id,'CourseProgress.enrollment_id'=>$enrollment_id));
        $this->update_course_progress($enrollment_id);
        $this->db->trans_complete();
        if($query){
            return true;
        }else{
            return false;
        }
    }
    public function update_course_progress($enrollment_id){
        $quantity_video_watched = $this->db->from("CourseProgress")->where(array("CourseProgress.enrollment_id"=>$enrollment_id,"CourseProgress.complete"=>1))->count_all_results();
        $quantityVideo = $this->db->select("Enrollment.id, Course.quantity_video")->from("Enrollment")->join("Course","Course.id=Enrollment.course_id")->where("Enrollment.id",$enrollment_id)->get()->result()[0]->quantity_video;
        
        $percentage = ($quantity_video_watched/$quantityVideo)*100;
        if($this->db->update("Enrollment",array("Enrollment.progress_percentage"=>$percentage),array("Enrollment.id"=>$enrollment_id))){
            return true;
        }else{
            return false;
        }
    }
    public function count_user_enrolls($user_id,$title="",$progress=""){
         $quantity = $this->db->from("Enrollment")->where("Enrollment.user_id",$user_id)->count_all_results();
        return $quantity;
    }
    public function count_user_completeenrolls($user_id){
        $quantity = $this->db->from("Enrollment")->where(array("Enrollment.user_id"=>$user_id,"Enrollment.progress_percentage"=>100))->count_all_results();
        return $quantity;
    }
    public function count_user_incompleteenrolls($user_id){
        $quantity = $this->db->from("Enrollment")->where(array("Enrollment.user_id"=>$user_id,"Enrollment.progress_percentage !="=>100))->count_all_results();
        return $quantity;
    }
}