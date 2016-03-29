<?php

/**
 * Created by PhpStorm.
 * User: halion
 * Date: 2016/3/10
 * Time: 21:46
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class HalionCurl {

    //发送http请求 get
    public function get_curl_value($url) {
        try {
            log_message('debug', "In get_curl_value: ".($url));
            $ch = curl_init();//初始会话
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0); //给会话添加相关设置
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //用于解决curl的特殊报错
            $res=curl_exec($ch); //执行一个会话
            $curl_errno = curl_errno($ch);//获取会话的错误码
            $curl_error = curl_error($ch);//获取会话的错误信息
            curl_close($ch);//关闭一个会话
        } catch(Exception $e) {
            $curl_errno = "";
            $res = array();
            log_message("error", "Out get_curl_value: ".($url).' -- '.$e->getMessage());
        }
        if ($curl_errno > 0) {
            $res = json_encode(array('code'=>-999, "message" => "通讯超时"));
            log_message("error", "Out get_curl_value: error_no: ".$curl_errno." error: ".$curl_error);
        }
        $bin = substr($res, 0, 1);
        if($bin == "{") {
            // 正常json文字结果
            log_message('debug', "Out get_curl_value: ".($res));
        }
        return $res;
    }

    //发送http请求 post
    function post_curl_value($url, $data_string) {
        try {
            log_message('debug', "In post_curl_value: ".$url.' -- '.($data_string));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string))
            );
            ob_start();
            curl_exec($ch);
            $return_content = ob_get_contents();
            ob_end_clean();
            $return_code = curl_errno($ch);
        } catch(Exception $e) {
            $return_code = "";
            $return_content = "";
            log_message("error", "Out post_curl_value2: ".($url).' -- '.$e->getMessage());
        }
        if($return_code > 0) {
            log_message("error", "Out post_curl_value: error_no: ".$return_code." error: ".($return_content));
            return json_encode(array('code'=>-999, "message" => "通讯超时"));
        } else {
            log_message("debug", "Out post_curl_value: ".($return_content));
            return $return_content;
        }
    }

}