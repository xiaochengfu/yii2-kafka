# yii2-kafka
yii2-kafka消息队列,对接阿里云的kafak消息队列

### 配置
在common/config/params.php 添加如下参数：
```
<?php
return [
    'kafka'=>[
        'sasl_plain_username' => '填写阿里云的Access Key ID',
        'sasl_plain_password' => '填写阿里云的Access Key Secret后10位',
        'bootstrap_servers' => "kafka-ons-internet.aliyun.com:8080",
        'topic_name' => '您的topic名称',
        'consumer_id' => '您的CID',
        'processName'=>'kafka-master',//j进程名称
        'callback'=>'common\\components\\kafka\\Consumer'//消费业务回调处理名字空间
    ]
];
```


### 启动消费进程

```
./yii kafka-consumer/start
```

### 发布消息

```
Yii::$app->mq->poll(['1'=>'我收到了','2'=>'我成功了'])
```