<?php
/**
 * response类
 * Author:shengsheng
 */
namespace phpshow;
class response
{
    public static $connection = null;
    public static $hander = null;
    public static $cors = [];
    public static function setConnection($response)
    {
        self::$connection = $response;
    }

    /**
     * cors跨域请求
     */
    public static function setCors($host = '*')
    {
        self::$cors = [
            'Access-Control-Allow-Origin' => $host,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ];
    }

    /**
     * 向客服端输出内容
     */
    public static function send($result)
    {
        if(self::$connection)
        {
            self::$connection->send(new \Workerman\Protocols\Http\Response(200, self::$cors, $result));
        }else{
            echo $result;
        }
    }

    /**
     * 输出结尾
     */
    public static function end($result)
    {
        if(self::$connection)
        {
            self::$connection->send(new \Workerman\Protocols\Http\Response(200, self::$cors, $result));
        }else{
            echo $result;
        }
    }

    /**
     * 向客服端写入内容
     */
    public static function write($result)
    {
        if(self::$connection)
        {
            self::$connection->write($result);
        }else{
            echo $result;
        }
    }

    /**
     * 输出头部信息
     */
    public static function Header($data = '')
    {
        if($data)
        {
            header($data);
        }else{
            header('Content-Type: text/html; charset=utf-8');
        }
    }

    /**
     * 设置响应状态码
     */
    public static function code($type = "success")
    {
        $arr_type = [
            'error' => 500,
            //Unauthorized
            'unauth' => 401,
            //Forbidden
            'forbidden' => 403,
            //默认success
            'success' => 200,
            'notfound' => 404,
        ];
        if(!isset($arr_type[$type]))
        {
            $type = "success";
        }
        $code = $arr_type[$type];
        http_response_code($code);
    }

    /**
     * url跳转
     * @param string $url
     */
    public static function redirect($url="")
    {
        header('Location:'.$url);
        exit();
    }

    /**
     * 禁用服务器缓存
     */
    public static function noCache()
    {
        header('Cache-Control:no-cache,must-revalidate');
        header('Pragma:no-cache');
        header("Expires:0");
    }
    
    /**
     * json返回
     */
    public static function toJson($result)
    {
        if(self::$connection)
        {
            self::$connection->send(new \Workerman\Protocols\Http\Response(200, self::$cors, json_encode($result)));
        }else{
            echo json_encode($result);
        }
    }

    /**
     * 输出json
     */
    public static function json($code=0,$msg='',$data='')
    {
        $result = self::returnArray($code,$msg,$data);
        $json_result = json_encode($result);
        if(self::$connection)
        {
            self::$connection->send($json_result);
        }else{
            echo $json_result;
        }

    }

    /**
     * 返回json
     * @param string $code
     * @param string $msg
     * @param string $data
     */
    public static function returnjson($code='0',$msg='',$data='')
    {
        $result = self::returnArray($code,$msg,$data);
        return json_encode($result);

    }
    
    /**
     * 通用返回
     */
    public static function commonData($data,$page,$pageSize)
    {
        $rows['list'] = $data['list'];
        $rows['pagination'] = [
            'total' => (int)$data['total'],
            'pageSize' => (int)$pageSize,
            'current' => (int)$page,
        ];
        return $rows;
    }
    /**
     * 返回array
     * @param string $code
     * @param string $msg
     * @param string $data
     */
    public static function returnArray($code='0',$msg='',$data='')
    {
        $result = array(
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        );
        return $result;
    }

    /**
     * 输出xml
     */
    public static function xml($data)
    {
        //构造xml数据格式
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<data>\n";
        foreach ($data as $item_arr) {
            $item = "<item>\n";
            foreach($item_arr as $ikey=>$ival)
            {
                //循环构造xml单项
                $item .= "<{$ikey}>" . $ival . "</{$ikey}>\n";
            }
            $item .= "</item>\n";
            $xml .=$item;
        }
        $xml .= "</data>";

        return $xml;
    }

    /**
     * 设置cli输出颜色
     * 更改为color
     * @param $text
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function color($text, $status='1')
    {
        //1 SUCCESS  2 FAILURE  3 WARNING 4 NOTE
        switch($status) {
            case "1":
                $out = "[42m"; //Green background
                break;
            case "2":
                $out = "[41m"; //Red background
                break;
            case "3":
                $out = "[43m"; //Yellow background
                break;
            case "4":
                $out = "[44m"; //Blue background
                break;
            default:
                $out = "";
                break;
        }
        echo chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }

    //----------------------------------------------------------
    /**
     * json to array
     */
    public static function jta($data)
    {
        $data = json_decode($data,true);
        echo "====================\n";
        echo "array(\n";
        foreach($data as $key=>$val)
        {
            self::JsonToArr($val,"\$arr['$key']");
        }
        echo ")";
    }

    /**
     * json to config
     * @param $a
     */
    public static function jtoc($data)
    {
        foreach($data as $key=>$val)
        {
            self::JsonToArr($val,"\$arr[$key]");
        }
    }
    /**
     * 数组配置文件式
     */
    public static function configArr($arr,$e='')
    {
        if(is_array($arr))
        {
            foreach($arr as $k=>$v)
            {
                $kk = $e."['".$k."']";
                self::configArr($v,$kk);
            }
        }else{
            echo $e."=\"".$arr."\";\n";
        }
    }
    /**
     * 一行输出数组
     */
    public static function lineArr($arr)
    {
        if(is_array($arr))
        {
            echo "array(";
            foreach($arr as $k=>$v)
            {
                if(!is_array($v))
                {
                    echo "\"{$k}\"=>\"{$v}\",";
                }
                self::lineArr($v);
            }
            echo "),\n";
        }
    }
    /**
     * json to array
     */
    public static function JsonToArr($arr,$i=1)
    {
        $str = str_repeat(" ",$i);
        $str2 = str_repeat(" ",$i+1);
        if(is_array($arr))
        {
            echo "\n$str array(\n";
            foreach($arr as $k=>$v)
            {
                echo $str2.'"'.$k."\"=>";
                if(is_array($v))
                {
                    $j = $i+2;
                }else{
                    $j = $i;
                }
                self::JsonToArr($v,$j);
            }
            echo $str."),\n";
        }else{
            echo '"'.$arr.'",'."\n";
        }
    }

}
