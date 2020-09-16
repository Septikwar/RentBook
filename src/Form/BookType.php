<?php


namespace App\Form;


use App\Entity\Book;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['em'] !== null) {
            $em = $options['em'];
        } else {
            throw new RuntimeException('em must be set');
        }

        $builder
            ->add('name')
            ->add('quantity')
            ->add('price')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'csrf_protection' => false,
            'em' => null,
            'allow_extra_fields' => true
        ]);
    }
}