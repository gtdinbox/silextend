<?php

namespace Silextend\Repository;

use Doctrine\DBAL\Connection;

abstract class Repository {
	
	public static $dbName = 'default';
	
	public static $tableName;

	public $db;

	public function __construct(Connection $db)
	{	
		$this->db = $db;
	}

	public function insert(array $data)
	{
		return $this->db->insert(static::$tableName, $data);
	}

	public function update(array $data, array $identifier)
	{
		return $this->db->update(static::$tableName, $data, $identifier);
	}

	public function delete(array $identifier)
	{
		return $this->db->delete(static::$tableName, $identifier);
	}

	public function find($id)
	{
		return $this->db->fetchAssoc(sprintf('SELECT * FROM %s WHERE id = ? LIMIT 1', static::$tableName), array((int) $id));
	}

	public function findAll()
	{
		return $this->db->fetchAll(sprintf('SELECT * FROM %s', static::$tableName));
	}
	
	public function queryBuilder()
	{
		return $this->db->createQueryBuilder();
	}
	
}
