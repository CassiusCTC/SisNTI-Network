<?php

namespace Sytem\Bundle\SGBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DhcpconfType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('optionrouters')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sytem\Bundle\SGBundle\Entity\Dhcpconf'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sytem_bundle_sgbundle_dhcpconf';
    }
}

