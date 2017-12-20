<?php
/**
 * =====================================================================================
 *
 *        Filename: tool_helper.php
 *
 *     Description: 工具帮助类
 *
 *         Created: 2017-07-10 18:05:05
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */

/**
 * 数组转化为字符串
 * @param $arr array 数组
 * @param $delimiter string 分隔符
 * @return string
 */
function array_to_string($arr = [], $delimiter = ', ')
{
    $str = '';

    if (is_array($arr)) {
        foreach ($arr as $row) {
            if (is_array($row)) {
                $str .= $this->array_to_string($row);
            } else {
                $str .= $row.$delimiter;
            }
        }
    } else {
        $str .= $arr;
    }

    return $str;
}

/**
 * 打印数据并中断
 * @param $data 传入的需要打印的参数
 */
function dd($data)
{
    echo "<pre>";
    var_dump($data);die;
}

/**
 * 打印数据并中断
 * @param $data 传入的需要打印的参数
 */
function aa($data)
{
    echo "<pre>";
    print_r($data);die;
}

/**
 * UTF8编码转换成GBK
 * @param string $s 需要转换的字符串
 * @return string
 */
function utf8_to_gbk($s) {

    return iconv('utf-8', 'gbk', $s);  // IGNORE 参数是遇到不成转换的字符时忽略

}

?>