<?php


namespace App\Form;


use App\Entity\Rent;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['em'] !== null) {
            $em = $options['em'];
        } else {
            throw new RuntimeException('em must be set');
        }

        $builder
            ->add('book')
            ->add('renter')
            ->add('quantity', IntegerType::class)
            ->add('days', IntegerType::class)
            ->add('sum', MoneyType::class, [
                'currency' => 'RUB'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rent::class,
            'csrf_protection' => false,
            'em' => null,
            'allow_extra_fields' => true
        ]);
    }
}