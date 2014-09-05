<?php
abstract class Repository extends Nette\Object {

/** @var Nette\Database\Context */
protected $connection;

public function __construct(Nette\Database\Context $db) {
$this->connection = $db;
}
}