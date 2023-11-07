<?php

namespace App\Form\Type;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Тип формы автора
 */
class AuthorType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, ['label' => 'Имя'])
			->add('patronym', TextType::class, ['label' => 'Отчество'])
			->add('surname', TextType::class, ['label' => 'Фамилия'])
			->add(
				'books',
				EntityType::class,
				[
					'label' => 'Книги автора',
					'class' => Book::class,
					'choice_label' => 'name',
					'choice_value' => fn(?Book $book) => $book ? $book->getId() : '',
					'choice_attr' => function ($choice) use ($options) {
						if (
							$options['data']->getBooks() instanceof Collection
							&& $choice instanceof Book
						) {
							if ($options['data']->getBooks()->contains($choice)) {
								return ['selected' => 'selected'];
							}
						}
						return [];
					},
					'multiple' => true,
					'expanded' => true,
					'by_reference' => false,
					'placeholder' => 'Выберите книгу',
				]
			)
			->add('save', SubmitType::class, ['label' => 'Сохранить'])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Author::class,
		]);
	}
}