<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/3/9
 * Time: 11:06
 */
class Calculate_model extends CI_Model {

    //计算
    function count($num1, $num2, $op) {
        if ($op == "+") {
            return $num1 + $num2;
        } else if ($op == "-") {
            return $num1 - $num2;
        } else if ($op == "x") {
            return $num1 * $num2;
        } else if ($op == "÷" && $num2 != 0) {
            return $num1 / 1.0 / $num2;
        } else {
            return FALSE;
        }
    }

}