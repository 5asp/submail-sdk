<?php

namespace Stefein;

class Submail
{
    public $config = array();

    protected $api_url = "https://api.mysubmail.com/";
    protected $timestamp = '';
    protected $http = null;
    protected $encrypt = array();
    protected $product = array();

    /**
     * Submail constructor.
     * @param null $config
     */
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        }
        $this->encrypt = array('normal', 'md5', 'sha1');
        $this->product = array('message' => 'sms', 'internationalsms' => 'internationalsms', 'voice' => 'voice', 'mms' => 'mms', 'mail' => 'mail');

        $status = $this->remoteStatus();
        if ($status['status'] !== 'runing') {
            $this->api_url = 'http' . trim($this->api_url, 'https');
        }
    }

    /**
     * @return mixed
     */
    protected function remoteTimestamp()
    {
        return $this->get($this->api_url . '/service/timestamp');
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
     * @return mixed
     */
    protected function remoteStatus()
    {
        return $this->get($this->api_url . '/service/status');
    }

    /**
     * @param $api
     * @param $data
     * @return mixed
     */
    protected function post($api, $data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded")
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        $output = trim($output, "\xEF\xBB\xBF");
        return json_decode($output, true);
    }

    /**
     * @param $api
     * @return mixed
     */
    protected function get($api)
    {
        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = trim($output, "\xEF\xBB\xBF");
        return json_decode($output, true);
    }

    /**
     * @param $type
     * @param array $config
     * @return mixed
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
        $res = $this->remoteTimestamp();
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = $this->config['sign_type'];
        $res['signature'] = $this->buildSignature($res);
        return $this->post($this->api_url . $url, $res);
    }

    /**
     * @param $type
     * @param array $config
     * @return mixed
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
        $res = $this->remoteTimestamp();
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = $this->config['sign_type'];
        $res['signature'] = $this->buildSignature($res);
        return $this->post($this->api_url . $url, $res);
    }

    /**
     * @param $request
     * @return mixed|string
     */
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

    /**
     * @param $to
     * @param string $content
     * @return mixed
     */
    private function send($to, $content = '')
    {
        $res = $this->remoteTimestamp();
        $res['appid'] = $this->config['appid'];
        $res['sign_type'] = trim($this->config['sign_type']);
        $res['content'] = $content;
        $res['to'] = $to;
        $res['signature'] = $this->buildSignature($res);
        return $res;
    }

    /**
     * @param $to
     * @param array $method
     * @return array|mixed
     */
    private function xsend($to, $method = array())
    {
        $res = $this->remoteTimestamp();
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

    /**
     * @param $params
     * @param string $method
     * @return mixed
     */
    private function multiSend($params, $method = '')
    {
        $res = $this->remoteTimestamp();
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

    /**
     * @param array $method
     * @return mixed
     */
    private function multixSend($method = array())
    {
        $res = $this->remoteTimestamp();
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

    /**
     * @param $to
     * @param $code
     * @param array $config
     * @return mixed
     */
    public function voiceVerify($to, $code, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->remoteTimestamp();
        $data['appid'] = $this->config['appid'];
        $data['sign_type'] = trim($this->config['sign_type']);
        $data['code'] = trim($code);
        $data['to'] = trim($to);
        $data['signature'] = $this->buildSignature($data);
        return $this->post($this->api_url . '/voice/verify', $data);
    }

    /**
     * @param $to
     * @param $content
     * @param array $config
     * @return mixed
     */
    public function messageSend($to, $content, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->send($to, $content);
        return $this->post($this->api_url . '/message/send', $data);
    }

    /**
     * @param $to
     * @param $params
     * @param array $config
     * @return mixed
     */
    public function messageXsend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->xsend($to, $params);
        return $this->post($this->api_url . '/message/xsend', $data);
    }

    /**
     * @param $to
     * @param $params
     * @param array $config
     * @return mixed
     */
    public function messageMultisend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multiSend($to, $params);
        return $this->post($this->api_url . '/message/multisend', $data);
    }

    /**
     * @param $params
     * @param array $config
     * @return mixed
     */
    public function messageMultixsend($params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multixSend($params);
        return $this->post($this->api_url . '/message/multixsend', $data);
    }

    public function verifyPhonenumber()
    {

    }

    /**
     * @param $to
     * @param $content
     * @param array $config
     * @return mixed
     */
    public function voiceSend($to, $content, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->send($to, $content);
        return $this->post($this->api_url . '/voice/send', $data);
    }

    /**
     * @param $to
     * @param $params
     * @param array $config
     * @return mixed
     */
    public function voiceXsend($to, $params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->xsend($to, $params);
        return $this->post($this->api_url . '/voice/xsend', $data);
    }

    /**
     * @param $params
     * @param array $config
     * @return mixed
     */
    public function voiceMultixsend($params, $config = array())
    {
        if ($config) {
            $this->config = $config;
        }
        $data = $this->multixSend($params);
        return $this->post($this->api_url . '/voice/multixsend', $data);
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

    public function mailSend()
    {

    }

    /**
     * @param $params
     * @param array $config
     * @return mixed
     */
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
        if (isset($params['links'])) {
            $params['links'] = json_encode($params['links']);
        }
        $to = $params['to'];
        unset($params['to']);
        $data = $this->xsend($to, $params);
        return $this->post($this->api_url . 'mail/xsend', $data);
    }
}