<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

/**
 * Заполняет бд тестовыми данными
 */
class AppFixtures extends Fixture
{
    /**
	 * Гененрирует и заносит данные (связи книга-автор не создаются)
	 * 
     * @param ObjectManager $manager
     * 
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
		$faker = FakerFactory::create();

		for ($i=0; $i < 20; $i++) {
			$author = new Author();
			$author->setName($faker->firstName);
			$author->setSurname($faker->lastName);
			$author->setPatronym($faker->firstName);
			$manager->persist($author);
		}

		for ($i=0; $i < 20; $i++) {
			$book = new Book();
			$book->setName($faker->city);
			$book->setYear(mt_rand(1900, 2010));
			$book->setISBN($faker->isbn13);
			$book->setpages(mt_rand(15, 900));
			$manager->persist($book);
		}

		$manager->flush();
    }
}
