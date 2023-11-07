<?php

namespace App\Form\Type;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Тип формы книги
 */
class BookType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, ['label' => 'Название'])
			->add('year', IntegerType::class, ['label' => 'Год издания', 'attr' => ['min' => 0, 'max' => 2100,]])
			->add('isbn', TextType::class, ['label' => 'ISBN'])
			->add('pages', IntegerType::class, ['label' => 'Кол-во страниц'])
			->add(
				'cover',
				FileType::class,
				[
					'label' => 'Загрузите обложку (.jpeg, .png, до 7Мб)',
					'mapped' => false,
					'required' => false,
					'attr' => ['value' => $options['data']->getCover() ?: ''],
					'constraints' => [
						new File([
							'maxSize' => '7168k',
							'mimeTypes' => [
								'image/jpeg',
								'image/png',
							],
							'mimeTypesMessage' => 'Пожалуйста, загрузите изображение .png или .jpeg',
						])
					],
				]
			)
			->add(
				'authors',
				EntityType::class,
				[
					'label' => 'Авторы книги',
					'class' => Author::class,
					'choice_label' => fn(?Author $author) => $author ? $author->getFullName() : '',
					'choice_value' => fn(?Author $author) => $author ? $author->getId() : '',
					'choice_attr' => function ($choice) use ($options) {
						if (
							$options['data']->getAuthors() instanceof Collection
							&& $choice instanceof Author
						) {
							if ($options['data']->getAuthors()->contains($choice)) {
								return ['selected' => 'selected'];
							}
						}
						return [];
					},
					'multiple' => true,
					'expanded' => true,
					'by_reference' => false,
					'placeholder' => 'Выберите автора',
				]
			)
			->add('save', SubmitType::class, ['label' => 'Сохранить'])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Book::class,
		]);
	}
}