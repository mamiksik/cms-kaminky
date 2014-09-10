<?php

class IpRepository extends Repository {


    public function count()
    {
        return $this->connection->table('ip_list')
            ->count("*");
    }

    public function fetchId()
    {
        return $this->connection->table('ip_list')
            ->select('id')
            ->limit(1)
            ->order('created_at ASC');
    }

    public  function  deleteById($id)
    {
        return $this->connection->table('ip_list')
            ->where('id', $id)
            ->delete();
    }

    public function insert($values){
        return $this->connection->table('ip_list')
            ->insert(array(
                'ip' => $values
            ));
    }


}