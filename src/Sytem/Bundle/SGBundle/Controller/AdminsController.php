<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Admins;
use Sytem\Bundle\SGBundle\Form\AdminsType;

use Sytem\Bundle\SGBundle\Entity\Usuariosystem;
use Sytem\Bundle\SGBundle\Form\UsuariosystemType;

/**
 * Classificacao controller.
 *
 */
class AdminsController extends Controller
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
        $entities = $em->getRepository('SytemSGBundle:Admins')->findAll();

        return $this->render('SytemSGBundle:Admins:index.html.twig', array(
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
       $campos = $request->request->all()["sytem_bundle_sgbundle_admins"];
       $usuario=$campos['cpf'];

        $entity = new Admins();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

                
            if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O cpf não existe no ldap ou não há conexão.');
                          return $this->render('SytemSGBundle:Admins:new.html.twig', array(
                            'entity' => $entity,
                            'form'   => $form->createView(),
                        ));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('admins', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Admins:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Admins entity.
     *
     * @param Admins $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Admins $entity)
    {
        $form = $this->createForm(new AdminsType(), $entity, array(
            'action' => $this->generateUrl('admins_create'),
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
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $entity = new Admins();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Admins:new.html.twig', array(
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

        $entity = $em->getRepository('SytemSGBundle:Admins')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admins entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Admins:show.html.twig', array(
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

        $entity = $em->getRepository('SytemSGBundle:Admins')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admins entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
         //$this->gerarquivo();
        return $this->render('SytemSGBundle:Admins:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Admins entity.
    *
    * @param Admins $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Admins $entity)
    {
        $form = $this->createForm(new AdminsType(), $entity, array(
            'action' => $this->generateUrl('admins_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('SytemSGBundle:Admins')->find($id);
        $campos = $request->request->all()["sytem_bundle_sgbundle_admins"];
        $usuario=$campos['cpf'];
                

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admins entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
         
        if ($editForm->isValid()) {

            if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O cpf não existe no ldap ou não há conexão.');
                           return $this->render('SytemSGBundle:Admins:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                      ));
            }
            
            $em->flush();
            $this->gerarquivo();
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            return $this->redirect($this->generateUrl('admins_edit', array('id' => $id)));
        }
         
        return $this->render('SytemSGBundle:Admins:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Admins entity.
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
            $entity = $em->getRepository('SytemSGBundle:Admins')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Admins entity.');
            }

            $em->remove($entity);
            $em->flush();
           
            $this->get('session')->getFlashBag()->set('message', 'Removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('admins'));
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
            ->setAction($this->generateUrl('admins_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function gerarquivo(){
        $file=$this->container->get('file_locator')->locate('@SytemSGBundle/Services/file/filecpf.txt');    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:Admins')->findAll();
        if(file_exists($file)){
        
              $arq= fopen($file, "w");
              foreach ($entity as $key => $value) {
                   fputs($arq,trim($value->getCpf())."\n");
              }
              fclose($arq);
             
              
             
          }

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
