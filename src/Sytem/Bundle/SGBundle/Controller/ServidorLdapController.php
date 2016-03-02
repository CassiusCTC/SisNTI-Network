<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\ServidorLdap;
use Sytem\Bundle\SGBundle\Form\ServidorLdapType;

/**
 * Servidores controller.
 *
 */
class ServidorLdapController extends Controller
{

    /**
     * Lists all Servidores entities.
     *
     */
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SytemSGBundle:ServidorLdap')->findAll();

        return $this->render('SytemSGBundle:ServidorLdap:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Admins entity.
     *
     */
    public function createAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $entity = new ServidorLdap();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->gerarquivo();
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso com sucesso.');        
            return $this->redirect($this->generateUrl('servidorldap', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:ServidorLdap:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ServidorLdap entity.
     *
     * @param ServidorLdap $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ServidorLdap $entity)
    {
        $form = $this->createForm(new ServidorLdapType(), $entity, array(
            'action' => $this->generateUrl('servidores_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Admins entity.
     *
     */
    public function newAction()
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $entity = new ServidorLdap();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:ServidorLdap:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Admins entity.
     *
     */
    /*public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Servidores')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admins entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Servidores:show.html.twig', array(
            'entity'      => $entity,
            //'delete_form' => $deleteForm->createView(),
        ));
    }*/

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

        $entity = $em->getRepository('SytemSGBundle:ServidorLdap')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ServidorLdap entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:ServidorLdap:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ServidorLdap entity.
    *
    * @param ServidorLdap $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ServidorLdap $entity)
    {
        $form = $this->createForm(new ServidorLdapType(), $entity, array(
            'action' => $this->generateUrl('servidorldap_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar'));

        return $form;
    }
    /**
     * Edits an existing Classificacao entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:ServidorLdap')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ServidorLdap entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            $this->gerarquivo();
            return $this->redirect($this->generateUrl('servidorldap_edit', array('id' => $id)));
        }

        return $this->render('SytemSGBundle:ServidorLdap:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    public function gerarquivo(){
        $file=$this->container->get('file_locator')->locate('@SytemSGBundle/Services/file/config_ldap.txt');    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:ServidorLdap')->findAll();
        if(file_exists($file)){
        
              $arq= fopen($file, "w");
              foreach ($entity as $key => $value) {
                   fputs($arq,trim($value->getIpLdap())."\n");
                   fputs($arq,trim($value->getComplemento())."\n");
                   fputs($arq,trim($value->getUsername())."\n");
                   fputs($arq,trim($value->getPassword())."\n");
              }
              fclose($arq);
             
              
             
        }
 }
    
}
