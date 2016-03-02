<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sytem\Bundle\SGBundle\Entity\UserLDAP;
use Sytem\Bundle\SGBundle\Entity\Usuariosystem;
use Sytem\Bundle\SGBundle\Form\UsuariosystemType;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\SecurityContext;

class LoginController extends Controller {

    
    public function loginAction()
    {
        $request= $this->getRequest();
        $session=$request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error =  $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
         

        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
           $this->persistUser(); 
           return $this->redirect($this->generateUrl('homeadmin'), 302);
        }else if ($this->get('security.context')->isGranted('ROLE_USER')){
           $this->persistUser();
           return $this->redirect($this->generateUrl('useradmin'), 302);
        }
        
        $errors = $session->get('excecoes');
        date_default_timezone_set("UTC");
        return $this->render('SytemSGBundle:Login:login.html.twig', array(
            // last username entered by the user
            'last_username' =>  $session->get(SecurityContext::LAST_USERNAME),
            'data' => new \DateTime,
            'error'         => $error,
            
            
               
        ));
    }
     
    public function sairAction() {
        $this->get("security.context")->setToken(null);
        $session = $this->getRequest()->getSession();
        $session->remove('user');
        $session->clear();
        return $this->redirect($this->generateUrl('login'), 302);
    }
    
    public function persistUser(){

      $user = $this->get('security.context')->getToken()->getUser();  
      $em = $this->getDoctrine()->getManager();
      $usuario= new Usuariosystem();
      $valuex=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($user->getId());
       
        if(!$valuex){
           $usuario= new Usuariosystem($user->getUsername(),$user->getNome(),$user->getTelefone(),$user->getEmail());
           $em->persist($usuario);
           $em->flush();
           //$entity->setResponsavel($usuario->getCpf());
        }else{
           //$usuario= new Usuariosystem($user->getUsername(),$user->getNome(),$user->getTelefone(),$user->getEmail(),$usuario->getGrupo());
          
           $em->flush();
       }

    }

    public function informacoesAction(){

      return $this->render('SytemSGBundle:Login:informacoes.html.twig', array(
            // last username entered by the user
            'data' => new \DateTime,
      ));      

    }
    public function contatoAction(){

      return $this->render('SytemSGBundle:Login:contato.html.twig', array(
            // last username entered by the user
            'data' => new \DateTime,
      ));      

    }

    public function contatoRequisicaoAction(Request $request) {
         $nome = $request->get('nome');
         $email = $request->get('email');
         $assunto = $request->get('assunto');
         $mensagem = $request->get('mensagem');
    
        $arraysuport=array();
        $arraycliente=array();

       
       
        $arraysuport[0]=$assunto;
        $arraysuport[1]="Solicitante: ".$nome." Email: ".$email.". ".$mensagem;

        $arraycliente[0]=$nome;
        $arraycliente[1]=$email;
        $arraycliente[2]=$assunto;
        $arraycliente[3]="Sua mensagem de contato foi enviada com sucesso. Aguarde a resposta do Suporte de Informática!";
        
        
        $caminhoimg=$this->caminhoImg().'nt2.bmp';
        join('/', array(trim(" ", '/'), trim($caminhoimg, '/')));
        $caminhoimg2=$this->caminhoImg2().'campaign-monitor-logo.jpg';
        join('/', array(trim(" ", '/'), trim($caminhoimg2, '/')));


        $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
        $mailer = \Swift_Mailer::newInstance($transport);
        $messageCliente = \Swift_Message::newInstance()
                ->setSubject('Contato Suporte Informática')
                ->setFrom('suporteinformatica@icea.ufop.br', 'ICEASGR- Suporte Informática')
                ->setTo($email)
                ->setBody($this->renderView('SytemSGBundle:Default:email.html.twig', array(
                    'array'=>$arraycliente,
                    'caminho'=>$caminhoimg,
                    'caminho2'=>$caminhoimg2,
                )), 'text/html');
        $this->get('mailer')->send($messageCliente);

        $messageSuporte = \Swift_Message::newInstance()
                ->setSubject('Contato ICEASGR - ' . $assunto)
                ->setFrom($email, $nome)
                ->setTo('suporteinformatica@icea.ufop.br')
                ->setBody($this->renderView('SytemSGBundle:Default:email.html.twig', array(
                    'array'=>$arraysuport,
        )),'text/html');
        $this->get('mailer')->send($messageSuporte);

        return $this->redirect($this->generateUrl('login'));
    }

   /*
    * Gera o caminho real das imagens
   */
    public function caminhoImg(){
      $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/bundles/sytemsg/images/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }
    public function caminhoImg2(){
      $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/bundles/sytemsg/imgemail/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }
    

}