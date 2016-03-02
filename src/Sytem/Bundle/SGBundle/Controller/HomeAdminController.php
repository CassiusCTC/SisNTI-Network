<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sytem\Bundle\SGBundle\Entity\Usuarios;

class HomeAdminController extends Controller
{
    public function homeadminAction(Request $request)
    {   
    	
    	 
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser();   
       $user = $this->get('security.context')->getToken()->getUser();    
       //$user=null;   
	        return $this->render('SytemSGBundle:HomeAdmin:homeadmin.html.twig', array('user'=>$user,
	                // ...
	     ));          
    }

}
