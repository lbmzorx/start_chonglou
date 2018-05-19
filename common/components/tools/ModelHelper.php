<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/25
 * Time: 15:51
 */

namespace common\components\tools;


class ModelHelper
{
    public static function getErrorAsString($errors){
        $err = '';
        foreach ($errors as $k=>$v) {
            $err .=$k.':'.$v[0] . '<br>';
        }
        return $err;
    }

    public static function clearRules($attribute,$rules){
        foreach ($rules as $k=>$rule){
            if(isset($rules[0])){
                if(is_string($rule[0]) && $rule[0]==$attribute){
                    unset($rules[$k]);
                }
                if(is_array($rule[0]) && in_array($attribute,$rule[0])){
                    if(count($rule[0])>1){
                        $flip = array_flip($rule[0]);
                        unset($rules[$k][$flip[$attribute]]);
                    }else{
                        unset($rules[$k]);
                    }
                }
            }
        }
    }
}