<?php
/**
 * User: lyhux
 * Date: 2019/7/1
 * Time: 14:50
 *　　　　　　　　┏┓　　　┏┓+ +
 *　　　　　　　┏┛┻━━━┛┻┓ + +
 *　　　　　　　┃　　　　　　　┃ 　
 *　　　　　　　┃　　　━　　　┃ ++ + + +
 *　　　　　　 ████━████ ┃+
 *　　　　　　　┃　　　　　　　┃ +
 *　　　　　　　┃　　　┻　　　┃
 *　　　　　　　┃　　　　　　　┃ + +
 *　　　　　　　┗━┓　　　┏━┛
 *　　　　　　　　　┃　　　┃　　　　　　　　　　　
 *　　　　　　　　　┃　　　┃ + + + +
 *　　　　　　　　　┃　　　┃　　　　Code is far away from bug with the animal protecting　　　　　　　
 *　　　　　　　　　┃　　　┃ + 　　　　神兽保佑,代码无bug　　
 *　　　　　　　　　┃　　　┃
 *　　　　　　　　　┃　　　┃　　+　　　　　　　　　
 *　　　　　　　　　┃　 　　┗━━━┓ + +
 *　　　　　　　　　┃ 　　　　　　　┣┓
 *　　　　　　　　　┃ 　　　　　　　┏┛
 *　　　　　　　　　┗┓┓┏━┳┓┏┛ + + + +
 *　　　　　　　　　　┃┫┫　┃┫┫
 *　　　　　　　　　　┗┻┛　┗┻┛+ + + +
 */

class PermAtom {

    private $perm;

    private $parent_id = 0;
    private $subPerm;

    private $CI;

    public function __construct($perm, $sub)
    {
        $this->CI = &get_instance();
        $this->CI->load->model('perm_resource', 'perm');
        $this->perm = $perm;
        $this->subPerm = $sub;
    }


