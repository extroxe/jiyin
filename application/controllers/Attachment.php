<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Attachment.php
 *
 *     Description: 附件控制器
 *
 *         Created: 2016-11-15 19:19:38
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class Attachment extends CI_Controller {

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_attachment', 'form_validation']);
        $this->load->library(['Jys_excel']);
        $this->load->model(['Common_model', 'Report_model']);
    }

    /**
     * 上传附件
     *
     * @param bool $clip_avatar 上传剪切头像
     */
    public function up_attachment(){
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;
        if (isset($_POST['str'])) {
            $dataUrl = $_POST['str'];
        }

        //开始事务
        $this->db->trans_start();
        //上传附件
        if (!empty($dataUrl)){
            $result = $this->jys_attachment->upload_clip_avatar($dataUrl);
        }else{
            $result = $this->jys_attachment->upload();
        }

        if ($result['success']){
            $info = $this->save_path_to_database($result);
            if (intval($info['attachment_id']) > 0) {
                $data['success'] = TRUE;
                $data['msg'] = '上传附件成功';
                $data['attachment_id'] = $info['attachment_id'];
                $data['url'] = '/'.$info['path'];

                if (isset($_POST['avatar_flag']) && !empty($_POST['avatar_flag'])){
                    $this->jys_db_helper->update('user', $_SESSION['user_id'], ['avatar'=>$data['attachment_id']]);
                    $_SESSION['avatar_path'] = $data['url'];
                }
            }else {
                $data['msg'] = "附件信息保存失败";
            }

        }else{
            $data['msg'] = $result['msg'];
        }

        if ($data['success']){
            //上传成功，完成事务
            $this->db->trans_complete();
        }else{
            //上传失败，回滚
            $this->db->trans_rollback();
            if (isset($result['path']) && !empty($result['path'])){
                unlink($result['path']);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $data['msg'] = '系统繁忙！请重新上传';
        }
        
        echo json_encode($data);
    }

    /**
     * 将文件信息保存到数据库中
     * @param $upload_info
     * @return bool
     */
    private function save_path_to_database($upload_info) {
        if (empty($upload_info)) {
            return FALSE;
        }

        //处理数据
        $upload_info['create_time'] = date('Y-m-d H:i:s');
        //插入附件信息
        $file = $this->jys_db_helper->get_where('attachment', ['md5'=>$upload_info['md5']]);

        if (!empty($file) && is_array($file)) {
            // 文件已存在
            unlink($upload_info['path']);
            $data['success'] = TRUE;
            $data['msg'] = '上传附件成功';
            $data['path'] = $file['path'];
            $attachment_id = $file['id'];
        }else {
            $data = $this->jys_attachment->save_attachment($upload_info);
            $attachment_id = $this->db->insert_id();
        }

        if ($data['success']){
            $data['success'] = $this->jys_attachment->save_attachment_user($attachment_id);
            $data['attachment_user_id'] = $this->db->insert_id();
            $data['attachment_id']      = $attachment_id;
        }else {
            return FALSE;
        }

        return $data;
    }
    /**
     * 上传报告附件
     */
    public function upload_report() {
        $data['msg'] = "上传报告附件失败";
        $data['success'] = FALSE;

        //开始事务
        $this->db->trans_start();

        $result = $this->jys_attachment->upload('source/uploads/report/');
        if ($result['success']){
            $info = $this->save_path_to_database($result);
            if (intval($info['attachment_id']) > 0) {
                $data['success'] = TRUE;
                $data['msg'] = '上传附件成功';
                $data['attachment_id'] = $info['attachment_id'];
                $data['url'] = '/'.$info['path'];
            }else {
                $data['msg'] = "附件信息保存失败";
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $data['msg'] = "数据库存储失败";
        }else {
            $data['success'] = TRUE;
            $data['msg'] = "上传报告附件成功";
        }

        echo json_encode($data);
    }

    /**
     * 上传快递公司导入模版
     */
    public function up_express_company(){
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;
        $flag = FALSE;

        //开始事务
        $this->db->trans_start();
        //上传附件
        $result = $this->jys_attachment->upload_excel_attachment();

        if ($result['success']){
            $express_data = $this->jys_excel->export_excel($result['path']);
            if(!empty($express_data && is_array($express_data))){
                    $res = $this->jys_db_helper->add_batch('express_company', $express_data);
                    if ($res['success']){
                        $flag = TRUE;
                    }
            }
        }
        if ($this->db->trans_status() === FALSE){
            $data['msg'] = '系统繁忙！请重新上传';
            //上传失败，回滚
            $this->db->trans_rollback();
        }else if($result['success'] && $flag){
            //上传成功，完成事务
            $this->db->trans_complete();
            $data['msg'] = "快递公司表格导入成功";
            $data['success'] = TRUE;
        }
        echo json_encode($data);
    }

    /**
     * 这是实现下载快递公司模版函数
     */
    public function express_company_download(){
        header("Content-type:text/html;charset=utf-8");
        $file_path = 'source/excel/express_company_template.xlsx';
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            echo "没有该文件";
            return ;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        header('Content-Transfer-Encoding:binary');
        header("Content-Type:application/vnd.ms-execl");
        Header("Content-Disposition: attachment; filename=".'快递公司导入模版.xlsx');
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

    /**
     * 校验文件MD5码是否存在
     */
    public function check_md5(){
        $md5_code = $this->input->post('md5_code', TRUE);

        $data = $this->jys_attachment->check_md5_code($md5_code);

        echo json_encode($data);
    }

     /**
     * 这是实现下载报告函数
     */
    public function report_download($id){
        header("Content-type:text/html;charset=utf-8");
        $data = $this->Report_model->get_path_by_report_id($id);
        if (empty($data) || !is_array($data)) {
            echo "未查询到该报告";
            return ;
        }
        $file_sub_path = $data['path'];
        $file_path=$file_sub_path;
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            echo "没有该文件";
            return ;
        }
        $file_name = basename($file_path);
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes"); 
        Header("Accept-Length:".$file_size); 
        Header("Content-Disposition: attachment; filename=".$file_name); 
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }
}