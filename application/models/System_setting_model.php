<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  System_setting_model.php
 *
 *     Description:  系统设置模型
 *
 *         Created:  2016-12-09 10:08:03
 *
 *          Author:  zourui
 *
 * =====================================================================================
 */
Class System_setting_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    /**
	 * 设置加载或更新状态
	 * @param array $array
	 */
	public function set_load_status($array = []){
		$data['success'] = FALSE;
		$data['msg'] = '设置失败';

		if (empty($array) || !is_array($array)){
			return $data;
		}

		$this->db->where('name', $array['name']);
		$result = $this->db->get('system_setting');
		if ($result && $result->num_rows() > 0) {
			$post['value']       = $array['value'];
			$post['update_time'] = $array['update_time'];
            $this->db->where('name', $array['name']);
			$status = $this->db->update('system_setting', $post);
			if ($status) {
				$data['success'] = TRUE;
				$data['msg'] = '更新成功';
			}
		}else{
			$post['name']  = $array['name'];
			$post['value'] = $array['value'];
			$post['create_time'] = $array['create_time'];
            $this->db->where('name', $array['name']);
			$status = $this->db->insert('system_setting', $post);
			if ($status) {
				$data['success'] = TRUE;
				$data['msg'] = '设置成功';
			}
		}

		return $data;
	}

	
	/**
	*获取订单预计完成天数
	*/
	public function get_system_info(){
        $this->db->where('name', 'orderExpectedCompletionDays');
		$result = $this->db->get('system_setting');
		if($result && $result->num_rows() > 0){
			$data['success'] = TRUE;
			$data['msg'] = '获取订单预计完成天数成功';
			$data['data'] = $result->result_array();
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '当前系统中未设置该订单预计完成天数';
			$data['data'] = null;
		}

		return $data;
	}

	/**
	 * 获取积分抽奖规则
	 *
	 * @return mixed
	 */
	public function get_sweepstakes_rules(){
		$result = $this->jys_db_helper->get_where('system_setting', ['name'=>'sweepstakesRules']);
		if ($result){
            $result['value'] = htmlspecialchars_decode($result['value']);
			$data['success'] = TRUE;
			$data['msg'] = '获取积分抽奖规则成功';
			$data['data'] = $result;
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '获取积分抽奖规则失败';
			$data['data'] = null;
		}

		return $data;
	}


	/**
	 * 获取积分夺宝规则
	 *
	 * @return mixed
	 */
	public function get_indiana_rules(){
		$result = $this->jys_db_helper->get_where('system_setting', ['name'=>'indianaRules']);
		if ($result){
            $result['value'] = htmlspecialchars_decode($result['value']);
			$data['success'] = TRUE;
			$data['msg'] = '获取积夺宝奖规则成功';
			$data['data'] = $result;
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '获取积夺宝奖规则失败';
			$data['data'] = null;
		}

		return $data;
	}

	/**
	 * 获取热卖商品封面路径
	 *
	 * @return mixed
	 */
	public function get_hot_sale_cover(){
		$result = $this->jys_db_helper->get_where('system_setting', ['name'=>'hotSaleCover']);
		if ($result){
			$data['success'] = TRUE;
			$data['msg'] = '获取热卖商品封面成功';
			$data['data'] = $result;
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '获取热卖商品封面失败';
			$data['data'] = null;
		}

		return $data;
	}

	/**
	 * 获取热换商品封面路径
	 *
	 * @return mixed
	 */
	public function get_hot_exchange_cover(){
		$result = $this->jys_db_helper->get_where('system_setting', ['name'=>'hotExchangeCover']);
		if ($result){
			$data['success'] = TRUE;
			$data['msg'] = '获取热换商品封面成功';
			$data['data'] = $result;
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '获取热换商品封面失败';
			$data['data'] = null;
		}

		return $data;
	}
}
