<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Ipsexcecoes;
use Sytem\Bundle\SGBundle\Form\IpsexcecoesType;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;

use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;

/**
 * Ipsexcecoes controller. Controla os ips destinados a impressoaras  
 *  e outros dispositivos cuja necessidade de cadastro na rede não se faça necessaria
 *
 */
class IpsexcecoesController extends Controller
{

    /**
     * Lists all Admins entities.
     *
     */
    public function indexAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();
        //$this->gerarquivo();
        $entities = $em->getRepository('SytemSGBundle:Ipsexcecoes')->findAll();

        return $this->render('SytemSGBundle:Ipsexcecoes:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Excecoes entity.
     *
     */
    public function createAction(Request $request)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $campos = $request->request->all()["sytem_bundle_sgbundle_ipsexcecoes"];
        $ip = $campos['ipexcecao'];
        $entity = new Ipsexcecoes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {

          $ipValidate=explode(".",$ip);

                    $ipValidate[0];
                    $ipValidate[1];
                    $ipValidate[2];
                    $ipValidate[3];
                    $ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
                   
                   
                   $em2 = $this->getDoctrine()->getManager();
                   if(!$em2->getRepository('SytemSGBundle:Intervalosips')->findOneByIntervalo($ipfaixa)){
                          $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips cadastrados.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Ipsexcecoes:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                   }
                   if($em2->getRepository('SytemSGBundle:DadosArquivos')->findOneByIp($ip)){
                          $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' já pertence a um dispositivo.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Ipsexcecoes:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                   }


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            //$this->gerarquivo();
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('ipsexcecoes', array('id' => $entity->getId())));
        }
        $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
        return $this->render('SytemSGBundle:Ipsexcecoes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'json' => $json,
        ));  
    }

    /*
     * Retorna os ips livres para o autocomplete dos ips nas views. 
    */
    public function ipsValidos(){
        $em = $this->getDoctrine()->getManager();
        $entities2 = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
        $em2 = $this->getDoctrine()->getManager();
        $entities = $em2->getRepository('SytemSGBundle:Intervalosips')->findAll();       
        $cont2=0;
        $listabdInterv=array();
        foreach ($entities as $key=>$value) { 
             $listabdInterv[$cont2]= $value->getIntervalo();
             $cont2++;
        }
        $listaIP = array();
        $cont=0;
        $cont3=0;
        $faixas=$cont2;
        for ($i=0;$i<$faixas;$i++){
            for($j=1;$j<255;$j++){
               $ip=$listabdInterv[$i].".".$j;
               $listaIP[$cont]=$ip;
               $cont++;
            }            
        }
        $listabd=array();
        foreach ($entities2 as $key=>$value) { 
             $listabd[$cont]= $value->getIp();
             $cont++;
        }
        $ips=array_diff($listaIP, $listabd);              
        foreach ($ips as $key => $value) {
                  $ip_livres[$cont3]= $value;
                  $cont3++;                  
        }
        return  $ip_livres;
    }


    /**
     * Creates a form to create a Excecoes entity.
     *
     * @param Admins $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ipsexcecoes $entity)
    {
        $form = $this->createForm(new IpsexcecoesType(), $entity, array(
            'action' => $this->generateUrl('ipsexcecoes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Ipsexcecoes entity.
     *
     */
    public function newAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $entity = new Ipsexcecoes();
        $form   = $this->createCreateForm($entity);
        $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
        return $this->render('SytemSGBundle:Ipsexcecoes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'json' => $json,
        ));
    }

    /**
     * Finds and displays a Excecoes entity.
     *
     */
    public function showAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Ipsexcecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ipsexcecoes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Ipsexcecoes:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Classificacao entity.
     *
     */
    public function editAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
      }
       $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true); 
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Ipsexcecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ips excecoes entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
         //$this->gerarquivo();
        return $this->render('SytemSGBundle:Ipsexcecoes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json'=>$json,
        ));
    }

    /**
    * Creates a form to edit a Ipsexcecoes entity.
    *
    * @param Excecoes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ipsexcecoes $entity)
    {
        $form = $this->createForm(new IpsexcecoesType(), $entity, array(
            'action' => $this->generateUrl('ipsexcecoes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar'));

        return $form;
    }
    /**
     * Edits an existing Excecoes entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();
        $campos = $request->request->all()["sytem_bundle_sgbundle_ipsexcecoes"];
        $ip = $campos['ipexcecao'];

        $entity = $em->getRepository('SytemSGBundle:Ipsexcecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ips excecoes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
         //$this->gerarquivo();
        if ($editForm->isValid()) {
              $ipValidate=explode(".",$ip);

                    $ipValidate[0];
                    $ipValidate[1];
                    $ipValidate[2];
                    $ipValidate[3];
                    $ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
                   
                   
                   $em2 = $this->getDoctrine()->getManager();
                   if(!$em2->getRepository('SytemSGBundle:Intervalosips')->findOneByIntervalo($ipfaixa)){
                          $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pertence a faixa de ips cadastrados.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Ipsexcecoes:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          'json' => $json,
                           ));
                   }
                  if($em2->getRepository('SytemSGBundle:DadosArquivos')->findOneByIp($ip)){
                          $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' já pertence a um dispositivo.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Ipsexcecoes:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          'json' => $json,
                           ));
                   }




            $em->flush();            
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            return $this->redirect($this->generateUrl('ipsexcecoes', array('id' => $id)));
        }
        $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);                          
        return $this->render('SytemSGBundle:Ipsexcecoes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json' => $json,
        ));
    }
    /**
     * Deletes a Ipsexcecoes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SytemSGBundle:Ipsexcecoes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ips excecoes entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('ipsexcecoes'));
    }

    /**
     * Creates a form to delete a Ipsexcecoes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ipsexcecoes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    
}
