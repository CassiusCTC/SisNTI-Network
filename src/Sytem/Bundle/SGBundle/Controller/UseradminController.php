<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sytem\Bundle\SGBundle\Entity\Usuarios;

class UseradminController extends Controller
{
    public function useradminAction(Request $request)
    {  
       $session = $this->getRequest()->getSession();
       if (!$this->get('security.context')->isGranted('ROLE_USER') ) {
            return $this->redirect($this->generateUrl('error'), 302);  
       }
   
      
       $user = $this->get('security.context')->getToken()->getUser();   
	    
	        return $this->render('SytemSGBundle:Useradmin:useradmin.html.twig', array('user'=>$user,
	            // ...
	        )); 
        } 
        
}

