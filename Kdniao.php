<?php
/**
 * Created by PhpStorm.
 * User: Jesonray
 * Date: 8/27/16
 * Time: 11:22 AM
 */
namespace raysoft\kdniao;

use Requests;
use raysoft\kdniao\lib\Track;
use yii\base\Object;

class Kdniao extends Object
{
    // 商户ID
    public $appId;
    // App Key
    public $appKey;

    public $apiUrl = 'http://api.kdniao.cc/api/dist';

    /**
     * 即时查询快递
     * @param string $shipper 快递公司代码
     * @param string $code 快递单号
     * @return \raysoft\kdniao\lib\Track
     */
    public function track($code, $shipper='')
    {
        if( !$shipper ) {
            $shippers = $this->shipper($code);
            if( !$shippers ) {
                return false;
            }

            $shipper = $shippers[0]['ShipperCode'];
        }

        $requestData= "{'OrderCode':'','ShipperCode':'{$shipper}','LogisticCode':'{$code}'}";

        $datas = array(
            'EBusinessID' => $this->appId,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, $this->appKey);
        $json = $this->httpPost($datas);
        $track = new Track($json);

        return $track;
    }


    public function shipper($code)
    {
        $requestData= "{'LogisticCode':'{$code}'}";

        $datas = array(
            'EBusinessID' => $this->appId,
            'RequestType' => '2002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, $this->appKey);
        $json = $this->httpPost($datas);
        return $json['Shippers'];
    }

    /**
     * HTTP请求数据
     * @param $data
     * @return mixed
     */
    private function httpPost($data)
    {
        $result = Requests::post($this->apiUrl, [], $data);
        $json = json_decode($result->body, 1);
        return $json;
    }

    /**
     * 生成签名
     * @param $data
     * @param $appkey
     * @return string
     */
    private function encrypt($data, $appkey)
    {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
}
