trait BuildTrait {
    public function __call($name, $arguments)
    {

        // getter
        if (strpos($name, 'get') === 0) {
            $field = lcfirst(substr($name, 3));
            if (0 !== count($arguments)) {
                throw new \Exception('getter操作没有参数');
            }

            if (!property_exists($this, $field)) {
                throw new \Exception('class 未定义此属性');
            }

            return $this->{$field};

        } else if(strpos($name, 'set') === 0){
            if (1 !== count($arguments)) {
                throw new \Exception('setter操作只有一个参数');
            }

            $field = lcfirst(substr($name, 3));

            if (!property_exists($this, $field)) {
                throw new \Exception('class 未定义此属性');
            }

            $this->{$field} = $arguments[0];

            return $this;
        } else {
            throw new \Exception('只支持getter与setter操作');
        }
    }
}
