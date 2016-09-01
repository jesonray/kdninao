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

    /**
     * 订阅快递信息
     * @param string $code      快递单号
     * @param string $shipper   快递公司
     * @param string $token     识别token, 增强安全性
     * @return bool
     */
    public function subscribe($code, $shipper='', $token='')
    {
        if( !$shipper ) {
            $shippers = $this->shipper($code);
            if( !$shippers ) {
                return false;
            }

            $shipper = $shippers[0]['ShipperCode'];
        }

        $requestData= "{'CallBack':'{$token}','ShipperCode':'{$shipper}','LogisticCode':'{$code}'}";

        $datas = array(
            'EBusinessID' => $this->appId,
            'RequestType' => '1008',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, $this->appKey);
        $json = $this->httpPost($datas);

        return $json && $json['Success']==true;
    }

    /**
     * 解析回调数据
     * @return Track|bool
     */
    public function parseCallback()
    {
        $json = json_decode(file_get_contents('php://input'), 1);
        if( !$json ) {
            return false;
        }

        $tracks = [];
        foreach($json['Data'] as $one) {
            $tracks[] = new Track($one);
        }

        return $tracks;
    }

    /**
     * 查询快递公司编码
     * @param $code
     * @return mixed
     */
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
     * 返回数据
     * @param bool $success
     * @param string $reason
     */
    public function response($success, $reason='')
    {
        $data = [
            'EBusinessID' => $this->appId,
            'UpdateTime' => date('Y-m-d H:i:s'),
            'Success' => (bool)$success,
            'Reason' => $reason
        ];

        ob_clean();
        echo json_encode($data);
        die;
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
