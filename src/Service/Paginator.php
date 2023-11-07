<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;

/**
 * Сервис-пагинатор для страниц со списками записей
 */
class Paginator
{
	private int $total;
	private int $lastPage;
	private $items;

	public function paginate($query, int $page = 1, int $limit = 10): Paginator
	{
		$paginator = new OrmPaginator($query);

		$paginator
			->getQuery()
			->setFirstResult($limit * ($page - 1))
			->setMaxResults($limit);

		$this->total = $paginator->count();
		$this->lastPage = (int) ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
		$this->items = $paginator;

		return $this;
	}

	public function getTotal(): int
	{
		return $this->total;
	}

	public function getLastPage(): int
	{
		return $this->lastPage;
	}

	public function getItems()
	{
		return $this->items;
	}
}