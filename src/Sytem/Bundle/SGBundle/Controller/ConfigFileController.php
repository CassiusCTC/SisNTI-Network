<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Config\FileLocator;

//use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigFileController extends Controller
{
    

    public function configpasswordAction()
    {   
    
    if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
    }

      $result=1;     
      $path = $this->container->get('file_locator')->locate('@SytemSGBundle/Services/file/file_password.txt');      
    
    if(file_exists($path)){
        
        $lines = file ($path);
        $this->get('session')->getFlashBag()->set('messageExecut', 'Arquivo encontrado.');
        $result= $lines;

    }else{
          $this->get('session')->getFlashBag()->set('messageExecut', 'Arquivo não encontrado.');
          //echo "to aqui 2";
         //exit();

    }
        
        return $this->render('SytemSGBundle:ConfigFile:configpassword.html.twig', array('path_file'=>$result,
                // ...
     ));    
  }

    

    public function atualisadminmasterAction(Request $request ){
      if ( !$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       
       $file = $this->container->get('file_locator')->locate('@SytemSGBundle/Services/file/file_password.txt');
       $arq=null;
       $login=$request->get('key0');
       $senha=$request->get('key1');
       $role='AdminMaster';
       $tel='000000000';
       $nome='Administrador Master_Nti';
       $cript=md5($senha);
       

       if($request->getMethod()=='POST'){
            if(file_exists($file)){
        
              $arq= fopen($file, "w");
              fputs($arq,trim($login)."\n");
              fputs($arq,trim($cript)."\n");
              fputs($arq,trim($role)."\n");
              fputs($arq,trim($tel)."\n");
              fputs($arq,trim($nome)."\n");
          }
       }
       fclose($arq);
       $lines = file ($file);
       $this->get('session')->getFlashBag()->set('message', 'Atualização feita com sucesso!! Username: '.$login.' Password: '.'******');
          
      
       return $this->render('SytemSGBundle:ConfigFile:configpassword.html.twig', array('path_file'=>$lines,));
        
    }
             

}
