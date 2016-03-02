<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Tipos;
use Sytem\Bundle\SGBundle\Form\TiposType;

/**
 * Classificacao controller.
 *
 */
class TiposController extends Controller
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
        $entities = $em->getRepository('SytemSGBundle:Tipos')->findAll();

        return $this->render('SytemSGBundle:Tipos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Admins entity.
     *
     */
    public function createAction(Request $request)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $entity = new Tipos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('tipos', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Tipos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tipos entity.
     *
     * @param Tipos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tipos $entity)
    {
        $form = $this->createForm(new TiposType(), $entity, array(
            'action' => $this->generateUrl('tipos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Tipos entity.
     *
     */
    public function newAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $entity = new Tipos();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Tipos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Admins entity.
     *
     */
    public function showAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Tipos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tipos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Tipos:show.html.twig', array(
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Tipos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tiposentity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('SytemSGBundle:Tipos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tipos entity.
    *
    * @param Tipos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tipos $entity)
    {
        $form = $this->createForm(new TiposType(), $entity, array(
            'action' => $this->generateUrl('tipos_update', array('id' => $entity->getId())),
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
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Tipos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tipos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
         
        if ($editForm->isValid()) {
            $em->flush();
            //$this->gerarquivo();
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            return $this->redirect($this->generateUrl('tipos'));
        }
         
        return $this->render('SytemSGBundle:Tipos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tipos entity.
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
            $entity = $em->getRepository('SytemSGBundle:Tipos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tipos entity.');
            }

            $em->remove($entity);
            $em->flush();
           
            $this->get('session')->getFlashBag()->set('message', 'Removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('tipos'));
    }

    /**
     * Creates a form to delete a Admins entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

   
}
