<?php

namespace Sytem\Bundle\SGBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServidorLdapType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ipLdap')
            ->add('complemento')
            ->add('username')
            ->add('password','password',array('required'=>true))
            //->add('tiposerver')           
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sytem\Bundle\SGBundle\Entity\ServidorLdap'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sytem_bundle_sgbundle_servidorLdap';
    }
}

