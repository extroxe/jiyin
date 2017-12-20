<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Article_model.php
 *
 *     Description: 文章模型
 *
 *         Created: 2016-11-22 17:33:40
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Article_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10, $status_id = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有文章数据',
            'data' => null,
            'total_page' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('article.id,
                           article.title,
                           article.abstract,
                           article.content,
                           article.status_id,
                           article.create_time,
                           article.update_time,
                           article.thumbnail_id,
                           attachment.path as thumbnail_path,
                           system_code.name as status_name');
        $this->db->join('attachment', 'attachment.id = article.thumbnail_id');
        $this->db->join('system_code', 'system_code.value = article.status_id and system_code.type = "'.jys_system_code::ARTICLE_STATUS.'"', 'left');
        if (intval($status_id) > 0) {
            $this->db->where('article.status_id', $status_id);
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('article.update_time', 'DESC');
        $result = $this->db->get('article');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $this->article_html_decode($result->result_array())
            ];
            $this->db->select('article.id');
            $this->db->join('attachment', 'attachment.id = article.thumbnail_id');
            $this->db->join('system_code', 'system_code.value = article.status_id and system_code.type = "'.jys_system_code::ARTICLE_STATUS.'"', 'left');
            if (intval($status_id) > 0) {
                $this->db->where('article.status_id', $status_id);
            }
            $count_result = $this->db->get('article');
            if ($count_result && $count_result->num_rows() > 0) {
                $total = $count_result->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 根据条件获取文章
     *
     * @param $condition
     * @return mixed
     */
    public function get_by_condition($condition){
        $this->db->select('article.*, attachment.path as thumbnail_path');
        $this->db->join('attachment', 'attachment.id = article.thumbnail_id');
        $this->db->where($condition);
        $result = $this->db->get('article');
        if ($result && $result->num_rows() > 0){
            return $this->article_html_decode($result->row_array());
        }
        return FALSE;
    }

    /**
     * 文章内容html解码
     *
     * @param array $articles
     * @return array
     */
    public function article_html_decode($articles = []){
        if (empty($articles) || !is_array($articles)){
            return [];
        }

        foreach ($articles as $key => $article){
            if (is_array($article)){
                $articles[$key]['content'] = htmlspecialchars_decode($article['content']);
            }else{
                $articles['content'] = htmlspecialchars_decode($articles['content']);
                return $articles;
            }
        }

        return $articles;
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate_log($page = 1, $page_size = 10, $keyword = '', $interface_name = '', $start_create_time = '', $end_create_time = ''){
        $data = [
            'success' => FALSE,
            'msg' => '没有日志数据',
            'data' => null,
            'total_page' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('log.*,
                           system_code.name as interface_name');
        $this->db->join('system_code', 'system_code.value = log.interface_name and system_code.type = "'.jys_system_code::ERP_NAME.'"', 'left');
        //按条件查询
        if (!empty($start_create_time)) {
            $this->db->where('log.create_time >=', $start_create_time);
        }
        if (!empty($end_create_time)) {
            $this->db->where('log.create_time <=', $end_create_time);
        }
        if (!empty($interface_name)) {
            $this->db->where('log.interface_name', $interface_name);
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('log.msg', $keyword);
            $this->db->or_like('log.success', $keyword);
            $this->db->or_like('log.create_time', $keyword);
            $this->db->or_like('log.code', $keyword);
            $this->db->group_end();
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('log.create_time', 'DESC');
        $result = $this->db->get('log');
        if ($result && $result->num_rows() > 0){
            $data = ['success' => FALSE, 'msg' => '获取成功', 'data' => $result->result_array()];
            $this->db->select('log.id');
            $this->db->join('system_code', 'system_code.value = log.interface_name and system_code.type = "'.jys_system_code::ERP_NAME.'"', 'left');
            //按条件查询
            if (!empty($start_create_time)) {
                $this->db->where('log.create_time >=', $start_create_time);
            }
            if (!empty($end_create_time)) {
                $this->db->where('log.create_time <=', $end_create_time);
            }
            if (!empty($interface_name)) {
                $this->db->where('log.interface_name', $interface_name);
            }
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('log.msg', $keyword);
                $this->db->or_like('log.success', $keyword);
                $this->db->or_like('log.create_time', $keyword);
                $this->db->or_like('log.interface_name', $keyword);
                $this->db->or_like('log.code', $keyword);
                $this->db->group_end();
            }
            $count_result = $this->db->get('log');
            if ($count_result && $count_result->num_rows() > 0) {
                $total = $count_result->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $data['total_page'] = 1;
            }
        }

        return $data;
    }
}