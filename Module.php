<?php
namespace xiaochengfu\kafka;

use xiaochengfu\kafka\component\Producer;
use yii\base\Component;

class Module extends Component
{
    /**
     * Description:  投递消息
     * Author: hp <xcf-hp@foxmail.com>
     * Updater:
     * @param array $msg
     * @return bool
     */
    public function poll($msg = []){
        $producer = new Producer();
        return $producer->publish($msg);
    }
}
