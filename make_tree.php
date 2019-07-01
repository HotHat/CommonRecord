<?php
/**
 * Created by PhpStorm.
 * User: Glyhux
 * Date: 2019/6/25
 * Time: 23:17
 */

//$key = 'nO98nB9882eS82eS';
//$length = mb_strlen($key, '8bit');

//print($length);


$perms = [
    ['id' => 1, 'parent_id' => 0, 'value' => 1],
    ['id' => 2, 'parent_id' => 0, 'value' => 2],
    ['id' => 3, 'parent_id' => 1, 'value' => 3],
    ['id' => 4, 'parent_id' => 3, 'value' => 4],
    ['id' => 5, 'parent_id' => 2, 'value' => 5],
    ['id' => 6, 'parent_id' => 4, 'value' => 6],
];


class A {
    public $id;
    public $items;
//    public $child;

    public function __construct($id)
    {
        $this->id = $id;
        $this->items = [];
//        $this->child = null;
    }
}

$parent = [];

foreach ($perms as $item) {
    $pid = $item['parent_id'];
    $cid = $item['id'];

    if (!isset($parent[$pid])) {
        $parent[$pid] = new A($pid);
        $parent[$cid] = new A($cid);
        $parent[$pid]->items[$cid] = &$parent[$cid];
//        $parent[$pid]->child = &$parent[$cid];

    } else {

        if (!isset($parent[$cid])) {
            $parent[$cid] = new A($cid);
        }

        $parent[$pid]->items[$cid] = &$parent[$cid];
//        $parent[$pid]->child = &$parent[$cid];

    }
}

print_r($parent);