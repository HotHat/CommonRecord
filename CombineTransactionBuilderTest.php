<?php declare(strict_types=1);


namespace Tests\Unit;


use Tests\TestCase;
use App\Services\CombineTransactionBuilder as Builder;

class CombineTransactionBuilderTest extends TestCase
{

    public function testH5() {
        $builder = new Builder('wxd678efh567hg6787', '1900000109');

        $content = $builder
            ->addTradeNo('20150806125346')
            ->addH5BaseSceneInfo('14.23.150.211', '013467007045764')
            ->addH5SceneInfo('iOS', '王者荣耀', 'https://pay.qq.com', 'com.tencent.wzryiOS', 'com.tencent.tmgp.sgame')
            ->addSubOrder('1900000109', '20150806125346', 10, '腾讯充值中心-QQ会员充值', '深圳分店',true, 10)
            ->addTradeTime('2019-12-31T15:59:59+08:00', '2019-12-31T16:59:59+08:00')
            ->addNotifyUrl('https://yourapp.com/notify')
            ->build();

        dump($content);
    }

    public function testM() {
        $builder = new Builder('wxd678efh567hg6787', '1900000109');

        $content = $builder
            ->addTradeNo('20150806125346')
            ->addBaseSceneInfo('14.23.150.211', 'POS1:1')
            ->addSubOrder('1900000109', '20150806125346', 10, '腾讯充值中心-QQ会员充值', '深圳分店',false, 10)
            ->addSubOrder('1900000109', '20150806125346', 10, '腾讯充值中心-QQ会员充值', '深圳分店',false, 10)
            ->addTradeTime('2019-12-31T15:59:59+08:00', '2019-12-31T16:59:59+08:00')
            ->addNotifyUrl('https://yourapp.com/notify')
            ->build();

        dump($content);
    }

    public function testJsApi() {
        $builder = new Builder('wxd678efh567hg6787', '1900000109');

        $content = $builder
            ->addTradeNo('20150806125346')
            ->addBaseSceneInfo('14.23.150.211', 'POS1:1')
            ->addSubOrder('1900000109', '20150806125346', 10, '腾讯充值中心-QQ会员充值', '深圳分店')
            ->addPayerInfo('oUpF8uMuAJO_M2pxb1Q9zNjWeS6o')
            ->addTradeTime('2019-12-31T15:59:59+08:00', '2019-12-31T16:59:59+08:00')
            ->addNotifyUrl('https://yourapp.com/notify')
            ->build();

        dump($content);
    }




}
