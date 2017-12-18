<?php
// +----------------------------------------------------------------------
// | 验证类
// +----------------------------------------------------------------------
// | Author: qh.cao
// +----------------------------------------------------------------------
class Valid
{
    protected static $instance = null;
    protected $validationRules = [];
    /**
     * 获取单例
     * @return [type] [description]
     */
    private static function getInstance()
    {
        if(self::$instance === null)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }
    /**
     * 执行有效性验证
     * @param  array   $data       参数数组
     * @param  array   $validators 规则数组
     * @return boolean             [description]
     */
    public function isValid(array $data, array $validators)
    {
        $instance = self::getInstance();
        $instance->validationRules($validators);
        if ($instance->validate($data) === false) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * 绑定规则数组属性
     * @param  array  $rules [description]
     * @return [type]        [description]
     */
    private function validationRules(array $rules = [])
    {
        if (empty($rules)) {
            return $this->validationRules;
        }
        $this->validationRules = $rules;
    }
    /**
     * 执行验证
     * @param  array  $input [description]
     * @return [type]        [description]
     */
    private function validate(array $input)
    {
        $inputKey  = array_keys($input);
        foreach ($this->validationRules as $field => $rules) {
            $rules = explode('|', $rules);
            // 检测是否必须
            if (in_array('required', $rules) && !isset($input[$field])) {
                return false;
            }
            if (!in_array('required', $rules) && !isset($input[$field])) {
                continue;
            }
            if (is_array($input[$field])) {
                return false;
            }
            $rules = array_diff($rules, ['required']);
            foreach ($rules as $rule) {
                $method = null;
                $param  = null;
                if (false !== strstr($rule, ',')) {
                    $rule   = explode(',', $rule);
                    $method = 'validate' . ucfirst(self::lineToHump($rule[0]));
                    $param  = $rule[1];
                } else {
                    $method = 'validate' . ucfirst(self::lineToHump($rule));
                }
                if (is_callable([$this, $method])) {
                    $result = call_user_func_array([$this, $method], [$input[$field], $param]);
                    if (false === $result) {
                        return false;
                    }
                } else {
                    throw new Exception("Validator method '$method' does not exist.");
                }
            }
        }
    }
    /**
     * 最小长度验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateMinLen($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input) >= (int) $param) {
                return true;
            }
        } else {
            if (strlen($input) >= (int) $param) {
                return true;
            }
        }
        return false;
    }
    /**
     * 最大长度验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateMaxLen($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input) <= (int) $param) {
                return true;
            }
        } else {
            if (strlen($input) <= (int) $param) {
                return true;
            }
        }
        return false;
    }
    /**
     * 字母验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateAlpha($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (!preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input) !== false) {
            return false;
        }
        return true;
    }
    /**
     * 字母下划线验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateAlphaDash($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $input) !== false) {
            return false;
        }
        return true;
    }
    /**
     * 字母下划线数字验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateAlphaNum($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input) !== false) {
            return false;
        }
        return true;
    }
    /**
     * 整数验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateInt($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (filter_var($input, FILTER_VALIDATE_INT) === false) {
            return false;
        }
        return true;
    }
    /**
     * 布尔验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateBool($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (filter_var($input, FILTER_VALIDATE_BOOLEAN) === false) {
            return false;
        }
        return true;
    }
    /**
     * 数字最大值验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateMaxNum($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (is_numeric($input) && is_numeric($param) && ($input <= $param)) {
            return true;
        }
        return false;
    }
    /**
     * 数字最小值验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateMinNum($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (is_numeric($input) && is_numeric($param) && ($input >= $param)) {
            return true;
        }
        return false;
    }
    /**
     * json字符串验证
     * @param  [type] $input 参数值
     * @param  [type] $param 比较直
     * @return [type]        [description]
     */
    protected function validateJson($input, $param = null)
    {
        if (empty($input)) {
            return false;
        }
        if (!is_string($input) || !is_object(json_decode($input))) {
            return false;
        }
        return true;
    }
    /**
     * 蛇形转驼峰
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    private static function lineToHump($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function($matches){
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }
    /**
     * 禁止克隆
     * @return [type] [description]
     */
    public function __clone()
    {
        return false;
    }
}
