<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Article.php
 *
 *   Description: 文章管理
 *
 *       Created: 2017-2-5 17:52:19
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Article extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Article_model']);
    }

    /**
     * 分页获取文章列表
     */
    public function paginate($page = 1, $page_size = 10) {
        $data = $this->Article_model->paginate($page, $page_size, jys_system_code::ARTICLE_STATUS_PUBLISHED);

        echo json_encode($data);
    }

    /**
     * 文章详情页
     */
    public function detail($id = 0){
        if(empty($id) || intval($id) < 1){
            show_404();
        }

        $data['title'] = "赛安生物-文章详情";
        $data['js'] = array('article_detail');
        $data['css'] = array('article_detail');
        $data['main_content'] = 'article_detail';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['article'] = $this->Article_model->get_by_condition(['article.id'=>$id, 'article.status_id'=>Jys_system_code::ARTICLE_STATUS_PUBLISHED]);
        $this->load->view('includes/template_view', $data);
    }

}