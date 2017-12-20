<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Detection_template_model.php
 *
 *     Description: 检测模型
 *
 *         Created: 2017-5-19 11:19:46
 *
 *          Author: liwen
 *
 * =====================================================================================
 */

class Detection_template_model extends CI_Model
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
        $this->load->library(['Jys_db_helper', 'Jys_tool']);
    }

    /*
     * 分页获取检测模板
     */
    public function get_template_by_page($page = 1, $page_size = 10, $keyword = '') {
        $data = [
            'success' => FALSE,
            'msg'     => '没有模板数据',
            'data'    => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('detection_template.*, count(detection_project.id) as project_count');
        $this->db->join('detection_project', 'detection_project.template_id = detection_template.id', 'left');

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('detection_template.name', $keyword);
            $this->db->or_like('detection_template.description', $keyword);
            $this->db->group_end();
        }
        $this->db->order_by('detection_template.create_time', 'DESC');
        $this->db->group_by('detection_project.template_id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('detection_template');
        if ($result && $result->num_rows() > 0) {
            $data = [
                'success' => TRUE,
                'data'    => $result->result_array(),
                'msg'     => '查询模板成功'
            ];

            $this->db->select('detection_template.*');
            
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('detection_template.name', $keyword);
                $this->db->or_like('detection_template.description', $keyword);
                $this->db->group_end();
            }
            $res = $this->db->get('detection_template');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /*
     * 获取所有的检测模板
     */
    public function get_detection_template() {
        $data = [
            'success' => FALSE,
            'msg'     => '没有模板数据',
            'data'    => null
        ];


        $templates = $this->jys_db_helper->all('detection_template');
        $projects = $this->jys_db_helper->all('detection_project');

        if (!$templates['success'] || empty($templates['data'])) {
            return $data;
        }

        if ($projects['success'] && !empty($projects['data'])) {
            $templates = $templates['data'];
            foreach ($projects['data'] as $project) {
                foreach ($templates as $key => $template) {
                    if ($template['id'] == $project['template_id']) {
                        $templates[$key]['projects'][] = $project;
                        break;
                    }
                }
            }
        }

        if (!empty($templates)) {
            $data['success'] = TRUE;
            $data['msg'] = '获取模板信息成功';
            $data['data'] = $templates;
        }

        return $data;
    }

    /*
     * 增加模板
     */
    public function add_template($template = []) {
        $data['success'] = FALSE;
        $data['msg']     = '添加失败';

        if (empty($template['name'])) {
            $data['msg'] = '参数错误';
            return $data;
        }
        $template['create_time'] = date('Y-m-d H:i:s');

        $data = $this->jys_db_helper->add('detection_template', $template);
        return $data;
    }

    /*
     * 修改模板
     */
    public function update_template($id, $name, $description = '', $hy_template_id = '') {
        $result = ['success' => FALSE, 'msg' => '更新模板失败'];

        if (intval($id) < 1) {
            $result['msg'] = '请选择要更新的模板';
            return $result;
        }

        $update = array();
        if (!empty($name)) {
            $update['name'] = $name;
        }
        if (isset($description)) {
            $update['description'] = $description;
        }
        if (!empty($hy_template_id)) {
            $update['hy_template_id'] = $hy_template_id;
        }
        if (!empty($update) && is_array($update)) {
            $update['create_time'] = date('Y-m-d H:i:s');
            if ($this->jys_db_helper->update('detection_template', $id, $update)) {
                $result['success'] = TRUE;
                $result['msg'] = '更新模板成功';
            } else {
                $result['msg'] = '更新模板失败';
            }
        }

        return $result;
    }

    /*
     * 删除模板
     */
    public function delete_template($template_id) {

        $data = ['success' => FALSE, 'msg' => '删除模板失败'];
        if (intval($template_id) < 1) {
            $result['msg'] = '请选择要删除的模板';
            return $result;
        }
        //删除模板前检测是否有检测项目已经选择了改模板
        $this->db->select('detection_project.*');
        $this->db->where('template_id', $template_id);
        $res = $this->db->get('detection_project');

        if ($res && $res->num_rows() > 0){
            $data = [
                'success' => FALSE,
                'msg'     => '模板已经被检测项目选中了，不能删除',
            ];
        }else {
            $result = $this->jys_db_helper->delete_by_condition('detection_template',['id' => $template_id]);
            if ($result) {
                $data['success'] = TRUE;
                $data['msg']     = '删除成功';
            }
        }

        return $data;
    }
}
