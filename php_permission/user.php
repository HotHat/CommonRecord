<?php


class User extends CI_Model 
{
	
    public function getRole($user) {

        $data = $this->db
            ->where('org_id', $user['organization_id'])
            ->where('user_id', $user['id'])
            ->from('perm_role_user')->get()->row_array();

        return $data;
    }

    public function getRoleResource($role) {
        $data = $this->db
            ->select('r.*')
            ->where('p.org_id', $role['org_id'])
            ->where('p.role_id', $role['id'])
            ->join('perm_resource r', 'r.id=p.resource_id')
            ->from('perm_role_resource p')->get()->result_array();

        return $data;
    }

    public function getUserResource($user) {
        $data = $this->db
            ->select('r.*')
            ->where('p.org_id', $user['organization_id'])
            ->where('p.user_id', $user['id'])
            ->join('perm_resource r', 'r.id=p.resource_id')
            ->from('perm_user_resource p')->get()->result_array();

        return $data;
    }

    public function getWhileList() {
        return [
            'window/common/*',
            'index'
        ];
    }

    public function isPerm($uri, $user) {
        $this->load->model('perm_resource', 'perm');
        // $role = $this->getRole($user);

        // 白名单
        $whileList = $this->getWhileList();
        foreach ($whileList as $while) {
            $t = str_replace('/', '\/', $while);
            $m = '/^'.$t.'/';
            if (preg_match($m, $uri)) {
                return true;
            }
        }

        // 权限设置
        $resource = $this->getUserResource($user);

        foreach ($resource as $item) {
            $url = ltrim($item['url'], '/');

            if ($url == $uri) {
                return true;
            }

            $spl = explode('/', $url);
            if (count($spl) <= 2) {
                $url .= '/index';
                if ($url == $uri) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getUserPermTree($user) {
        $this->load->model('perm_resource', 'perm');
        // $role = $this->getRole($user);

        // $resource = $this->getRoleResource($role);
        $resource = $this->getUserResource($user);

        $data = $this->perm->getPermTree($resource);
        $result = $data->child ?? [];

        foreach ($result as &$item) {
            if (!empty($item->child)) {
                $curr = current($item->child);
                $item->first = $curr->item;

                $subPerm = [];
                foreach ($item->child as $it) {
                    $url = $it->item['url'];

                    $url = ltrim($url, '/');
                    $subPerm[] = $url;

                    $spl = explode('/', $url);
                    if (count($spl) <= 2) {
                        $url .= '/index';
                        $subPerm[] = $url;
                    }

                }

                $item->subPerm = $subPerm;
            }

        }

        usort($result, function($a, $b) {
            return $a->item['sort_num']  > $b->item['sort_num'];
        });

        return $result;
    }
    
}
