<?php
/**
 * Created by PhpStorm.
 * User: pengyongsheng
 * Date: 2018/8/23
 * Time: 上午9:10
 */
namespace traits;
trait log{
    /**
     * 简单调试功能
     */
    function write($data)
    {
        file_put_contents($this->file_path,$data);
    }
}