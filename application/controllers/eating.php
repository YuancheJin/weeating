<?php

/**
 * Created by PhpStorm.
 * User: halion
 * Date: 2016/3/9
 * Time: 10:36
 * 数据库id不存在session中，而是写到页面，页面每次走接口传进来，这样不会有session过期的问题
 */
class Eating extends CI_Controller {

    //构造函数
    function __construct() {
        //重写构造函数莫忘包含父类构造，controller功能会出错
        parent::__construct();
        //加载model
        //$this->load->model('calculate_model');
        //使用url相关的'辅助函数'(保证页面base_url可用),
        //对应system/helpers/url_helper.php
        //但这样写，只能应用在当前controller和对应的view里，因为它用处广，故而放到config/autoload.php的helper数组里, 用于全局使用
        //$this->load->helper('url');
        //相应的，这些libraries也可在autoload里加载
        $this->load->database();
        $this->load->library('halioncurl');
    }

    function index2() {
        $this->load->view('test_view');
        
    }

    function index() {

        //$openId = $this->code2Openid();
        //echo json_encode($openId);
        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&appid=wx3bcd55810031ddf9&secret=bc1ce901f955dbabccae40c7dadfd970&code=".$code;
        //成功格式为: {access_token:'', openid:'', refresh_token:'', scope:'user_info', expires_in:7200}
        //微信接口统一报错格式为: {errcode:41008, errmsg:"missing code"}
        $res = $this->halioncurl->get_curl_value($url);
        $res = json_decode($res);
        if (!$res->errcode) {
            //echo '无错误码，openid='. $res->open_id;
            $openid = $res->openid;
            $access_token = $res->access_token;
            //用openid和access_token获取用户昵称
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
            //成功格式为:{openid:'',nickname:'halion',...}
            $res = $this->halioncurl->get_curl_value($url);
            $res = json_decode($res);
            if (!$res->errcode) {
                //1. 获取用户的nickname
                $nickname = $res->nickname;
                //2. 取出数据库数据的openid字段，进行比对
                $openIdRes = $this->db->query("select * from members where openid='".$openid."'");
                $openIdResArr = $openIdRes->result();
                if ($openIdResArr) {
                    $openIdMember = $openIdResArr[0];
                    echo "库里有您的openid，欢迎回来, 进入确认加班餐view页";
                    //进入点餐view
                    $data = array (
                        id => $openIdMember->id
                    );
                    //$this->load->view('xxx', $data);

                } else {
                    echo "库里没有您的openid，接下来会用nickname再做比对";
                    //3. 取出数据库数据的nickname字段，进行比对
                    $res = $this->db->query("select * from members where nickname='".$nickname."'");
                    $resArr = $res->result();
                    if ($resArr) {
                        //库里有匹配的nickname，是国金人
                        $member = $resArr[0];
                        //比对openid，若无匹配（1说明库里没有该人，或该人第一次访问，库里仅有他昵称，无openid）
                        if ($member->openid == null || $member->openid == "") {
                            //第一次访问，需在库中记录用户的openid
                            $id = $member->id;
                            $where = "id=".$id;
                            $data = array(
                                'openid' => $openid
                            );
                            $res = $this->db->update('members', $data, $where);
                            if ($res) {
                                echo '第一次访问，openid更新成功, 开始点餐';
                                //进入点餐view
                                $data = array (
                                    id => $id
                                );
                                //$this->load->view('xxx', $data);
                            } else {
                                echo '第一次访问，openid更新失败, 宝宝摔倒了';
                            }
                        } else {
                            //非第一次访问，可以直接使用确认加班餐的功能了
                            echo '非第一次访问，您可以直接确认加班餐了';
                        }
                    } else {
                        //库里无匹配的nickname，不是国金人
                        echo '登记过您的部门和微信昵称后，就可微信确认加班餐了，快行动吧！';
                    }
                }
            } else {
                log_message('debug', 'openId2Nickname errcode='.$res->errcode. 'errmsg='.$res->errmsg);
            }
        } else {
            log_message('debug', 'code2OpenId errcode='.$res->errcode. 'errmsg='.$res->errmsg);
        }



    }


