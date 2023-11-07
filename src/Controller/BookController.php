<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\Type\BookType;
use App\Repository\BookRepository;
use App\Service\CoverUploader;
use App\Service\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер действий с книгами
 */
class BookController extends AbstractController
{
	/**
	 * @var BookRepository
	 */
	private BookRepository $repo;

	/**
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->repo = $entityManager->getRepository(Book::class);
	}

	#[Route('/book/list', name: 'books')]
	/**
	 * Страница со списком всех книг
	 * 
	 * @param Request $request
	 * @param Paginator $paginator
	 * 
	 * @return Response
	 */
	public function list(Request $request, Paginator $paginator): Response
	{
		$query = $this->repo->createQueryBuilder('b')->getQuery();
		$paginator->paginate($query, $request->query->getInt('page', 1));
		
		return $this->render(Book::class::LIST_TEMPLATE, [
			'paginator' => $paginator,
		]);
	}

	#[Route('/book', name: 'book_page')]
	/**
	 * Страница создания/редактирования книги
	 * 
	 * @param Request $request
	 * @param CoverUploader $uploader
	 * 
	 * @return Response
	 */
	public function page(Request $request, CoverUploader $uploader): Response
	{
		$bookId = $request->query->get('id');
		$Book = $bookId
			? $this->validateId($bookId)
			: new Book();

		$form = $this
			->createForm(BookType::class, $Book)
			->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$Book = $form->getData();
			if ($coverFile = $form->get('cover')->getData()) {
				$coverFileName = $uploader->upload($coverFile);
				$Book->setCover($coverFileName);
			}
			$this->repo->save($Book);

			return $this->redirectToRoute('books');
		}

		return $this->render(Book::class::PAGE_TEMPLATE, [
			'bookForm' => $form,
			'bookId' => $bookId,
		]);
	}

	#[Route('/book/delete/{id}', name: 'remove_book')]
	/**
	 * Функция удаления книги
	 * 
	 * @param int $id
	 * 
	 * @return RedirectResponse
	 */
	public function delete(int $id)
	{
		$this->repo->remove($this->validateId($id));

		return $this->redirectToRoute('books');
	}

	/**
	 * Проверяет id книги на существование
	 * 
	 * @param int $id
	 * 
	 * @return Book
	 */
	private function validateId(int $id): Book
	{
		$Book = $this->repo->findOneBy(['id' => $id]);

		if (!$Book) {
			throw $this->createNotFoundException('Книга не найдена');
		}

		return $Book;
	}
}
