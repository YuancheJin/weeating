<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/3/9
 * Time: 10:36
 */
class Calculate extends CI_Controller {

    //构造函数
    function __construct() {
        //重写构造函数莫忘包含父类构造，controller功能会出错
        parent::__construct();
        //加载model
        $this->load->model('calculate_model');
        //使用url相关的'辅助函数'(保证页面base_url可用),
        //对应system/helpers/url_helper.php
        //但这样写，只能应用在当前controller和对应的view里，因为它用处广，故而放到config/autoload.php的helper数组里, 用于全局使用
        //$this->load->helper('url');
        $this->load->database();
    }

    function index() {
        //加载calculate_view视图
        $this->load->view('calculate_view');
        //echo 'calculate index func';
    }

    function count() {
        //获取input参数
        $num1 = $this->input->get('num1'); //若ajax采用type:post，这里用post函数取值
        $op = $this->input->get('operate');
        $num2 = $this->input->get('num2');
        if (is_numeric($num1) && is_numeric($num2)) {
            //均为数字，调用calculate_model模型的count方法
            $result = $this->calculate_model->count($num1, $num2, $op);
            $resultArr = array (
                'code'=> 0,
                'result'=> $result
            );
            echo json_encode($resultArr);
        } else {
            $resultArr = array (
                'code'=> -1,
                'result'=> 'invalid param',
            );
            echo json_encode($resultArr);
        }

    }

    function form() {
        //$this->load->view('form_view');
        $query = $this->db->query('SELECT * FROM tb_1');
        foreach ($query->result() as $row)
        {
            //print_r($row);
            echo $row->id;
            echo $row->name;
            echo $row->age;
            echo $row->info;
            echo '<br>';
        }
    }





}