    /**
     * 查询点餐总数
     * http://localhost/weeating/index.php/eating/get_total
     * http://nz-kj.com/weeating/index.php/eating/get_total
     */
    function get_total() {
        $retArr = null;
        $res = $this->db->query('select amount from members');
        $result = $res->result();
        if ($result) {
            $total = 0;
            foreach ($result as $member) {
                $amount = $member->amount;
                $total += $amount; //php中null也可以和数字相加，当0运算
            }
            $retArr = array(
                code => 0,
                total => $total,
                date => time(),
                msg => ''
            );
        } else {
            $retArr = array(
                code => 100,
                date => time(),
                msg => 'get total failed'
            );
        }
        echo json_encode($retArr);
    }

    /**普通订餐
     * @param $id int 用户的id
     * @param $amount int 点餐数量
     * http://localhost/weeating/index.php/eating/order/2/1
     * http://nz-kj.com/weeating/index.php/eating/order/1/3
     */
    function order($id, $amount) {
        $retArr = null;
        $data = array(
            'amount' => $amount
        );
        $where = "id=".$id;
        $res = $this->db->update('members', $data, $where);
        if ($res) {
            $retArr = array(
                code => '0',
                date => time()
            );
        } else {
            $retArr = array(
                code => '101',
                date => time(),
                msg => 'order failed'
            );
        }
        echo json_encode($retArr);
    }


    function cnct() {
        $this->load->view('cnct_view');
    }


    function dbtest() {
        /*
        //insert
        $data = array(
            'department' => 2,
            'nickname' => 'dahuang'
        );
        //这里有坑，若连不上db，$res不会返回，后面语句不会执行, 成功$res=1
        $res = $this->db->insert('members', $data);
        if ($res) {
            echo '插入成功';
        } else {
            echo '插入失败';
        }
        */

        /*
        //update
        $data = array(
            'nickname'=>'xiaohei',
            'department'=>12
        );
        $where = "id = 5";
        //注意，如果where条件未匹配到，$res还是1
        $res = $this->db->update('members', $data, $where);
        if ($res) {
            echo 'update succeed';
        } else {
            echo 'update failed';
        }
        */

        /*
        //select
        //若sql语句无错，为查询到进入else，但若sql有错，比如条件字段或表名不存在等，代码隐式报错，写入日志不向下执行
        $res = $this->db->query('select nickname from members where id=2');
        print_r($res->result());
        if ($res->result()) {
            echo '查询陈宫';
        } else {
            echo '未查询到';
        }
        */

    }


    //code换openid
    private function code2Openid() {
        //获取code
        $code = $_GET['code'];
        log_message('debug', 'in code2Openid code='.$code);
        $wx_code_to_token = $this->config->item("wx_code_to_token");
        //https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&appid=wx3bcd55810031ddf9&secret=bc1ce901f955dbabccae40c7dadfd970&code=001d50d594076718268cda2cf62aff40
        //echo $wx_code_to_token;
        $code = "001d50d594076718268cda2cf62aff40";
        return $this->halioncurl->get_curl_value($wx_code_to_token."&code=".$code);
    }

    function get_curl_value($url)
    {
        try
        {
            _log("debug", "In get_curl_value: ".remove_password($url), $log='page');
            $ch = curl_init();
            if($_[0] == 'https:')
            {
                curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 28);
            $res=curl_exec($ch);
            $errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
        }
        catch(Exception $e)
        {
            $errno = "";
            $res = array();
            _log("error", "Out get_curl_value: ".remove_password($url).' -- '.$e->getMessage(), $log='error');
            _log("error", "Out get_curl_value: ".remove_password($url).' -- '.$e->getMessage(), $log='page');
        }

        if($errno != 0)
        {
            $res = json_encode(array('code'=>-999, 'message'=>'timeout'));
            _log("error", "Out get_curl_value: error_no: ".remove_password($url).' -- '.$errno." error: ".$curl_error, $log='page');
        }
        else
        {
            _log("debug", "Out get_curl_value: ".remove_password($url).' -- '.remove_password($res), $log='page');
        }
        return $res;
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


    //用于向数据库插入新统计的订餐员工nickname; http://localhost/weeating/index.php/eating/new_members
    function readme() {
        /*
         * 录入sql
        INSERT INTO members(department, nickname) VALUES (3, 'halion');
        INSERT INTO members(department, nickname) VALUES (3, '自愚自樂');
        INSERT INTO members(department, nickname) VALUES (3, 'yancy');
         */

        /*
         * department
         1. 领导+其他
         2. java
         3. web
         4. 终端
         6. 数据
         7. 测试
         8. 运维
         9. 市场
         10. 财务
         */


    }




}