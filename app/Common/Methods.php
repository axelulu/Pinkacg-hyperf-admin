<?php


namespace App\Common;


use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Jenssegers\Agent\Agent;

class Methods
{

    /**
     * getLogArguments
     * 获取要存储的日志部分字段，monolog以外的业务信息
     * User：YM
     * Date：2019/12/20
     * Time：下午12:57
     * @param float $executionTime 程序执行时间，运行时才能判断这里初始化为0
     * @param int $rbs 响应包体大小，初始化0，只有正常请求响应才有值
     * @return array
     */
    public function getLogArguments($executionTime = null,$rbs = 0): array
    {
        if (Context::get('http_request_flag') === true) {
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
            $requestHeaders = $request->getHeaders();
            $serverParams = $request->getServerParams();
            $arguments = $request->all();
            $url = $request->fullUrl();
        } else {
            $requestHeaders = $serverParams = $arguments = [];
        }
        $uuid = $userId = '';
        $agent = new Agent();
        $agent->setUserAgent($requestHeaders['user-agent'][0]??'');
        $ip = $requestHeaders['x-real-ip'][0]??$requestHeaders['x-forwarded-for'][0]??'';
        // ip转换地域
        if($ip && ip2long($ip) != false){
            $location = $this->getIpLocation($ip);
            $cityId = $location['city_id']??0;
        }else{
            $cityId = 0;
        }
        return [
            'qid' => $requestHeaders['qid'][0]??'',
            'server_name' => $requestHeaders['host'][0]??'',
            'server_addr' => $this->getServerLocalIp()??'',
            'remote_addr' => $serverParams['remote_addr']??'',
            'forwarded_for' => $requestHeaders['x-forwarded-for'][0]??'',
            'real_ip' => $ip,
            'city_id' => $cityId,
            'user_agent' => $requestHeaders['user-agent'][0]??'',
            'platform' => $agent->platform()??'',
            'device' => $agent->device()??'',
            'browser' => $agent->browser()??'',
            'url' => $url,
            'uri' => $serverParams['request_uri']??'',
            'arguments' => $arguments?json_encode($arguments):'',
            'method' => $serverParams['request_method']??'',
            'execution_time' => $executionTime,
            'request_body_size' => (int)$requestHeaders['content-length'][0]??'',
            'response_body_size' => (int)$rbs,
            'uuid' => $uuid,
            'user_id' => $userId??'',
            'referer' => $requestHeaders['referer'][0]??'',
            'unix_time' => (int)$serverParams['request_time']??'',
            'time_day' => isset($serverParams['request_time'])?date('Y-m-d',$serverParams['request_time']):'',
            'time_hour' => isset($serverParams['request_time'])?date('Y-m-d H:00:00',$serverParams['request_time']):'',
        ];
    }

    /**
     * getServerLocalIp
     * 获取服务端内网ip地址
     * User：YM
     * Date：2019/12/19
     * Time：下午5:48
     * @return string
     */
    public function getServerLocalIp(): string
    {
        $ip = '127.0.0.1';
        $ips = array_values(swoole_get_local_ip());
        foreach ($ips as $v) {
            if ($v && $v != $ip) {
                $ip = $v;
                break;
            }
        }

        return $ip;
    }

    /**
     * getIpLocation
     * 获取ip对应的城市信息
     * User：YM
     * Date：2020/2/19
     * Time：下午8:42
     * @param $ip
     * @return mixed
     */
    public function getIpLocation($ip)
    {
        $dbFile = BASE_PATH . '/app/Common/ip2region.db';
        $ip2regionObj = new Ip2Region($dbFile);
        $ret = $ip2regionObj->binarySearch($ip);
        return $ret;
    }

}