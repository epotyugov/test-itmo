<?php

namespace App\Entity;

use App\Entity\ModelInterface;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\UniqueConstraint(
	name: 'unique_full_name',
	columns: ['surname', 'name', 'patronym']
)]
#[UniqueEntity(
    fields: ['surname', 'name', 'patronym'],
	errorPath: 'name',
    message: 'Автор с таким ФИО уже существует',
)]
/**
 * Модель автора
 */
class Author implements ModelInterface
{
	/** @var string Путь к шаблону страницы со списком авторов */
	public const LIST_TEMPLATE = 'author/list.html.twig';
	/** @var string Путь к шаблону страницы отдельного автора */
	public const PAGE_TEMPLATE = 'author/page.html.twig';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $patronym = null;

	#[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors', cascade: ['remove'])]
	private ?Collection $books;

	public function __construct()
	{
		$this->books = new ArrayCollection();
	}

	public function getBooks(): Collection
	{
		return $this->books;
	}

	public function addBook(Book $book): void
	{
		if (!$this->books->contains($book)) {
			$this->books[] = $book;
			$book->addAuthor($this);
		}
	}

	public function removeBook(Book $book)
	{
		if ($this->books->contains($book)) {
			$this->books->removeElement($book);
			$book->removeAuthor($this);
		}
	}

	/**
	 * Собирает полное ФИО
	 * 
	 * @return string|null
	 */
	public function getFullName(): ?string
	{
		return $this->name . ' ' . $this->patronym . ' ' . $this->surname;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
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

    public function getPatronym(): ?string
    {
        return $this->patronym;
    }

    public function setPatronym(string $patronym): static
    {
        $this->patronym = $patronym;

        return $this;
    }
}
