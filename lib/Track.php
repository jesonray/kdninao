<?php
/**
 * Created by PhpStorm.
 * User: Ray
 * Date: 8/30/16
 * Time: 9:56 AM
 */

namespace raysoft\kdniao\lib;


use yii\base\Object;

class Track extends Object
{
    // 在途件
    const STATE_TRANSIT = '2';

    // 已签收
    const STATE_RECEIVED = '3';

    // 问题件
    const STATE_ERROR = '4';

    public $shipperCode;

    public $logisticCode;

    public $state;

    public $traces;

    public $success;

    public $reason;

    public function __construct(array $config)
    {
        $tmp = [];
        foreach($config as $k=>$v) {
            $tmp[lcfirst($k)] = $v;
        }
        parent::__construct($tmp);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    public function isReceived()
    {
        return $this->state == self::STATE_RECEIVED;
    }
}