<?php

namespace Stefein;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Submail
{
    public $config = array();

    const SUBMAIL_API_URL = "https://api.mysubmail.com/";

    protected $timestamp = '';
    protected $http = null;
    protected $encrypt = array();
    protected $product = array();

    /**
     * Submail constructor.
     * @param null $config
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        }
        $this->encrypt = array('normal', 'md5', 'sha1');
        $this->product = array('message' => 'sms', 'internationalsms' => 'internationalsms', 'voice' => 'voice', 'mms' => 'mms', 'mail' => 'mail');
        $this->http = new Client([
            'base_uri' => self::SUBMAIL_API_URL,
            'timeout' => 2.0
        ]);
        $status = $this->remoteStatus();
        if ($status['code'] !== 200 || $status['data']['status'] !== 'runing') {
            $this->http = new Client([
                'base_uri' => 'http' . trim(self::SUBMAIL_API_URL, 'https'),
                'timeout' => 2.0
            ]);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function processResponse(ResponseInterface $response)
    {
        $body = $response->getBody();
        $contents = $body->getContents();
        $contents = json_decode($contents, true);
        return ['code' => $response->getStatusCode(), 'status' => $response->getReasonPhrase(), 'data' => $contents];
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function remoteTimestamp()
    {
        $res = $this->http->request('GET', '/service/timestamp');
        return $this->processResponse($res);
    }

    /**
     * @param $msg
     * @return array
     */
    protected function exitCode($msg)
    {
        return array('code' => 404, 'msg' => 'Error', 'data' => $msg);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function remoteStatus()
    {
        $res = $this->http->request('GET', '/service/status');
        return $this->processResponse($res);
    }

    /**
     * @param $type
     * @param array $config
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCredits($type, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $type = trim($type);
        if (isset($this->product[$type])) {
            $url = 'balance/' . $this->product[$type];
        } else {
            $this->exitCode('未正确配置业务名称');
        }
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = $this->config['sign_type'];
        $res['signature'] = $this->buildSignature($res);
        $response = $this->http->post($url, [
            'form_params' => $res
        ]);
        return $this->processResponse($response);
    }

    /**
     * @param $type
     * @param array $config
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLog($type, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        if (isset($type)) {
            $type = trim($type);
            $url = 'log/' . $type;
        } else {
            $this->exitCode('未正确配置业务名称');
        }
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = $this->config['sign_type'];
        $res['signature'] = $this->buildSignature($res);
        $response = $this->http->post($url, [
            'form_params' => $res
        ]);
        return $this->processResponse($response);
    }

    protected function buildSignature($request)
    {
        ksort($request);
        reset($request);
        $str = '';
        foreach ($request as $key => $value) {
            if (strpos($key, "attachments") === false) {
                $str .= $key . "=" . $value . "&";
            }
        }
        $str = substr($str, 0, -1);
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        switch ($request['sign_type']) {
            case 'md5':
                $r = md5($this->config['appid'] . $this->config['appkey'] . $str . $this->config['appid'] . $this->config['appkey']);
                break;
            case 'sha1':
                $r = sha1($this->config['appid'] . $this->config['appkey'] . $str . $this->config['appid'] . $this->config['appkey']);
                break;
            default:
                $r = $this->config['appkey'];
                break;
        }
        return $r;
    }

    private function send($to, $content = '')
    {
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['content'] = $content;
        $res['to'] = $to;
        $res['signature'] = $this->buildSignature($res);
        return $res;
    }

    private function xsend($to, $method = array())
    {
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['project'] = $this->config['project'];
        $res['to'] = $to;
        if (isset($method['vars']) || isset($method['links'])) {
            $res = array_merge($method, $res);
        } else {
            $res['vars'] = json_encode($method);
        }
        $res['signature'] = $this->buildSignature($res);
        return $res;
    }

    private function multiSend($params, $method = '')
    {
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['content'] = $params;
        $res['multi'] = $method;
        foreach ($method as $k => $v) {
            $res['multi'][$k][$v['to']] = $v['to'];
            unset($v['to']);
            $res['multi'][$k]['vars'] = $v;
        }
        $res['multi'] = json_encode($res['multi']);
        $res['signature'] = $this->buildSignature($res);
        return $res;
    }

    private function multixSend($method = array())
    {
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['project'] = $this->config['project'];
        $res['multi'] = $method;
        foreach ($method as $k => $v) {
            $res['multi'][$k][$v['to']] = $v['to'];
            unset($v['to']);
            $res['multi'][$k]['vars'] = $v;
        }
        $res['multi'] = json_encode($res['multi']);
        $res['project'] = $this->config['project'];
        $res['signature'] = $this->buildSignature($res);
        return $res;
    }

    public function messageSend($to, $content, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->send($to, $content);
        $response = $this->http->post('message/send', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function messageXsend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->xsend($to, $params);
        $response = $this->http->post('message/xsend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function messageMultisend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multiSend($to, $params);
        $response = $this->http->post('message/multisend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function messageMultixsend($params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multixSend($params);
        $response = $this->http->post('message/multixsend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function voiceSend($to, $content, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->send($to, $content);
        $response = $this->http->post('voice/send', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function voiceXsend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->xsend($to, $params);
        $response = $this->http->post('voice/xsend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function voiceMultixsend($params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multixSend($params);
        $response = $this->http->post('voice/multixsend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }

    public function voiceVerify($to, $code, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $res['timestamp'] = $this->remoteTimestamp();
        $res['timestamp'] = $res['timestamp']['data']['timestamp'];
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['code'] = trim($code);
        $res['to'] = trim($to);
        $res['signature'] = $this->buildSignature($res);
        $response = $this->http->post('voice/verify', [
            'form_params' => $res
        ]);
        return $this->processResponse($response);
    }

    public function internationalSmsSend()
    {

    }

    public function internationalSmsXsend()
    {

    }

    public function internationalSmsMultixsend()
    {

    }

    public function verifyPhonenumber()
    {

    }

    public function mailSend()
    {

    }

    public function mailXsend($params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        if (!isset($params['to']) || empty($params['to'])) {
            $this->exitCode('收件人格式不正确');
        }
        if (isset($params['vars'])) {
            $params['vars'] = json_encode($params['vars']);
        }
        if (isset($params['link'])) {
            $params['link'] = json_encode($params['link']);
        }
        $to = $params['to'];
        unset($params['to']);
        $data = $this->xsend($to, $params);
        $response = $this->http->post('mail/xsend', [
            'form_params' => $data
        ]);
        return $this->processResponse($response);
    }
}