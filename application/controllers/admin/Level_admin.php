<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Level_admin.php
 *
 *   Description: 会员等级管理
 *
 *       Created: 2016-11-15 19:46:53
 *
 *        Author: zourui
 *
 * =========================================================
 */

class Level_admin extends CI_Controller {
	 /**
     * 构造函数
     */
	public function __construct(){
		parent::__construct();
		$this->load->helper('form');
		$this->load->library(['form_validation', 'Jys_db_helper', 'Jys_attachment']);
		$this->load->model(['Level_model', 'Common_model']);
	}


	/**
	 * 管理员查看会员等级
	 */
	public function get_level(){
		$result['success'] = FALSE;
		$result['msg'] = '获取会员等级信息失败';
		
		$result = $this->Level_model->get_all_level();
		
		echo json_encode($result);
	}

	/**
	 * 获取等级名称和等级排序
	 */
	public function get_info(){
		$result['success'] = FALSE;
		$result['msg'] = '获取会员等级信息失败';

		$result = $this->Level_model->get_info();
		
		echo json_encode($result);
	}

	
	/**
	 * 管理员增加会员等级
	 */ 
	public function add(){
		$result['success'] = FALSE;
		$result['msg'] = '新增会员等级失败';

		//验证用户
		$this->form_validation->set_rules('name', '等级名称', 'trim|required');
		$this->form_validation->set_rules('rank', '等级排序', 'trim|required');
		$this->form_validation->set_rules('price_discount', '折扣', 'trim|required');
		$this->form_validation->set_rules('points_coefficient', '会员等级积分系数', 'trim|required');

		//表单验证是否通过
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']) {
			//以下为等级表所需参数
			$level['name']               = $this->input->post('name', TRUE);
			$level['rank']               = $this->input->post('rank', TRUE);
			$level['price_discount']     = $this->input->post('price_discount', TRUE);
			$level['points_coefficient'] = $this->input->post('points_coefficient', TRUE);
			//以下为上传会员图标成功后返回的附加表信息
			$level['icon_id']            = $this->input->post('attachment_id', TRUE);

			//以下为判断是否存在等级名称重复或排序重复
			$data = $this->jys_db_helper->all('level');
            if ($data['success']){
                foreach ($data['data'] as $key => $value) {
                    if(($level['name'] == $value['name']) || ($level['rank'] == $value['rank'])){
                        $result['success'] = FALSE;
                        $result['msg'] = '等级名称或等级排序有重复';
                        $result['error'] = $result['msg'];
                        echo json_encode($result);
                        exit;
                    }
                }
            }
			$result = $this->jys_db_helper->add('level', $level);
		}else{
			$result['success'] = FALSE;
			$result['msg'] = '输入有错误';
			$result['error'] = $res['msg'];
		}
		echo json_encode($result);

	}
	

	
	/**
	 * 管理员删除会员等级
     * 并在删除等级时，自动排序后面的等级
	 */
	public function del_level(){
		$result['success'] = FALSE;
		$result['msg'] = '删除会员等级失败';
		
		$id = $this->input->post('id', TRUE);
		if(intval($id) < 1){
			echo json_encode($result);exit;
		}

		//获取id对应的rank
        $level_info = $this->jys_db_helper->get('level', $id);
        $rank['rank'] = $level_info['rank'] + 1;

		$this->db->trans_start();
        if ($this->jys_db_helper->get_where('user', ['level'=>$id])){
            echo json_encode([
                'success' => FALSE,
                'msg' => '会员等级已在使用中'
            ]);exit;
        }

		$status = $this->jys_db_helper->delete('level', $id);
		if($status){
            //根据当前rank查询是否还有对应数据，有数据，进行依次更新排序，没有数据，不进行排序操作
            for ($i = 0; ; $i++){
                $res = $this->jys_db_helper->get_where('level', $rank);
                if ($res){
                    $res['rank'] = $rank['rank'] - 1;
                    $data = $this->jys_db_helper->update('level', $res['id'], $res);
                    if (!$data){
                        $result['success'] = FALSE;
                        $result['msg'] = '删除会员出错，请重试';
                        $this->db->trans_rollback();
                    }
                }else
                {
                    break;
                }
                $rank['rank']++;
            }
            $result['success'] = TRUE;
            $result['msg'] = '删除会员等级成功';
            $this->db->trans_commit();
        }
		echo json_encode($result);
		
	}
	
	/**
	 * 管理员更新会员等级
	 */
	public function update(){
		$result['success'] = FALSE;
		$result['msg'] = '更新会员等级失败';

		//验证用户
		$this->form_validation->set_rules('name', '等级名称', 'trim|required');
		$this->form_validation->set_rules('price_discount', '折扣', 'trim|required');
		$this->form_validation->set_rules('points_coefficient', '会员等级积分系数', 'trim|required');

		//表单验证是否通过
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']) {
			//以下为等级表所需参数
			$id	                         = intval($this->input->post('id', TRUE));
			$level['name']               = $this->input->post('name', TRUE);
			$level['price_discount']     = $this->input->post('price_discount', TRUE);
			$level['points_coefficient'] = $this->input->post('points_coefficient', TRUE);

			//以下为上传会员图标成功后返回的附加表信息
			$level['icon_id']            = $this->input->post('attachment_id', TRUE);

			$res_status = $this->jys_db_helper->update('level', $id, $level);
			if($res_status){
				$result['success'] = TRUE;
				$result['msg'] = '更新会员等级成功';
			}

		}else{
			$result['success'] = FALSE;
			$result['msg'] = '输入有错误';
			$result['error'] = $res['msg'];
		}
		echo json_encode($result);
	}

	/**
	 * 调整排序
	 */
    public function adjust_rank()
    {
        $data['success'] = FALSE;
        $data['msg'] = '更改等级排序失败123';

        $level_post = $this->input->post('id');
        $level_ids = explode(',', $level_post);
        if (empty($level_ids) || !is_array($level_ids))
        {
            $data['msg'] = '接收参数有误！';
            return $data;
        }

        $data['data'] = $level_ids;
        $this->db->trans_start();
        $level_rank = array();
        foreach ($level_ids as $key=>$val)
        {
            $level_rank['rank'] = $key + 1;
            if (!empty($val)){
                $res = $this->jys_db_helper->update('level', $val, $level_rank);
                if (!$res)
                {
                    $this->db->trans_rollback();
                }
                else
                {
                    $this->db->trans_commit();
                    $data['success'] = TRUE;
                    $data['msg'] = '更改等级排序成功';
                }
            }
        }
        $this->db->trans_complete();

        echo json_encode($data);
	}
}