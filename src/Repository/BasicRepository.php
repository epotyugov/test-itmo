<?php

namespace App\Repository;

use App\Entity\ModelInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class BasicRepository extends ServiceEntityRepository
{
	protected EntityManagerInterface $entityManager;
	private string $className;

	public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, string $className)
    {
		$this->className = $className;
        parent::__construct($registry, $this->className);
		$this->entityManager = $entityManager;
    }

	public function remove(ModelInterface $entity)
	{
		$this->entityManager->remove($entity);
		$this->entityManager->flush();
	}

	public function save(ModelInterface $entity)
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
	}
}