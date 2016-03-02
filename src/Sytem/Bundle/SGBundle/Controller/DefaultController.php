<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SytemSGBundle:Default:index.html.twig');
    }

    public function errorAction()
    {
        return $this->render('SytemSGBundle:Default:error.html.twig');
    }
    public function emailAction()
    {   $arraysuport=array();
    	$arraysuport[0]="Cassius Tales Cordeiro";
        $arraysuport[1]="email";
        $arraysuport[2]="Suporte ti";
        $arraysuport[3]="Sua mensagem de contato foi enviada com sucesso. Aguarde a resposta do Suporte de Informática.";

        return $this->render('SytemSGBundle:Default:email.html.twig', array(
                    'array'=>$arraysuport,
                ));
    }

    public function tutorialAction()
    {
       if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        return $this->render('SytemSGBundle:Default:tutorial.html.twig');
    }
    
}
