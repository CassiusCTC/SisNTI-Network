<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Excecoes;
use Sytem\Bundle\SGBundle\Form\ExcecoesType;

use Sytem\Bundle\SGBundle\Entity\Usuariosystem;
use Sytem\Bundle\SGBundle\Form\UsuariosystemType;

/**
 * Classificacao controller.
 *
 */
class ExcecoesController extends Controller
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
        $entities = $em->getRepository('SytemSGBundle:Excecoes')->findAll();

        return $this->render('SytemSGBundle:Excecoes:index.html.twig', array(
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
       $campos = $request->request->all()["sytem_bundle_sgbundle_excecoes"];
       $usuario=$campos['cpf'];


        $entity = new Excecoes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O cpf não existe no ldap ou não há conexão.');
                          return $this->render('SytemSGBundle:Excecoes:new.html.twig', array(
                                'entity' => $entity,
                                'form'   => $form->createView(),
                          ));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            //$this->gerarquivo();
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('excecoes', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Excecoes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Excecoes entity.
     *
     * @param Admins $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Excecoes $entity)
    {
        $form = $this->createForm(new ExcecoesType(), $entity, array(
            'action' => $this->generateUrl('excecoes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Excecoes entity.
     *
     */
    public function newAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $entity = new Excecoes();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Excecoes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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

        $entity = $em->getRepository('SytemSGBundle:Excecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Excecoes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Excecoes:show.html.twig', array(
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

        $entity = $em->getRepository('SytemSGBundle:Excecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Excecoes entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
         //$this->gerarquivo();
        return $this->render('SytemSGBundle:Excecoes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Excecoes entity.
    *
    * @param Excecoes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Excecoes $entity)
    {
        $form = $this->createForm(new ExcecoesType(), $entity, array(
            'action' => $this->generateUrl('excecoes_update', array('id' => $entity->getId())),
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

       $campos = $request->request->all()["sytem_bundle_sgbundle_excecoes"];
       $usuario=$campos['cpf'];


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Excecoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Excecoes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
         //$this->gerarquivo();
        if ($editForm->isValid()) {
            if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O cpf não existe no ldap ou não há conexão.');
                          return $this->render('SytemSGBundle:Excecoes:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          ));
            }
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            return $this->redirect($this->generateUrl('excecoes_edit', array('id' => $id)));
        }
         
        return $this->render('SytemSGBundle:Excecoes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Excecoes entity.
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
            $entity = $em->getRepository('SytemSGBundle:Excecoes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Excecoes entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('excecoes'));
    }

    /**
     * Creates a form to delete a Excecoes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('excecoes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    /*
     * Verifica se o cpf existe caso contrario verifica no ldap se sim o persiste como usuario
     *
    */

    public function searchldap($username/*Request $request*/){

       
      $em = $this->getDoctrine()->getManager();
      $query2= $em->getRepository('SytemSGBundle:ServidorLdap')->findAll();
        if($query2/*file_exists($file3*/){
           // $row1=file($file3); 
          foreach ($query2 as $key => $value) {
            $ipservidor=$value->getIpLdap();//trim($row1[0]);
            $complemento=$value->getComplemento();//trim($row1[1]);
            $login=$value->getUsername();//trim($row1[2]);
            $senha=$value->getPassword();//trim($row1[3]);
          }      
          $ldapconn = @ldap_connect($ipservidor/*"200.239.152.140"*/);
          if($ldapconn){
               $bind = @ldap_bind($ldapconn, $login,$senha/*"cn=arp,dc=ufop,dc=br","th0123"*/);            
                    if($bind){
                          $filter = "(uid=".$username.")"; // this command requires some filter
                          $justthese = array("uid", "cn", "sn", "mail", "gidNumber","telephonenumber"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                          $sr = ldap_search($ldapconn,$complemento/*"dc=ufop,dc=br"*/, $filter, $justthese);
                          $entry = ldap_get_entries($ldapconn, $sr);
                          if($entry['count']>0){
                        
                              $cpf=$entry[0]['uid'][0];
                              $nome=$entry[0]['cn'][0] . " " . $entry[0]['sn'][0];
                              $telefone=$entry[0]['telephonenumber'][0];
                              $email=$entry[0]['mail'][0];
                              $this->persistUser($cpf,$nome,$telefone,$email);
                              return TRUE;                                        
                          }
                          else{
                              return FALSE; 
                          }
                    }
                    @ldap_close($ldapconn);
          }else{
            return FALSE; 
          }
       }else{
            return FALSE; 
       }
       
   }

   /*
   * Persiste o usuario com infoemaçoes do ldap
   */
   public function persistUser($cpf,$nome,$telefone,$email){

      $em = $this->getDoctrine()->getManager();
      $usuario= new Usuariosystem();
      $valuex=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($cpf);
       
        if(!$valuex){
           $usuario= new Usuariosystem($cpf,$nome,$telefone,$email);
           $em->persist($usuario);
           $em->flush();
        }

    }

    
}
