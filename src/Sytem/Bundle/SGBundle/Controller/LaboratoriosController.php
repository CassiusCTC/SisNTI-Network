<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Laboratorios;
use Sytem\Bundle\SGBundle\Form\LaboratoriosType;

/**
 * Laboratorios controller.
 *
 */
class LaboratoriosController extends Controller
{

    /**
     * Lists all Laboratorios entities.
     *
     */
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SytemSGBundle:Laboratorios')->findAll();
       date_default_timezone_set("America/Sao_Paulo");
        
        return $this->render('SytemSGBundle:Laboratorios:index.html.twig', array(
            'entities' => $entities,
            'data'=> new \DateTime(),
        ));
    }
    /**
     * Creates a new Laboratorios entity.
     *
     */
    public function createAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $entity = new Laboratorios();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Laboratorio criado com sucesso.');
        
            return $this->redirect($this->generateUrl('laboratorios', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Laboratorios:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Laboratorios entity.
     *
     * @param Laboratorios $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Laboratorios $entity)
    {
        $form = $this->createForm(new LaboratoriosType(), $entity, array(
            'action' => $this->generateUrl('laboratorios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Laboratorios entity.
     *
     */
    public function newAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $entity = new Laboratorios();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Laboratorios:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Laboratorios entity.
     *
     */
    public function showAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Laboratorios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Laboratorios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Laboratorios:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Laboratorios entity.
     *
     */
    public function editAction($id)
    {
        
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Laboratorios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Laboratorios entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Laboratorios:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Laboratorios entity.
    *
    * @param Laboratorios $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Laboratorios $entity)
    {
        $form = $this->createForm(new LaboratoriosType(), $entity, array(
            'action' => $this->generateUrl('laboratorios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar'));

        return $form;
    }
    /**
     * Edits an existing Laboratorios entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Laboratorios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Laboratorios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Laboratorio atualisado com sucesso.');
        
            return $this->redirect($this->generateUrl('laboratorios_edit', array('id' => $id)));
        }

        return $this->render('SytemSGBundle:Laboratorios:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Laboratorios entity.
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
            $entity = $em->getRepository('SytemSGBundle:Laboratorios')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Laboratorios entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Laboratorio removido com sucesso.');
        
        }

        return $this->redirect($this->generateUrl('laboratorios'));
    }

    /**
     * Creates a form to delete a Laboratorios entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('laboratorios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
