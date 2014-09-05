<?php

class ArticlesRepository extends Repository {


    public function fetchAllFront()
    {
        return $this->connection->table('articles')
            ->where("hide", "0")
            ->order('created_at DESC');
    }

    public function fetchAll()
    {
        return iterator_to_array($this->connection->table('articles')
            ->order('created_at ASC'));
    }

    public function getById($id)
    {
        return $this->connection->table('articles')
            ->where("id", $id)
            ->fetch();
    }

    public  function  deleteById($id)
    {
        return $this->connection->table('articles')
            ->where('id', $id)
            ->delete();
    }

    public function insert($values){
        return $this->connection->table('articles')
            ->insert($values);
    }

    public function updateById($id, $values)
    {
        return $this->connection->table('articles')
            ->where("id", $id)
            ->Update($values);
    }


}