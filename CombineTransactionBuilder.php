<?php declare(strict_types=1);


namespace App\Services;


class CombineTransactionBuilder
{
    private $appId;
    private $mchId;

    private $content;

    public function __construct($appId, $mchId)
    {
        $this->appId = $appId;
        $this->mchId = $mchId;

        $this->content = [
            'combine_appid' => $this->appId,
            'combien_mchid' => $this->mchId
        ];
    }

    public function addTradeNo($tradeNo) {
        $this->content['combine_out_trade_no'] = $tradeNo;
        return $this;
    }

    public function addBaseSceneInfo($ip, $deviceId = '') {
        $this->content['scene_info']['payer_client_ip'] = $ip;

        if (!empty($deviceId)) { $this->content['scene_info']['device_id'] = $deviceId ;}

        return $this;
    }

    public function addH5BaseSceneInfo($ip, $deviceId) {
        $this->content['scene_info']['payer_client_ip'] = $ip;
        // 终端设备号（门店号或收银设备ID）
        $this->content['scene_info']['device_id'] = $deviceId;
        return $this;
    }

    public function addH5SceneInfo($type, $appName = '', $appUrl = '', $bundleId = '', $packageName = '') {
        $this->content['scene_info']['h5_info']['type'] = $type;
        if (!empty($appName))    { $this->content['scene_info']['h5_info']['app_name']    = $appName;}
        if (!empty($appUrl))     { $this->content['scene_info']['h5_info']['app_url']     = $appUrl;}
        if (!empty($bundleId))   { $this->content['scene_info']['h5_info']['bundle_i']    = $bundleId;}
        if (!empty($packageName)){ $this->content['scene_info']['h5_info']['package_name']= $packageName;}
        return $this;
    }


    public function addSubOrder($subMchId, $tradeNo, $amount, $description,
                                $attach, $profitSharing = null, $subsidyAmount = 0, $currency = 'CNY') {
        $item = [
            'mchid' => $this->mchId,
            'attach' => $attach,
            'amount' => [
                'total_amount' => $amount,
                'currency' => $currency
            ],
            'out_trade_no' => $tradeNo,
            'sub_mchid' => $subMchId,
            'description' => $description,
        ];

        if ($profitSharing !== null) { $item['settle_info']['profit_sharing'] = $profitSharing; }
        if($subsidyAmount !== 0) { $item['settle_info']['subsidy_amount'] = $subsidyAmount; }

        $this->content['sub_orders'][] = $item;

        return $this;
    }


    public function addPayerInfo($openId) {
        $this->content['combine_payer_info']['openid'] = $openId;
        return $this;
    }

    public function addTradeTime($start, $expire) {
        $this->content['time_start'] = $start;
        $this->content['time_expire'] = $expire;
        return $this;
    }

    public function addNotifyUrl($url) {
        $this->content['notify_url'] = $url;
        return $this;
    }

    public function build() {
        return $this->content;
    }

}
