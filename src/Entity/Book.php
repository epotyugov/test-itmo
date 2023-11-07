<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\UniqueConstraint(
  name: 'unique_name_isbn',
  columns: ['name', 'ISBN']
)]
#[ORM\UniqueConstraint(
  name: 'unique_name_year',
  columns: ['name', 'year']
)]
#[UniqueEntity(
    fields: ['name', 'ISBN'],
	errorPath: 'ISBN',
    message: 'Книга с такой комбинацией названия и ISBN уже существует',
)]
#[UniqueEntity(
    fields: ['name', 'year'],
	errorPath: 'year',
    message: 'Книга с такой комбинацией названия и года выпуска уже существует',
)]
/**
 * Модель книги
 */
class Book implements ModelInterface
{
	/** @var string Путь к шаблону страницы со списком книг */
	public const LIST_TEMPLATE = 'book/list.html.twig';
	/** @var string Путь к шаблону страницы отдельной книги */
	public const PAGE_TEMPLATE = 'book/page.html.twig';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $ISBN = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $pages = null;

    #[ORM\Column(length: 255, nullable: true)]
	#[Assert\Type(type: 'file', message: 'Пожалуйста, загрузите корректное изображение')]
    private ?string $cover = null;

	#[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'authors_books')]
	private ?Collection $authors;

	public function getAuthors(): Collection
	{
		return $this->authors;
	}

	public function addAuthor(Author $author): void
	{
		if (!$this->authors->contains($author)) {
			$this->authors[] = $author;
			$author->addBook($this);
		}
	}

	public function removeAuthor(Author $author)
	{
		if ($this->authors->contains($author)) {
			$this->authors->removeElement($author);
			$author->removeBook($this);
		}
	}

	public function __construct()
	{
		$this->authors = new ArrayCollection();
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(int $pages): static
    {
        $this->pages = $pages;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }
}
