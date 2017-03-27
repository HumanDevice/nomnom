<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * GitLab
 *
 * @property Client $client
 */
class GitLab extends Component
{
    /**
     * @var string GitLab repo URL
     */
    public $url = 'https://gitlab.human-device.com/';

    /**
     * @var string GitLab access token
     */
    public $token;

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
     * Returns prepared URL.
     * @param int $project
     * @param int $issue
     * @return string
     */
    public function prepareUrl($project, $issue)
    {
        return $this->url . '/api/v3/projects/' . $project . '/issues/' . $issue . '/time_stats';
    }

    /**
     * Checks time stats for issue.
     * @param int $project
     * @param int $issue
     * @return array|bool
     */
    public function checkTime($project, $issue)
    {
        $response = $this->client->createRequest()
                    ->setMethod('get')
                    ->setUrl($this->prepareUrl($project, $issue))
                    ->addHeaders(['PRIVATE-TOKEN' => $this->token])
                    ->send();
        if ($response->isOk) {
            return $response->getData();
        }
        Yii::error(VarDumper::dumpAsString($response));
        return false;
    }
}