    public function setParent($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function save() {
        $this->perm['parent_id'] = $this->parent_id;

        $id = $this->CI->perm->addPerm($this->perm);

        if ($id === false) {
            var_dump($this->perm);
            dd($this->CI->perm->getError());
        }

        foreach ($this->subPerm as $perm) {
            $perm->setParent($id);
            $perm->save();
        }
    }
}


class PermHelper
{

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function savePerm() {

         $perms = [
             // 售票
             new PermAtom(['title' => '售票', 'url' => '',
                 'icon' => 'icon-nav-a0', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], [

                 // 散客售票
                 new PermAtom(['title' => '散客售票', 'url' => '/window/index',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [
                     new PermAtom(['title' => '售票订单扫描', 'url' => '/window/index/order_save',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '售票支付订单', 'url' => '/window/index/payment',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),

                // 团队售票
                 new PermAtom(['title' => '团队售票', 'url' => '/window/index/team_index',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [
                      new PermAtom(['title' => '售票订单扫描2', 'url' => '/window/index/order_save',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '售票支付订单2', 'url' => '/window/index/payment',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),

                 // 售票记录
                 new PermAtom(['title' => '售票记录', 'url' => '/window/order',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], []),
             ]),

             // 手环
             new PermAtom(['title' => '手环', 'url' => '',
                 'icon' => 'icon-nav-a1', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [

                 // 手环发放
                 new PermAtom(['title' => '手环发放', 'url' => '/window/exchange',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 0, 'is_link' => 2], [
                     new PermAtom(['title' => '发放检查手环', 'url' => '/window/exchange/check_ban',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 0, 'is_link' => 2], []),

                     new PermAtom(['title' => '发放办理手环绑定', 'url' => '/window/exchange/handle',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 0, 'is_link' => 2], []),

                     new PermAtom(['title' => '发放生成押金', 'url' => '/window/exchange/handle_deposit',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 0, 'is_link' => 2], []),

                     new PermAtom(['title' => '发放支付押金', 'url' => '/window/exchange/handle_deposit',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 0, 'is_link' => 2], []),
                 ]),

                 //
                 new PermAtom(['title' => '手环回收', 'url' => '/window/bands/back',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [
                     new PermAtom(['title' => '回收手环信息', 'url' => '/window/bands/ajax_back_band_info',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], []),
                     new PermAtom(['title' => '回收退余额', 'url' => '/window/bands/ajax_return_amount',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], []),
                 ]),

                 new PermAtom(['title' => '押金退款', 'url' => '/window/exchange/refund',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 3, 'is_link' => 1], [
                     new PermAtom(['title' => '押金退款退回', 'url' => '/window/exchange/ajax_back_deposit',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], []),
                 ]),
             ]),

             // 退票
             new PermAtom(['title' => '退票', 'url' => '',
                 'icon' => 'icon-nav-a0', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [

                 // 订单退票
                 new PermAtom(['title' => '订单退票', 'url' => '/window/refund/untravel',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], [
                     new PermAtom(['title' => '退票获取手环信息', 'url' => '/window/refund/get_band_info',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '退票搜索订单', 'url' => '/window/refund/search_order',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),



                 // 手环退票
                 new PermAtom(['title' => '退票手环退票', 'url' => '/window/refund/index',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [

                 ]),
             ]),

             // 装备
             new PermAtom(['title' => '装备', 'url' => '',
                 'icon' => 'icon-nav-a0', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [

                 // 装备发放
                 new PermAtom(['title' => '装备发放', 'url' => '/window/equipments/get',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], [

                     new PermAtom(['title' => '装备领取装备信息', 'url' => '/window/equipments/ajax_get_band_info',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '装备领取保存', 'url' => '/window/equipments/get_save',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),


                 // 装备回收
                 new PermAtom(['title' => '装备回收', 'url' => '/window/equipments/back',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [

                     new PermAtom(['title' => '装备回收装备信息', 'url' => '/window/equipments/ajax_back_band_info',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '装备回收保存', 'url' => '/window/equipments/back_save',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                 ]),
             ]),

             // 充值
             new PermAtom(['title' => '充值', 'url' => '',
                 'icon' => 'icon-nav-a0', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [

                 // 手环充值
                 new PermAtom(['title' => '手环充值', 'url' => '/window/recharge/index',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], [
                     new PermAtom(['title' => '充值操作页面', 'url' => '/window/recharge/detail',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '充值手环检查', 'url' => '/window/recharge/checkSn',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '充值生成支付订单', 'url' => '/window/recharge/getPaySn',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '充值支付', 'url' => '/window/recharge/pay',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),


                 // 充值记录
                 new PermAtom(['title' => '充值记录', 'url' => '/window/recharge/record',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [
                     new PermAtom(['title' => '充值记录发送短信', 'url' => '/window/recharge/sms',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),
             ]),

             // 异常
             new PermAtom(['title' => '异常', 'url' => '',
                 'icon' => 'icon-nav-a0', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 1], [

                 // 手环补办
                 new PermAtom(['title' => '手环补办', 'url' => '/window/anomaly/supplement',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], [
                     new PermAtom(['title' => '补办扫手环', 'url' => '/window/anomaly/scanBandForSupplement',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '补办生成订单', 'url' => '/window/anomaly/getSupplementTradeSn',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '补办支付', 'url' => '/window/anomaly/paySupplement',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),
                 ]),

                 // 手环更换
                 new PermAtom(['title' => '异常更换', 'url' => '/window/anomaly/replace',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [

                     new PermAtom(['title' => '更换扫手环', 'url' => '/window/anomaly/scanBandForReplace',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '更换检查', 'url' => '/window/anomaly/replaceCheck',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '更换保存', 'url' => '/window/anomaly/replaceSave',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                 ]),

                 // 装备补领
                 new PermAtom(['title' => '装备补领', 'url' => '/window/anomaly/equipment',
                     'icon' => '', 'parent_id' => 0, 'sort_num' => 2, 'is_link' => 1], [

                     new PermAtom(['title' => '装备补领扫手环', 'url' => '/window/anomaly/scanBandForEquipment',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '装备补领检查', 'url' => '/window/anomaly/equipmentCheck',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '装备补领生成订单', 'url' => '/window/anomaly/getEquipmentTradeSn',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                     new PermAtom(['title' => '装备补领支付', 'url' => '/window/anomaly/payEquipment',
                         'icon' => '', 'parent_id' => 0, 'sort_num' => 1, 'is_link' => 2], []),

                 ]),
             ]),




         ];

         foreach ($perms as $perm) {
             $this->CI->db->trans_begin();

             $perm->save();

             if ($this->CI->db->trans_status() === FALSE){
                 $this->CI->db->trans_rollback();
                 dd('Error happen');
             }
             $this->CI->db->trans_commit();
         }

    }


}