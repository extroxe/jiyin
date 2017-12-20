<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Level_model.php
 *
 *     Description:  会员模型
 *
 *         Created:  2016-11-15 20:54:57
 *
 *          Author:  zourui
 *
 * =====================================================================================
 */
Class Level_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }
	
	/**
	 * 获取所有的会员等级
	 */
	public function get_all_level(){

		$this->db->select('level.*,
						   attachment.path as path
						   ');
		$this->db->from('level');
		$this->db->join('attachment', 'attachment.id = level.icon_id');
        $this->db->order_by('rank');
		$result = $this->db->get();
		if($result && $result->num_rows() > 0){
			$data['success'] = TRUE;
			$data['msg'] = '获取会员等级信息成功';
			$data['data'] = $result->result_array();
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '当前系统中未设置等级信息';
		}

		return $data;
	}

	/**
	 * 获取等级名称和等级排序
	 */
	public function get_info(){

		$this->db->select('id, name, rank');
		$this->db->from('level');
		$result = $this->db->get();
		if($result && $result->num_rows() > 0){
			$data['success'] = TRUE;
			$data['msg'] = '获取会员等级信息成功';
			$data['data'] = $result->result_array();
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '当前系统中未设置等级信息';
		}

		return $data;
	}

	/**
	 * 根据等级id获取等级信息
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_level_by_id($id){
		if (empty($id) || intval($id) < 1){
			return FALSE;
		}

		$this->db->where('id', $id);
		$result = $this->db->get('level');

		if ($result && $result->num_rows() > 0){
			return $result->row_array();
		}

		return FALSE;
	}

	/**
	 * 添加等级
	 *
	 * @param array $level
	 * @return bool
	 */
	public function add_level($level = []){
		$data['success'] = FALSE;
		$data['msg'] = '添加失败';

		if (empty($level)){
			return $data;
		}

		$this->db->trans_begin();
		$category_id = $this->add_level_category($level);
		if ($category_id){
			$level['commodity_id'] = $this->add_level_commodity($level, $category_id);
			$data = $this->jys_db_helper->add('level', $level);
			if ($data['success']){
				$this->db->trans_commit();
			}else{
				$this->db->rollback();
			}
		}else{
			$this->db->rollback();
		}


		return $data;
	}

	/**
	 * 添加等级商品
	 *
	 * @param array $level 等级信息
	 * @return bool
	 */
	public function add_level_commodity($level = [], $category_id = 0){
		if (empty($level) || empty($category_id) || intval($category_id) < 1){
			return FALSE;
		}

		$commodity['name'] = $level['name'];
		$commodity['number'] = md5($level['name']);
		$commodity['points'] = 0;
		//会员分类（不能修改）
		$commodity['category_id'] = $category_id;
		$commodity['introduce'] = $level['name'];
		$commodity['detail'] = $level['name'];
		$commodity['sales_volume'] = 100000;
		$commodity['status_id'] = jys_system_code::COMMODITY_STATUS_PUTAWAY;
		$commodity['type_id'] = jys_system_code::COMMODITY_TYPE_MEMBER;
		$commodity['is_point'] = 0;
		$commodity['create_time'] = date('Y-m-d H:i:s');
        $commodity['update_time'] = date('Y-m-d H:i:s');

		$data = $this->jys_db_helper->add('commodity', $commodity, TRUE);

		if ($data['success']){
			$commodity_id = $data['insert_id'];
			$thumbnail_arr = [
				'attachment_id' => $level['icon_id'],
				'commodity_id' => $commodity_id,
				'create_time' => date('Y-m-d H:i:s')
			];
			$_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);

			if ($_data['success']){
				return $commodity_id;
			}
		}

		return FALSE;
	}

	/**
	 * 添加等级分类
	 *
	 * @param array $level 等级信息
	 * @return bool
	 */
	public function add_level_category($level = []){
		if (empty($level)){
			return FALSE;
		}

		$category['name'] = $level['name'];
		$category['parent_id'] = 13;
		$data = $this->jys_db_helper->add('category', $category, TRUE);

		if ($data['success']){
			return $data['insert_id'];
		}

		return FALSE;
	}

	/**
	 * 更新等级
	 *
	 * @param int $id 等级ID
	 * @param array $level 等级信息
	 * @return mixed
	 */
	public function update_level($id = 0, $level = []){
		$data['success'] = FALSE;
		$data['msg'] = '更新失败';

		if (empty($id) || empty($level) || intval($id) < 1){
			return $data;
		}

		$this->db->trans_begin();
		if ($this->update_level_commodity($level)){
			$data = $this->jys_db_helper->update('level', $id, $level);
			if ($data['success']){
				$this->db->trans_commit();
			}else{
				$this->db->rollback();
			}
		}else{
			$this->db->rollback();
		}


		return $data;
	}

	/**
	 * 更新等级商品
	 *
	 * @param array $level
	 * @return bool
	 */
	public function update_level_commodity($level = []){
		if (empty($level)){
			return FALSE;
		}

		$id = $level['commodity_id'];
		$commodity['name'] = $level['name'];
		$commodity['number'] = md5($level['name']);
		$commodity['introduce'] = $level['name'];
		$commodity['detail'] = $level['name'];
		$commodity['update_time'] = date('Y-m-d H:i:s');

		if ($this->jys_db_helper->update('commodity', $id, $commodity)){
			$thumbnail_arr = [
				'attachment_id' => $level['icon_id'],
				'commodity_id' => $id,
				'create_time' => date('Y-m-d H:i:s')
			];
			$_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);

			if ($_data['success']){
				return TRUE;
			}
		}

		return FALSE;
	}
}