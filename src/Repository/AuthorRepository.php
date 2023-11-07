<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends BasicRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, $entityManager, Author::class);
    }
}
