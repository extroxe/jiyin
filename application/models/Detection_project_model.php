<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Detection_project_model.php
 *
 *     Description: 检测项目模型
 *
 *         Created: 2017-5-19 11:19:46
 *
 *          Author: liwen
 *
 * =====================================================================================
 */

class Detection_project_model extends CI_Model
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_db_helper', 'Jys_tool']);
    }

    /*
     * 分页获取检测模板
     */
    public function get_project_by_page($page = 1, $page_size = 10, $keyword = '', $template_id = '') {
        $data = [
            'success'   => FALSE,
            'msg'       => '没有检测项目数据',
            'data'      => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('detection_project.*,
                            detection_template.name as template_name
                            ');
        $this->db->join('detection_template', 'detection_template.id = detection_project.template_id', 'left');
        if (!empty($template_id)) {
            $this->db->where('detection_project.template_id', $template_id);
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('detection_project.name', $keyword);
            $this->db->or_like('detection_project.description', $keyword);
            $this->db->group_end();
        }
        $this->db->order_by('detection_project.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('detection_project');
        if ($result && $result->num_rows() > 0) {
            $data = [
                'success'   => TRUE,
                'data'      => $result->result_array(),
                'msg'       => '查询检测项目成功'
            ];

            $this->db->select('detection_project.*');
            if (!empty($template_id)) {
                $this->db->where('detection_project.template_id', $template_id);
            }
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('detection_project.name', $keyword);
                $this->db->or_like('detection_project.description', $keyword);
                $this->db->group_end();
            }
            $res = $this->db->get('detection_project');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /*
     * 增加检测项目
     */
    public function add_project($project =[]) {
        $data['success'] = FALSE;
        $data['msg']     = '添加失败';

        if (empty($project['name'])) {
            $data['msg'] = '参数错误';
            return $data;
        }
        $project['create_time'] = date('Y-m-d H:i:s');
        $project['update_time'] = date('Y-m-d H:i:s');

        $data = $this->jys_db_helper->add('detection_project', $project);
        return $data;
    }

    /*
     * 修改检测项目信息
     */
    public function update_project($id, $template_id, $name, $description = '', $hy_project_id = '') {
        $result = ['success' => FALSE, 'msg' => '更新检测项目失败'];

        if (intval($id) < 1) {
            $result['msg'] = '请选择要更新的检测项目';
            return $result;
        }

        $update = array();
        if (!empty($name)) {
            $update['name'] = $name;
        }
        if (!empty($template_id)) {
            $update['template_id'] = $template_id;
        }
        if (isset($description)) {
            $update['description'] = $description;
        }
        if (!empty($hy_project_id)) {
            $update['hy_project_id'] = $hy_project_id;
        }
        if (!empty($update) && is_array($update)) {
            $update['create_time'] = date('Y-m-d H:i:s');
            if ($this->jys_db_helper->update('detection_project', $id, $update)) {
                $result['success'] = TRUE;
                $result['msg'] = '更新模板成功';
            } else {
                $result['msg'] = '更新模板失败';
            }
        }

        return $result;
    }

    /**
     *删除检测项目
     */
    public function delete_project($project_id)
    {
        $result = ['success' => FALSE, 'msg' => '删除检测项目失败'];
        if (intval($project_id) < 1) {
            $result['msg'] = '请选择要删除的检测项目';
            return $result;
        }

        if ($this->jys_db_helper->delete('detection_project', $project_id)) {
            $result['success']  = TRUE;
            $result['msg']      = '删除成功';
        }
        return $result;
    }
}
