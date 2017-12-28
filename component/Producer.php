<?php
/**
 * 消费组件
 * time:2017-05-27
 * author:houpeng
 */
namespace xiaochengfu\kafka\component;

use Yii;
use yii\base\Exception;

class Producer extends \yii\base\Component
{

    /**
     * Description:  发布消息
     * Author: hp <xcf-hp@foxmail.com>
     * Updater:
     * @param $msg
     * @return bool
     */
    public function publish($msg){
        try{
            $setting = \Yii::$app->params['kafka'];
            $conf = new \RdKafka\Conf();
            $conf->set('sasl.mechanisms', 'PLAIN');
            $conf->set('api.version.request', 'true');
            $conf->set('sasl.username', $setting['sasl_plain_username']);
            $conf->set('sasl.password', $setting['sasl_plain_password']);
            $conf->set('security.protocol', 'SASL_SSL');
            $conf->set('ssl.ca.location', dirname(dirname(__FILE__))  . '/ca-cert');
            $conf->set('message.send.max.retries', 5);
            $rk = new \RdKafka\Producer($conf);
            $rk->setLogLevel(LOG_DEBUG);
            $rk->addBrokers($setting['bootstrap_servers']);
            $topic = $rk->newTopic($setting['topic_name']);
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, serialize((array)$msg));
            $rk->poll(0);
            while ($rk->getOutQLen() > 0) {
                $rk->poll(50);
            }
            return true;
        }catch (Exception $e){
            return false;
        }
    }
    
}