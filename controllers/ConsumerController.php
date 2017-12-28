<?php

namespace xiaochengfu\kafka\controllers;


use yii\console\Controller;

/**
 * RabbitMQ consumer functionality
 * @package mikemadisonweb\rabbitmq\controllers
 */
class ConsumerController extends Controller
{
    /**
     * Description:  启动消费进程
     * Author: hp <xcf-hp@foxmail.com>
     * Updater:
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStart(){
        $setting = \Yii::$app->params['kafka'];
        $conf = new \RdKafka\Conf();
        $conf->set('sasl.mechanisms', 'PLAIN');
        $conf->set('api.version.request', 'true');
        $conf->set('sasl.username', $setting['sasl_plain_username']);
        $conf->set('sasl.password', $setting['sasl_plain_password']);
        $conf->set('security.protocol', 'SASL_SSL');
        $conf->set('ssl.ca.location', dirname(dirname(__FILE__))  . '/ca-cert');

        $conf->set('group.id', $setting['consumer_id']);

        $conf->set('metadata.broker.list', $setting['bootstrap_servers']);

        $topicConf = new \RdKafka\TopicConf();

        $topicConf->set('auto.offset.reset', 'smallest');

        $conf->setDefaultTopicConf($topicConf);

        $consumer = new \RdKafka\KafkaConsumer($conf);

        $consumer->subscribe([$setting['topic_name']]);
        //设置进程名称
        $this->setProcessName(isset($setting['processName'])?$setting['processName']:'kafka-master');

        echo "------------------------kafak消费进程启动成功---------------------------------\n";
        echo "php-kafka start success process name ".$setting['processName']."\n";
        echo "Waiting for partition assignment... (make take some time when\n";
        echo "quickly re-joining the group after leaving it.)\n";
        echo "---------------------------kafak等待消费...-----------------------------------\n";

        while (true) {
            $message = $consumer->consume(120 * 1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $object = \Yii::createObject($setting['callback']);
                    $object->execute($message);
//                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }

    /**
     * Description:  设置kafka进程名称
     * Author: hp <xcf-hp@foxmail.com>
     * Updater:
     * @param $name
     */
    private function setProcessName($name){
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else {
            trigger_error(__METHOD__. " failed.require cli_set_process_title.");
        }
    }
}