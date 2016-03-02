<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;

/**
 * IntervalosIps controller.
 *
 */
class IntervalosipsController extends Controller
{

    /**
     * Lists all Laboratorios entities.
     *
     */
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SytemSGBundle:Intervalosips')->findAll();
        date_default_timezone_set("America/Sao_Paulo");
        
        return $this->render('SytemSGBundle:Intervalosips:index.html.twig', array(
            'entities' => $entities,
            'data'=> new \DateTime(),
        ));
    }
    /**
     * Creates a new Intervalosips entity.
     *
     */
    public function createAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
        $entity = new Intervalosips();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('intervalosips', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Intervalosips:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Intervalosips entity.
     *
     * @param Intervalosips $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Intervalosips $entity)
    {
        $form = $this->createForm(new IntervalosipsType(), $entity, array(
            'action' => $this->generateUrl('intervalosips_create'),
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

        $entity = new Intervalosips();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Intervalosips:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a IntervalosIps entity.
     *
     */
    public function showAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Intervalosips')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Intervalos de Ips entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Intervalosips:show.html.twig', array(
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

        $entity = $em->getRepository('SytemSGBundle:Intervalosips')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Intervalos Ips entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Intervalosips:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a IntervalosIps entity.
    *
    * @param Intervalosips $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Intervalosips $entity)
    {
        $form = $this->createForm(new IntervalosipsType(), $entity, array(
            'action' => $this->generateUrl('intervalosips_update', array('id' => $entity->getId())),
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
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Intervalosips')->find($id);
        $anterior=$entity->getIntervalo();
        $new = $request->get('intervalo');
        $options=$request->get('optionx');
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Intervalosips entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $campos = $request->request->all()["sytem_bundle_sgbundle_intervalosIps"];
        $ipnew = $campos['intervalo'];
        
        if ($editForm->isValid()) {

          if($options==1){
            //echo $ipnew;
            //echo "<br>".$anterior;
        
            $this->atualisaIntervalo($anterior,$ipnew);
          }
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Editado com sucesso.');
            return $this->redirect($this->generateUrl('intervalosips'));
        }

        return $this->render('SytemSGBundle:Intervalosips:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a IntervalosIps entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
        $option=0;
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $form_checkbox=$request->get('option');
        
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SytemSGBundle:Intervalosips')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Intervalo de Ip não existe na base de dados');
            }
            if($form_checkbox==1){
              $this->removeIntervalo($entity->getIntervalo());
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->set('message', 'Item removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('intervalosips'));
    }

    /**
     * Creates a form to delete a Intervalosips entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('intervalosips_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function removeIntervalo($intervalo){
          $em = $this->getDoctrine()->getManager();
          $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();

         foreach ($entities as $key => $value) {
            $ipx=explode(".", $value->getIp());
            $ip=$ipx[0].".".$ipx[1].".".$ipx[2];
            if(!strcmp($intervalo, $ip)){
                $em->remove($value);
                $em->flush();
            }
         }
    }

    private function atualisaIntervalo($intervalo,$new){
          $em = $this->getDoctrine()->getManager();
          $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();

         foreach ($entities as $key => $value) {
            $ipx=explode(".", $value->getIp());
            $ip=$ipx[0].".".$ipx[1].".".$ipx[2];
            if(!strcmp($intervalo, $ip)){
                $ipnew=$new.".".$ipx[3];
                $value->setIp($ipnew);
                $em->flush();
            }
         }
    }

   

}
