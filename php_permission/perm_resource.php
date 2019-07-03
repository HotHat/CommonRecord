<?php
/**
 * User: lyhux
 * Date: 2019/7/1
 * Time: 14:08
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

class PermNode {
    public $id;
    public $item;
    public $child;

    public function __construct($id, $item = [])
    {
        $this->id   = $id;
        $this->item = $item;
        $this->child = [];
    }
}

class Perm_resource extends Basemodel
{

    protected static $table = 'perm_resource';


    public function save($data) {
        $rules = [
            ['id',  'id',  'integer'],
            ['title',  'title',  'required|max_length[30]'],
            ['url',  'url',  'max_length[512]'],
            ['icon',  'icon',  'max_length[20]'],
            ['parent_id',  'parent_id',  'required|integer'],
            ['sort_num',  'sort_num',  'required|integer'],
            ['is_link',  'is_link',  'required|in_list[1,2]'],
            // ['uid',  'uid',  'required|integer'],
            // ['org',  'org',  'required|integer'],
        ];

        $tmp = $this->verify($data, $rules);
        if ($tmp === false) {
            return false;
        }

        // 保存
        $id = $data['id'];
        unset($data['id']);

        $data['url'] = $data['url'] ?: '';
        $data['icon'] = $data['icon'] ?: '';

        if (empty($id)) {
            $ext = $this->db
                ->where('title', $tmp['title'])
                ->where('url', $tmp['url'])
                ->from(self::$table)->get()->row_array();

            if ($ext) {
                return true;
            }

            $tmp['created_at'] = NOW_TIME;
            $tmp['updated_at'] = NOW_TIME;
            $this->db->insert(self::$table, $tmp);
            $id = $this->db->insert_id();
        } else {
            $tmp['updated_at'] = NOW_TIME;
            $this->db->where('id', $id)->update(self::$table, $tmp);
        }

        return $id;
    }

    private function treeHelper($parent_id, $all) {

        $curr = [];

        foreach ($all as $it) {
            if ($it['parent_id'] == $parent_id) {
                $curr['lst'][] = $it;
                $tmp = $this->treeHelper($it['id'], $all);
                $curr['sub'] = $tmp;
            }
        }

        return $curr;
    }

    public function getPermTree($perms) {

        $parent = [];
        foreach ($perms as $item) {
            $pid = $item['parent_id'];
            $cid = $item['id'];

            if (!isset($parent[$pid])) {
                $parent[$pid] = new PermNode($pid);
            }

            if (!isset($parent[$cid])) {
                $parent[$cid] = new PermNode($cid);
            }

            $parent[$cid]->item = $item;

            $parent[$pid]->child[$cid] = &$parent[$cid];

        }

        return $parent[0];
    }


    public function addPerm($data) {
       return $this->save($data);
    }

    public function updatePerm($id, $data) {
        $data['id'] = $id;

        return $this->save($data);
    }


    public function isPermitted($url, $perms) {
        return true;
    }

}