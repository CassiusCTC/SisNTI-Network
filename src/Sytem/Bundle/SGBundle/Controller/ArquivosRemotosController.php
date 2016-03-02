<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sytem\Bundle\SGBundle\Entity\ArquivosRemotos;
use Sytem\Bundle\SGBundle\Form\ArquivosRemotosType;

/**
 * Servidores controller.
 *
 */
class ArquivosRemotosController extends Controller
{

    /**
     * Lists all Servidores entities.
     *
     */
    public function indexAction()
    {

       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
      //echo $this->patchweb(); exit();
        
        
        $em = $this->getDoctrine()->getManager();
         $this->atualisafile();
        $entities = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();

        return $this->render('SytemSGBundle:ArquivosRemotos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Displays a form to edit an existing Classificacao entity.
     *
     */
    public function editAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:ArquivosRemotos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArquivosRemotos entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);
        $this->atualisafile();
        return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Admins entity.
    *
    * @param ArquivosRemotos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ArquivosRemotos $entity)
    {
        $form = $this->createForm(new ArquivosRemotosType(), $entity, array(
            'action' => $this->generateUrl('arquivosremotos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar'));

        return $form;
    }
    /**
     * Edits an existing Classificacao entity. E Valida os ips.
     *
     */
    public function updateAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $campos2 = $request->request->all()["sytem_bundle_sgbundle_arquivosremotos"];
        $ip = $campos2['serverip'];
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:ArquivosRemotos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArquivosRemotos entity.');
        }
       
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
              if(!$ip==""){
                if(!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$ip, $matches )){
                    $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips  validos!!');
                    return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
                     'entity'      => $entity,
                     'edit_form'   => $editForm->createView(),           
                    ));
                }else if(preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$ip,$matches)) {
                                  
                    $ipValidate=explode(".",$ip);
                    $ipValidate[0];
                    $ipValidate[1];
                    $ipValidate[2];
                    $ipValidate[3];
                    $ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
                    
                    if(!strcmp($ipValidate[0],"000")||!strcmp($ipValidate[0],"00")||!strcmp($ipValidate[0],"0")){
                        $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips validos.');
                        return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
                        'entity'      => $entity,
                        'edit_form'   => $editForm->createView(),           
                        ));
                    }else if((!strcmp($ipValidate[0],"000")||!strcmp($ipValidate[0],"00")||!strcmp($ipValidate[0],"0"))&&($ipValidate[3]>254 || $ipValidate[3]<1)){
                        $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips  validos.');
                        return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
                         'entity'      => $entity,
                         'edit_form'   => $editForm->createView(),           
                        ));
                    }else if($ipValidate[3]>254 || $ipValidate[3]<1){
                        $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips  validos.');
                        return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
                         'entity'      => $entity,
                         'edit_form'   => $editForm->createView(),           
                        ));
                    }
                }
            }               
            
            $entity->setCaminhoOrigem($this->patchweb());
            $em->flush();
            $this->atualisafile();
            $this->get('session')->getFlashBag()->set('message','Arquivo atualizado com sucesso.');

            return $this->redirect($this->generateUrl('arquivosremotos_edit', array('id' => $id)));
        
       }
        return $this->render('SytemSGBundle:ArquivosRemotos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    public function atualisafile( ){
       
       $file = $this->container->get('file_locator')->locate('@SytemSGBundle/Resources/file_routers/file_roters_remoto.txt');
       $em = $this->getDoctrine()->getManager();
       $entities = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
       $pathdhcpOrigem=$entities[0]->getCaminhoOrigem();
       $pathdhcpDestino=$entities[0]->getCaminhoDestino();
       $pathdhcplogOrigem=$entities[1]->getCaminhoOrigem();
       $pathdhcplogDestino=$entities[1]->getCaminhoDestino();
       $pathdhcpOrigemCompleta=$entities[0]->getCaminhoOrigem().$entities[0]->getNomeArquivo();
       $pathdhcpDestinoCompleta=$entities[0]->getCaminhoDestino().$entities[0]->getNomeArquivo();
       $pathdhcplogOrigemCompleta=$entities[1]->getCaminhoOrigem().$entities[1]->getNomeArquivo();
       $pathdhcplogDestinoCompleta=$entities[1]->getCaminhoDestino().$entities[1]->getNomeArquivo();
       
       //arp e icea.h
       $pathArpOrigem=$entities[2]->getCaminhoOrigem();
       $pathArpDestino=$entities[2]->getCaminhoDestino();
       $pathiceaOrigem=$entities[3]->getCaminhoOrigem();
       $pathiceaDestino=$entities[3]->getCaminhoDestino();
       $pathArpOrigemCompleta=$entities[2]->getCaminhoOrigem().$entities[2]->getNomeArquivo();
       $pathArpDestinoCompleta=$entities[2]->getCaminhoDestino().$entities[2]->getNomeArquivo();
       $pathiceaOrigemCompleta=$entities[3]->getCaminhoOrigem().$entities[3]->getNomeArquivo();
       $pathiceaDestinoCompleta=$entities[3]->getCaminhoDestino().$entities[3]->getNomeArquivo();
        
            
            if(file_exists($file)){        
              $arq= fopen($file, "w");
              /*0*/fputs($arq,trim($pathdhcpOrigem)."\n");
              /*1*/fputs($arq,trim($pathdhcpDestino)."\n");                           
              /*4*/fputs($arq,trim($pathdhcpOrigemCompleta)."\n");
              /*5*/fputs($arq,trim($pathdhcpDestinoCompleta)."\n"); 

              /*2*/fputs($arq,trim($pathdhcplogOrigem)."\n");
              /*3*/fputs($arq,trim($pathdhcplogDestino)."\n");
              /*6*/fputs($arq,trim($pathdhcplogOrigemCompleta)."\n");
              /*7*/fputs($arq,trim($pathdhcplogDestinoCompleta)."\n"); 

              /*8*/fputs($arq,trim($pathArpOrigem)."\n");
              /*9*/fputs($arq,trim($pathArpDestino)."\n");                            
              /*12*/fputs($arq,trim($pathArpOrigemCompleta)."\n");
              /*13*/fputs($arq,trim($pathArpDestinoCompleta)."\n");

              /*10*/fputs($arq,trim($pathiceaOrigem)."\n");
              /*11*/fputs($arq,trim($pathiceaDestino)."\n"); 
              /*14*/fputs($arq,trim($pathiceaOrigemCompleta)."\n");
              /*15*/fputs($arq,trim($pathiceaDestinoCompleta)."\n");           
             
            }
      
       fclose($arq);
      
    }

    public function patchweb(){
      $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/systemfiles/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }

   
    
}
