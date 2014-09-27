<?php

class ImagesRepository extends Repository {

    /*public  function deleteByIdHome($id_clanku)
    {
        return $this->connection->table('images')
            ->where('id_bytu', $id_clanku)
            ->delete();
    }*/

    public  function insertImage($values)
    {
        return $this->connection->table('images')
            ->insert($values);
    }

    public function fetchById($id_clanku)
    {
        return $this->connection->table('images')
            ->where('id_record', $id_clanku)
            ->order('id ASC');
    }

    public function getByKey($key, $val)
    {
        return iterator_to_array($this->connection->table('images')
            ->where($key, $val)
            ->order('id ASC'));
    }

    public  function deleteByKey($key, $id)
    {
        return $this->connection->table('images')
            ->where($key, $id)
            ->delete();
    }
}