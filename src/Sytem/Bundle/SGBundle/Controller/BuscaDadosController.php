<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collection\ArrayCollection;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
//use Sytem\Bundle\SGBundle\Entity\DadosArquivosRepository;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;

use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;

use Sytem\Bundle\SGBundle\Entity\Solicitacoes;
use Sytem\Bundle\SGBundle\Form\SolicitacoesType;

class BuscaDadosController extends Controller
{

public function search_ipAction(Request $request ){      
   
   if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
   }    
       if($request->getMethod()=='POST'){
              
              $op=$request->get('opcao');
              $search=$request->get('valor');
             

              if (!is_null($search)||!strcmp($search,"")){
                if($op==1){
                     
                     $em = $this->getDoctrine()->getManager();
                     $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findBy(array('mac'=>$search)); 
                     
                     $cont=count($entities);                    
                     if($cont>0){
                       $this->get('session')->getFlashBag()->set('messagem',' Busca feita com sucesso Mac  '. $search.' encontrado.');
                       
                       return $this->render('SytemSGBundle:BuscaDados:search_ip.html.twig', array(
                        'entities' => $entities,


                       ));

                     }else{ 
                       
                       $this->get('session')->getFlashBag()->set('message1',' O Mac não foi encontrado.');
                       return $this->redirect($this->generateUrl('search'));

                     }
                }
                if($op==2){
                     
                     $em = $this->getDoctrine()->getManager();                    
                     $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findBy(array('ip'=>$search)); 
                     
                     $cont=count($entities);                    
                     if($cont>0){
                       $this->get('session')->getFlashBag()->set('messagem',' Busca feita com sucesso, IP '. $search.' encontrado.');
                       return $this->render('SytemSGBundle:BuscaDados:search_ip.html.twig', array(
                       'entities' => $entities,
                       ));

                     }else{                      
                       $this->get('session')->getFlashBag()->set('message1',' O IP não foi encontrado.');
                       return $this->redirect($this->generateUrl('search'));
                     }                    
                }
                if($op==3){
                     
                     $em = $this->getDoctrine()->getManager();
                     $entities=null;
                     $dateform=explode("-", $search);
                     $dateBD=$dateform[2]."-".$dateform[1]."-".$dateform[0];                     
                     $dataresult=date($dateBD);                     
                     $repositorio=$em->getRepository('SytemSGBundle:DadosArquivos');
                     $query=$repositorio->createqueryBuilder('d')
                             ->where('d.dataCadastro= :dataCadastro') 
                             ->setParameter('dataCadastro',$dataresult ) 
                             ->orderBy('d.mac','ASC')
                             ->getQuery();
                     $entities = $query->getResult();                

        
                     //$entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findBy(array('name'=>$search), array('ip'=>'ASC'));
                     $cont=count($entities);                    
                     if($cont>0){

                       $this->get('session')->getFlashBag()->set('messagem',' Busca feita com sucesso, data '. $search.' encontrada.');
                       
                   
                         return $this->render('SytemSGBundle:BuscaDados:search_ip.html.twig', array(
                         'entities' => $entities, 
                                          
                         ));
                      

                     }else{                      
                       $this->get('session')->getFlashBag()->set('message1',' A data de cadastro não foi encontrada.');
                       return $this->redirect($this->generateUrl('search'));
                     }                    
                 }
                 if($op==4){
                     
                     $em = $this->getDoctrine()->getManager();
                     $entities=null;
                    
                       $repositorio=$em->getRepository('SytemSGBundle:DadosArquivos');
                       $query=$repositorio->createqueryBuilder('d')
                             ->where('d.status = :status') 
                             ->setParameter('status',$search) 
                             ->orderBy('d.ip','ASC')
                             ->getQuery();
                       $entities = $query->getResult();
                     $cont=count($entities);                    
                     if($cont>0){
                       $this->get('session')->getFlashBag()->set('messagem',' Busca feita com sucesso, status '. $search.' encontrado.');
                       return $this->render('SytemSGBundle:BuscaDados:search_ip.html.twig', array(
                       'entities' => $entities,   
                                           
                       ));

                     }else{
                       
                       $this->get('session')->getFlashBag()->set('message1',' O Status não foi encontrado.');
                       return $this->redirect($this->generateUrl('search'));

                     }                    
                }
               

                if($op==6){
                     $em2 = $this->getDoctrine()->getManager();                    
                     $lab = $em2->getRepository('SytemSGBundle:Laboratorios')->findBy(array('nomeLaboratorio'=>$search)); 
                     $em = $this->getDoctrine()->getManager();
                     $entities=null;
                     
                       $repositorio=$em->getRepository('SytemSGBundle:DadosArquivos');
                       $query=$repositorio->createqueryBuilder('d')
                             ->where('d.laboratorio = :laboratorio') 
                             ->setParameter('laboratorio',$lab) 
                             ->orderBy('d.ip','ASC')
                             ->getQuery();
                       $entities = $query->getResult();
                    $cont=count($entities);                    
                    if($cont>0){
                       $this->get('session')->getFlashBag()->set('messagem',' Busca feita com sucesso, status '. $search.' encontrado.');
                       
                      
                         return $this->render('SytemSGBundle:BuscaDados:search_ip.html.twig', array(
                         'entities' => $entities, 
                                            
                         ));
                      

                     }else{
                       
                       $this->get('session')->getFlashBag()->set('message1','  Não há registros referentes ao laboratorio especificado.');
                       return $this->redirect($this->generateUrl('search'));

                     }                    
                   }

                   

              }
              else{
                return $this->redirect($this->generateUrl('search'));
              }

        }
      }

	public function search_desbloqueadoAction(Request $request){
     
   if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
  }     
       

        $em = $this->getDoctrine()->getManager();                    
        
        $repositorio=$em->getRepository('SytemSGBundle:DadosArquivos');
                       $query=$repositorio->createqueryBuilder('a')
                             ->where('a.status = :status') 
                             ->setParameter('status','DESBLOQUEADO') 
                             ->orderBy('a.ip','ASC')
                             ->getQuery();
                        $query = $query->getResult();    

        return $this->render('SytemSGBundle:BuscaDados:search_desbloqueado.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Status Desboqueado",
        
        ));

    
    }
    public function search_bloqueadoAction(Request $request){
     
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
        //$session = $this->getRequest()->getSession();
                      
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.status = 'BLOQUEADO' order by a.mac  ";  
        $query = $em->createQuery($dql);
        $query = $query->getResult(); 

        // parameters to template
        return $this->render('SytemSGBundle:BuscaDados:search_desbloqueado.html.twig', array('pagination' =>  $query ,
         'data'=> new \DateTime(),
         'status'=> " Status Bloqueado"
        ));

    
    }

    public function search_externoAction(Request $request){
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();                    
        $lab = $em->getRepository('SytemSGBundle:Laboratorios')->findBy(array('nomeLaboratorio'=>'Dispositivos Externos')); 
        $repositorio=$em->getRepository('SytemSGBundle:DadosArquivos');
                       $query=$repositorio->createqueryBuilder('a')
                             ->where('a.laboratorio = :laboratorio') 
                             ->setParameter('laboratorio',$lab) 
                             ->orderBy('a.ip','ASC')
                             ->getQuery();
                        $query = $query->getResult();             
                      
       
        // parameters to template
        return $this->render('SytemSGBundle:BuscaDados:search_desbloqueado.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Computadores que não fazem parte do patrimonio local"
        ));

    
    }

    public function search_serverAction(Request $request){
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        
        //$session = $this->getRequest()->getSession();
                      
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.tipo = 'Servidor' order by a.mac  ";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();


        // parameters to template
        return $this->render('SytemSGBundle:BuscaDados:search_desbloqueado.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Servidores Ufop"
        ));

    
    }
    public function searchAction(){
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        return $this->render('SytemSGBundle:BuscaDados:search.html.twig'        
        );
    }


}