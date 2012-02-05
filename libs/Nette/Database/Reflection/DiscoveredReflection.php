<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Database\Reflection
 */



/**
 * Reflection metadata class with discovery for a database.
 *
 * @author     Jakuv Vrana
 * @property-write NConnection $connection
 * @package Nette\Database\Reflection
 */
class NDiscoveredReflection extends NObject implements IReflection
{
	/** @var NCache */
	protected $cache;

	/** @var ICacheStorage */
	protected $cacheStorage;

	/** @var NConnection */
	protected $connection;

	/** @var array */
	protected $structure = array(
		'primary' => array(),
		'hasMany' => array(),
		'belongsTo' => array(),
	);



	/**
	 * Create autodiscovery structure.
	 * @param  ICacheStorage
	 */
	public function __construct(ICacheStorage $storage = NULL)
	{
		$this->cacheStorage = $storage;
	}



	public function setConnection(NConnection $connection)
	{
		$this->connection = $connection;
		if ($this->cacheStorage) {
			$this->cache = new NCache($this->cacheStorage, 'Nette.Database.' . md5($connection->getDsn()));
			$this->structure = ($tmp=$this->cache->load('structure')) ? $tmp : $this->structure;
		}
	}



	public function __destruct()
	{
		if ($this->cache) {
			$this->cache->save('structure', $this->structure);
		}
	}



	public function getPrimary($table)
	{
		$primary = & $this->structure['primary'][$table];
		if (isset($primary)) {
			return $primary;
		}

		$columns = $this->connection->getSupplementalDriver()->getColumns($table);
		$primaryCount = 0;
		foreach ($columns as $column) {
			if ($column['primary']) {
				$primary = $column['name'];
				$primaryCount++;
			}
		}

		if ($primaryCount !== 1)
			return NULL;

		return $primary;
	}



	public function getHasManyReference($table, $key, $refresh = TRUE)
	{
		$reference = $this->structure['hasMany'];
		if (!empty($reference[$table])) {
			foreach ($reference[$table] as $targetTable => $targetColumn) {
				if (strpos($targetTable, strtolower($key)) !== FALSE) {
					return array(
						$targetTable,
						$targetColumn,
					);
				}
			}
		}

		if (!$refresh) {
			throw new PDOException("No reference found for \${$table}->related({$key}).");
		}

		$this->reloadAllForeignKeys();
		return $this->getHasManyReference($table, $key, FALSE);
	}



	public function getBelongsToReference($table, $key, $refresh = TRUE)
	{
		$reference = $this->structure['belongsTo'];
		if (!empty($reference[$table])) {
			foreach ($reference[$table] as $column => $targetTable) {
				if (strpos($column, strtolower($key)) !== FALSE) {
					return array(
						$targetTable,
						$column,
					);
				}
			}
		}

		if (!$refresh) {
			throw new PDOException("No reference found for \${$table}->{$key}.");
		}

		$this->reloadForeignKeys($table);
		return $this->getBelongsToReference($table, $key, FALSE);
	}



	protected function reloadAllForeignKeys()
	{
		foreach ($this->connection->getSupplementalDriver()->getTables() as $table) {
			if ($table['view'] == FALSE) {
				$this->reloadForeignKeys($table['name']);
			}
		}

		foreach (array_keys($this->structure['hasMany']) as $table) {
			uksort($this->structure['hasMany'][$table], create_function('$a, $b', '
				return strlen($a) - strlen($b);
			'));
		}
	}



	protected function reloadForeignKeys($table)
	{
		static $reloaded = array();
		if (isset($reloaded[$table]))
			return;

		foreach ($this->connection->getSupplementalDriver()->getForeignKeys($table) as $row) {
			$this->structure['belongsTo'][$table][strtolower($row['local'])] = $row['table'];
			$this->structure['hasMany'][strtolower($row['table'])][$table] = $row['local'];
		}

		if (isset($this->structure['belongsTo'][$table])) {
			uksort($this->structure['belongsTo'][$table], create_function('$a, $b', '
				return strlen($a) - strlen($b);
			'));
		}

		$reloaded[$table] = TRUE;
	}

}
