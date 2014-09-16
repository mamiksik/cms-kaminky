<?php

class UzivateleRepository extends Repository {


    public function fetchAllAdmin($paginator)
    {
        return $this->connection->table('users')
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->order('username DESC');
    }

    public function fetchAllFront()
    {
        return $this->connection->table('users')
            ->order('id DESC');
    }

    public function count()
    {
        return $this->connection->table('users')
            ->count("*");
    }

    public function countByKey($key, $value)
    {
        return $this->connection->table('users')
            ->where($key, $value)
            ->count("*");
    }


    public function getById($id)
    {
        return $this->connection->table('users')
            ->where("id", $id)
            ->fetch();
    }

    public  function  deleteById($id)
    {
        return $this->connection->table('users')
            ->where('id', $id)
            ->delete();
    }

    public function insert($values){
        return $this->connection->table('users')
            ->insert($values);
    }

    public function updateById($id, $values)
    {
        return $this->connection->table('users')
            ->where("id", $id)
            ->Update($values);
    }


}