<?php

namespace Sytem\Bundle\SGBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extencion\Core\ChoiseList\ChoiceList;
use Symfony\Component\Form\Extencion\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DadosArquivosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            //->add('hostname')
            ->add('ip')
            ->add('mac')
            ->add('tipo','entity', array('class'=>'SytemSGBundle:Tipos',))
            ->add('patrimonioLocal')
            //->add('responsavel','entity', array('class'=>'SytemSGBundle:Usuariosystem',))
            ->add('descricao')
            ->add('filemove', 'file',array('data_class' => null,'required'=>false))
            //->add('usuario','entity', array('class'=>'SytemSGBundle:Usuariosystem',))
            //->add('ramal')
            //->add('dataCadastro')
            ->add('status', 'choice', array('choices' => array('DESBLOQUEADO'=>'Desbloqueado','BLOQUEADO'=>'Bloqueado',)))
            //->add('status', 'choice', array('choices' => array('DESBLOQUEADO'=>'Desbloqueado','BLOQUEADO'=>'Bloqueado',), 'multiple'=>false, 'expanded'=> true, 'data'=>'DESBLOQUEADO',))
            ->add('laboratorio','entity', array('class'=>'SytemSGBundle:Laboratorios',))            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sytem\Bundle\SGBundle\Entity\DadosArquivos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sytem_bundle_sgbundle_dadosarquivos';
    }


    public static function processFile(UploadedFile $uploaded_file, DadosArquivos $name,$path)
    {
       // $path = 'pictures/blog/';
        //getClientOriginalName() => Returns the original file name.
        $uploaded_file_info = pathinfo($uploaded_file->getClientOriginalName());
        $file_name =$name->getUsuario().md5(time()). "." .$uploaded_file_info['extension']; 
        $uploaded_file->move($path, $file_name);

        return $file_name;
    }
    
    public static function processFileaux(UploadedFile $uploaded_file, $name,$path)
    {
       // $path = 'pictures/blog/';
        //getClientOriginalName() => Returns the original file name.
        $uploaded_file_info = pathinfo($uploaded_file->getClientOriginalName());
        $file_name =$name.md5(time()). "." .$uploaded_file_info['extension']; 
        $uploaded_file->move($path, $file_name);

        return $file_name;
    }


}
