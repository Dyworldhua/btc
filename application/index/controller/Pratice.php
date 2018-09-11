<?php
    namespace app\index\Controller;
    use app\index\model\Pratice;
    class Pratice extends Common{
        public function test(){
            $mill = new Partice();
            $where = [
                'id'=>1001
            ];
            $field = [
                'm.mill_name'
            ];
            $result = $mill->alias('m')
            ->where($where)
            ->field($field)
            ->find();
            var_dump($result);
        }
    }