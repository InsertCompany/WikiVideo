<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AjaxCoursesController extends CI_Controller {

	/**
	 * Controlador que controla as requisições ajax a procura de cursos dentro da paltaforma.
	 *
	 * Mapeado para seguinteURL
	 * 		https://wikivideo.ga/wikivideo/ajaxCourses
	 */

    /* Construtor padrão do controlador, inicializando uma biblioteca  de validação de formularios, de sessão
    * E também carregando a model Course.
    */
	public function __construct()
	{
        parent::__construct();
        
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model("Course",'',TRUE);

    }
    /*Metodo para verificar qual a requisição ajax */
	public function ajax()
	{
        if($this->input->post('method')){
            switch($this->input->post('method')){
                case "getCourses":
                    /* Obtém os dados do filtro de pesquisa */
                    $page = $this->input->post('page');
                    $title = $this->input->post('title');
                    $orderby = $this->input->post('orderby');
                    $orderbyOrder = "DESC";
                    $category = $this->input->post('category');

                    $invalid = false;
                    /* Verifica qual a ordem selecionada para os cursos */
                    switch($orderby){
                        case 'avaliacaoCrescente':
                            $orderby = "rating_average";
                            $orderbyOrder = "DESC";
                            break;
                        case 'avaliacaoDecrescente':
                            $orderby = "rating_average";
                            $orderbyOrder = "ASC";
                            break;
                        case 'matriculaCrescente':
                            $orderby = "enrollment_quantity";
                            $orderbyOrder = "DESC";
                            break;
                        case 'matriculaDecrescente':
                            $orderby = "enrollment_quantity";
                            $orderbyOrder = "ASC";
                            break;
                        case 'dataDecrescente':
                            $orderby = "created_at";
                            $orderbyOrder = "DESC";
                            break;

                        case 'dataCrescente':
                            $orderby = "created_at";
                            $orderbyOrder = "ASC";
                            break;
                        default:
                            $invalid = true;
                            break;
                    }
                    if($this->validate_filter() && !$invalid){
                        /* Retorna em formato json os cursos cadastrados */
                        echo json_encode($this->getCourses($page,$category,$orderby,$orderbyOrder,$title));
                    }else{
                        echo json_encode(array('error'=>'forbiden'));
                    }
                    break;
                case "getCategories":
                    echo json_encode($this->getCategories());
                    break;
                case "getRatings":
                    $page = $this->input->post('page');
                    $course_id = $this->input->post('courseId');
                    echo json_encode($this->getRatings($course_id,$page));
                    break;
                default:
					echo json_encode(array('error'=>'forbiden'));
					break;
            }
        }else{
            echo json_encode(["error"=>"forbiden"]);
        }
		
    }
    public function validate_filter(){
        $config = array(
            array(
                "field"=>"page",
                "label"=>"Pagina",
                "rules"=>"integer"
            ),
            array(
                "field"=>"title",
                "label"=>"Titulo",
                "rules"=>"max_length[60]|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/]"
            ),
            array(
                "field"=>"category",
                "label"=>"Categoria",
                "rules"=>"integer"
            )

        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() == FALSE){
            return false;
        }else{
            return true;
        }

    }
    public function getRatings($course_id,$page){
        $data = array();
	
		$config['per_page'] =4;
        $config['total_rows']= $this->Course->count_ratings($course_id);
        $config['page'] = $page;
        $data['page'] = $page;
        $data+=$this->pagination($config);
        
        $ratings =$this->Course->list_rating($config['per_page'],$data['initial'],$course_id);
        $data['ratings'] = $ratings;        
        $data['config'] = $config;
        return $data;
    }
    /* Obém os cursos cadastrados na plataforma utilizando um filtro */
    public function getCourses($page, $category,$orderby,$orderbyOrder,$title){
        $data = array();
	
		$config['per_page'] = 8;
        $config['total_rows']= $this->Course->count_course($category,$title);
        $config['page'] = $page;
        $data['page'] = $page;
        $data+=$this->pagination($config);
        
        $courses =$this->Course->list_course($config['per_page'],$data['initial'],$category,$orderby,$orderbyOrder,$title);
        $data['courses'] = $courses;        
        $data['config'] = $config;

        return $data;
    }
    /* Obtém todas as categorias cadastradas na plataforma */
    public function getCategories(){
        $data = array();
        $categories = $this->Course->get_category();
        $data['categories'] = $categories;
        return $data;
    }
    /* Calcula o maximo de paginas e o elemento inicial */
    public function pagination($config){
		$return['max_page'] = ceil($config['total_rows']/$config['per_page']);
		$return['initial'] = (($config['page']-1)*$config['per_page']);
		return $return;
	}
   

}
