<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Favorite.php
 *
 *     Description:  收藏控制器
 *
 *         Created:  2017-1-3 17:45:44
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
class Favorite extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->model(['Favorite_model']);
    }

    /**
     * 分页获取用户收藏信息
     * @param int $page
     * @param int $page_size
     */
    public function get_favorite_by_page($page = 1, $page_size = 10) {
        $result = $this->Favorite_model->paginate($page, $page_size, array('favorite.user_id' => $_SESSION['user_id']));
        if ($result['success']) {
            $result['data'] = $this->Common_model->format_commodity_name($result['data']);
        }

        echo json_encode($result);
    }

    /**
     * 添加收藏
     */
    public function add() {
        $commodity_id = $this->input->post('commodity_id');
        $commodity_specification_id = $this->input->post('commodity_specification_id');

        $result = $this->Favorite_model->add($commodity_id, $_SESSION['user_id'], $commodity_specification_id);

        echo json_encode($result);
    }

    /**
     * 根据收藏ID取消收藏
     */
    public function delete_by_id() {
        $id = $this->input->post('id');

        $result = array('success' => FALSE, 'msg' => '取消收藏失败');
        if (intval($id) < 1) {
            $result['msg'] = '请选择要取消的收藏商品';
            echo json_encode($result);
            exit;
        }

        if ($this->jys_db_helper->delete('favorite', $id)) {
            $result['success'] = TRUE;
            $result['msg'] = '取消收藏成功';
        }
        echo json_encode($result);
    }

    /**
     * 根据商品ID取消收藏
     */
    public function delete_by_commodity_id() {
        $commodity_id = $this->input->post('commodity_id');
        $commodity_specification_id = $this->input->post('commodity_specification_id');
        $user_id = $this->session->userdata('user_id');

        $result = array('success' => FALSE, 'msg' => '取消收藏失败');
        if (intval($commodity_id) < 1 || intval($user_id) < 1 || intval($commodity_specification_id) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        $favorite = $this->jys_db_helper->get_where('favorite', array('commodity_id' => $commodity_id, 'commodity_specification_id' => $commodity_specification_id, 'user_id' => $user_id));
        if (empty($favorite)) {
            $result['msg'] = '用户未收藏该商品';
            echo json_encode($result);
            exit;
        } else {
            if ($this->jys_db_helper->delete('favorite', $favorite['id'])) {
                $result['success'] = TRUE;
                $result['msg'] = '取消收藏成功';
            }
        }

        echo json_encode($result);
    }

    /**
     * 根据商品ID，判断当前用户是否已经收藏了该商品
     */
    public function check_favorite_by_commodity_id() {
        $commodity_id = $this->input->post('commodity_id');
        $commodity_specification_id = $this->input->post('commodity_specification_id');
        $user_id = $this->session->userdata('user_id');

        $result = array('success' => FALSE, 'msg' => '当前用户未收藏当前商品');
        if (intval($commodity_id) < 1 || intval($user_id) < 1 || intval($commodity_specification_id) < 0) {
            $result['msg'] = '当前用户未收藏当前商品';
            echo json_encode($result);
            exit;
        }

        $favorite = $this->jys_db_helper->get_where('favorite', ['commodity_id' => $commodity_id, 'user_id' => $user_id, 'commodity_specification_id' => $commodity_specification_id]);
        if (!empty($favorite)) {
            $result['success'] = TRUE;
            $result['msg'] = '当前用户收藏了该商品';
            $result['favorite_id'] = $favorite['id'];
        }
        echo json_encode($result);
    }
}