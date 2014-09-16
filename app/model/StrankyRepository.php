<?php

class StrankyRepository extends Repository {


    public function fetchAllFront($paginator)
    {
        return $this->connection->table('pages')
            ->where("hide", "0")
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->order('created_at DESC');
    }

    public function count()
    {
        return $this->connection->table('pages')
            ->count("*");
    }

    public function fetchAll()
    {
        return iterator_to_array($this->connection->table('pages')
            ->order('created_at ASC'));
    }

    public function getById($id)
    {
        return $this->connection->table('pages')
            ->where("id", $id)
            ->fetch();
    }

    //admin
    public  function  deleteById($id)
    {
        return $this->connection->table('pages')
            ->where('id', $id)
            ->delete();
    }

    public function insert($values){
        return $this->connection->table('pages')
            ->insert($values);
    }

    public function updateById($id, $values)
    {
        return $this->connection->table('pages')
            ->where("id", $id)
            ->Update($values);
    }
    //admin end

}