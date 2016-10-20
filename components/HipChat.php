<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * HipChat
 *
 */
class HipChat extends Component
{
    public $url = 'https://humandevice.hipchat.com/v2/room/';
    public $prod;
    public $test;
    public $mode = 'test';
    public $color = 'purple';
    public $notify = true;
    public $message_format = 'text';
    
    protected $_client;
    
    /**
     * Returns HTTP client
     * @return Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new Client;
        }
        return $this->_client;
    }
    
    /**
     * Prepares HipChat url.
     * @return string
     */
    public function getPreparedUrl()
    {
        return $this->url
            . $this->{$this->mode}['room']
            . '/notification?auth_token='
            . $this->{$this->mode}['token'];
    }
    
    /**
     * Sends message
     * @param string $msg
     * @param string $color
     * @param int $log
     * @return bool
     */
    public function send($msg, $color = null, $log = 0)
    {
        $response = $this->client->createRequest()
                    ->setFormat(Client::FORMAT_JSON)
                    ->setMethod('post')
                    ->setUrl($this->preparedUrl)
                    ->setData([
                        'color' => $color ?: $this->color, 
                        'message' => $msg,
                        'notify' => $this->notify,
                        'message_format' => $this->message_format,
                    ])
                    ->send();
        if ($response->isOk) {
            return true;
        }
        Yii::error(VarDumper::dumpAsString($response));
        if ($log) {
            var_dump($response);
        }
        return false;
    }
    
    /**
     * Returns random yay.
     * @return string
     */
    public static function randomYay()
    {
        $list = ['(allthethings)', '(awesome)', '(awthanks)', '(aww)', 
            '(awwyiss)', '(awyeah)', '(badass)', '(boom)', '(celeryman)', 
            '(content)', '(dobre)', '(drool)', '(feelsgoodman)', '(goodnews)', 
            '(megusta)', '(mindblown)', '(motherofgod)', '(nice)', '(notbad)', 
            '(ohmy)', '(success)', '(sweetjesus)', '(yey)', '(goodone)', 
            '(uuuuuuu)'];
        
        return $list[array_rand($list)];
    }
    
    /**
     * Returns random nope.
     * @return string
     */
    public static function randomNope()
    {
        $list = ['(areyoukiddingme)', '(badjokeeel)', '(badpokerface)', 
            '(badtime)', '(challengeaccepted)', '(dealwithit)', '(derp)', 
            '(disapproval)', '(doh)', '(donotwant)', '(drevil)', '(evilburns)', 
            '(facepalm)', '(fry)', '(grumpycat)', '(gtfo)', '(itsatrap)', 
            '(ohcrap)', '(ohgodwhy)', '(omg)', '(pokerface)', '(watchingyou)', 
            '(youdontsay)', '(yuno)', '(zmiana)'];
        
        return $list[array_rand($list)];
    }
}