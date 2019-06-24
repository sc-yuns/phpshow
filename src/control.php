<?php
/**
 * control基类
 * Created by PhpStorm.
 * User: shengsheng
 * Date: 2018/7/19
 * Time: 上午12:37
 */

namespace phpshow;
use \phpshow\lib\tpl;

class control
{
    //是否ajax请求
    public $is_ajax = false;
    //默认使用的页数
    public $pageSize = '20';
    public $commander;
    public function __construct()
    {
        if( PHP_SAPI == 'cli' )
        {
            $this->commander = \phpshow\lib\command();
        }

        $this->is_ajax = $this->is_ajax();
        if(!self::_Acl())
        {
            die('no access');
        }
    }
    /**
     * 默认选择时间的范围
     */
    public static function dateRange($day=7)
    {
        //7天之内
        $endtime = date("Ymd",time());
        $starttime = date("Ymd",time()-(86400*$day));
        return "{$starttime} / {$endtime}";
    }
    /**
     * 判断权限
     */
    public function _Acl()
    {
        return true;
    }

    /**
     * 判断是否ajax请求
     * @return bool
     */
    public function is_ajax()
    {
        defined("PS_ISAJAX") or define("PS_ISAJAX","0");
        if(PS_ISAJAX) return true;
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
    }

    /**
     * 输出版本
     */
    final public function welcome()
    {
        echo 'phpshow';
    }
}