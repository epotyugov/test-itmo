<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends BasicRepository
{
	public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
	{
		parent::__construct($registry, $entityManager, Book::class);
	}
}
