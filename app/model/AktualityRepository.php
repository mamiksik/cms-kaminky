<?php

class AktualityRepository extends Repository {


    public function fetchAllFront($paginator)
    {
        return $this->connection->table('articles')
            ->where("hide", "0")
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->order('created_at DESC');
    }

    public function count()
    {
        return $this->connection->table('articles')
            ->count("*");
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

    //
    //ADMIN
    //

    public function fetchAllAdmin($paginator)
    {
        return $this->connection->table('articles')
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->order('created_at DESC');
    }

    public function countMy($id)
    {
        return $this->connection->table('articles')
        ->where('author_id', $id)
        ->count("*");
    }


    public function fetchAllMyAdmin($paginator, $author_id)
    {
        return $this->connection->table('articles')
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->where('author_id', $author_id)
            ->order('created_at DESC');
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

    public function controlByName($name)
    {
        return $this->connection->table('articles')
            ->where("name", $name);
    }

    public function getByKey($key, $val)
    {
        return $this->connection->table('articles')
            ->where($key, $val)
            ->fetch();
    }

    //admin end

}