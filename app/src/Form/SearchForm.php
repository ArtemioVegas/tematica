<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employerId', SearchType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Поиск сотрудника',
            ])
            ->setMethod('GET')
        ;
    }
}
