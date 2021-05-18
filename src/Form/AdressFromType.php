<?php

namespace App\Form;

use App\Entity\AdresseUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressFromType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('prenom')
            ->add('phone')
            ->add('adresse')
            ->add('cp', null, [
                'attr' => [
                    'class' => 'cp',
                    'maxlength' => 5
                ]
            ])
            ->add('ville', ChoiceType::class, [
                'choices' => $this->getChoises()
            ])
            ->add('pays')
            ->add('complement', null, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdresseUser::class,
        ]);
    }

    private function getChoises ()
    {
        $choises = [];
        foreach (Countries::getNames() as $key => $country) {
            $choises[$country] = $key;
        }
        return $choises;
    }
}
