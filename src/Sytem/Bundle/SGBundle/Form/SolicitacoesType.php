<?php

namespace Sytem\Bundle\SGBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extencion\Core\ChoiseList\ChoiceList;
use Symfony\Component\Form\Extencion\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Sytem\Bundle\SGBundle\Entity\Solicitacoes;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class SolicitacoesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder            
            ->add('mac')
            ->add('tipo', 'entity', array('class'=>'SytemSGBundle:Tipos',))
            ->add('file','file',array('data_class' => null,'required'=>false))
            ->add('laboratorio','entity', array('class'=>'SytemSGBundle:Laboratorios',))
            ->add('descricaoequip')
            ->add('justificativa')
            ->add('responsavel','entity', array('class'=>'SytemSGBundle:Usuariosystem',))
            
            //->add('usuario','entity', array('class'=>'SytemSGBundle:Usuariosystem',))
                     
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sytem\Bundle\SGBundle\Entity\Solicitacoes'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sytem_bundle_sgbundle_solicitacoes';
    }

    public static function processFile(UploadedFile $uploaded_file, Solicitacoes $name,$path)
    {
       // $path = 'pictures/blog/';
        //getClientOriginalName() => Returns the original file name.
        $uploaded_file_info = pathinfo($uploaded_file->getClientOriginalName());
        $file_name =$name->getUsuario().md5(time()). "." .$uploaded_file_info['extension']; 
        $uploaded_file->move($path, $file_name);

        return $file_name;
    }
}
