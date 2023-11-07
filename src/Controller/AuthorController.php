<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\Type\AuthorType;
use App\Repository\AuthorRepository;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер действий с авторами
 */
class AuthorController extends AbstractController
{
	/**
	 * @var AuthorRepository
	 */
	private AuthorRepository $repo;

	/**
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->repo = $entityManager->getRepository(Author::class);
	}

	#[Route('/author/list', name: 'authors')]
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
		$query = $this->repo->createQueryBuilder('a')->getQuery();
		$paginator->paginate($query, $request->query->getInt('page', 1));

		return $this->render(Author::class::LIST_TEMPLATE, [
			'paginator' => $paginator,
		]);
	}

	#[Route('/author', name: 'author_page')]
	/**
	 * Страница создания/редактирования автора
	 * 
	 * @param Request $request
	 * 
	 * @return Response
	 */
	public function page(Request $request): Response
	{
		$authorId = $request->query->get('id');
		$Author = $authorId
			? $this->validateId($authorId)
			: new Author();

		$form = $this
			->createForm(AuthorType::class, $Author)
			->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->repo->save($form->getData());

			return $this->redirectToRoute('authors');
		}

		return $this->render(Author::class::PAGE_TEMPLATE, [
			'authorForm' => $form,
			'authorId' => $authorId,
		]);
	}

	#[Route('/author/delete/{id}', name: 'remove_author')]
	/**
	 * Функция удаления автора
	 * 
	 * @param int $id
	 * 
	 * @return RedirectResponse
	 */
	public function delete(int $id): RedirectResponse
	{
		$Author = $this->validateId($id);
		$this->repo->remove($Author);

		return $this->redirectToRoute('authors');
	}

	/**
	 * Проверяет id автора на существование
	 * 
	 * @param int $id
	 * 
	 * @return Author
	 */
	private function validateId(int $id): Author
	{
		$Author = $this->repo->findOneBy(['id' => $id]);

		if (!$Author) {
			throw $this->createNotFoundException('Автор не найден');
		}

		return $Author;
	}
}
