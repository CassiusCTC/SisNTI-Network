<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\Response;

use Sytem\Bundle\SGBundle\Entity\Grupos;
use Sytem\Bundle\SGBundle\Form\GruposType;

/**
 * Classificacao controller.
 *
 */
class GruposController extends Controller
{

    /**
     * Lists all Grupos entities.
     *
     */
    public function indexAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();        
        $entities = $em->getRepository('SytemSGBundle:Grupos')->findAll();

        return $this->render('SytemSGBundle:Grupos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Grupos entity.
     *
     */
    public function createAction(Request $request)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       $campos = $request->request->all()["sytem_bundle_sgbundle_grupos"];
       $grupo=$campos['grupo'];

        $entity = new Grupos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if(($this->searchGrupo($grupo/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O grupo não existe no ldap ou não há conexão.');
                          return $this->render('SytemSGBundle:Grupos:new.html.twig', array(
                                'entity' => $entity,
                                'form'   => $form->createView(),
                          ));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();            
            $this->get('session')->getFlashBag()->set('message', 'Criado com sucesso.');
            return $this->redirect($this->generateUrl('grupos', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Grupos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Grupos entity.
     *
     * @param Grupos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Grupos $entity)
    {
        $form = $this->createForm(new GruposType(), $entity, array(
            'action' => $this->generateUrl('grupos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new Grupos entity.
     *
     */
    public function newAction()
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $entity = new Grupos();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Grupos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Grupos entity.
     *
     */
    public function showAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Grupos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Grupos:show.html.twig', array(
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

        $entity = $em->getRepository('SytemSGBundle:Grupos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);        
        return $this->render('SytemSGBundle:Grupos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Grupos entity.
    *
    * @param Grupos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Grupos $entity)
    {
        $form = $this->createForm(new GruposType(), $entity, array(
            'action' => $this->generateUrl('grupos_update', array('id' => $entity->getId())),
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

       $campos = $request->request->all()["sytem_bundle_sgbundle_grupos"];
       $grupo=$campos['grupo'];

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Grupos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            if(($this->searchGrupo($grupo/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message1','O grupo não existe no ldap ou não há conexão.');
                           return $this->render('SytemSGBundle:Grupos:edit.html.twig', array(
                                'entity'      => $entity,
                                'edit_form'   => $editForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                            ));
            }
            $em->flush();
            
            $this->get('session')->getFlashBag()->set('message', 'Atualizado com sucesso.');
            return $this->redirect($this->generateUrl('grupos_edit', array('id' => $id)));
        }
         
        return $this->render('SytemSGBundle:Grupos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Grupos entity.
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
            $entity = $em->getRepository('SytemSGBundle:Grupos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Grupos entity.');
            }

            $em->remove($entity);
            $em->flush();
           
            $this->get('session')->getFlashBag()->set('message', 'Removido com sucesso.');
        }

        return $this->redirect($this->generateUrl('grupos'));
    }

    /**
     * Creates a form to delete a Grupos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('grupos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

   
                        


    /*
     * Verifica se o cpf existe caso contrario verifica no ldap se sim o persiste como usuario
     *
    */

    public function searchGrupo($grupo/*Request $request*/){

       
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
                          $filter2 = "(gidNumber=".$grupo.")";
                                        $gindnumber=$grupo;
                                        $justthese2 = array("cn"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                                        $sr2 = ldap_search($ldapconn,"ou=Grupos,".$complemento/*"dc=ufop,dc=br"*/, $filter2, $justthese2);
                                        $entry = ldap_get_entries($ldapconn, $sr2);                                        
                          if($entry['count']>0){
                                
                             $namegrupo=$entry[0]['cn'][0];
                                      
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
   public function consultagrupoAction(Request $request){

     if ($request->getMethod()=='POST') {
        $grupo=$request->get('grupo');
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
      if($grupo!=''){ 
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
                          $filter2 = "(gidNumber=".$grupo.")";
                                        $gindnumber=$grupo;
                                        $justthese2 = array("cn"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                                        $sr2 = ldap_search($ldapconn,"ou=Grupos,".$complemento/*"dc=ufop,dc=br"*/, $filter2, $justthese2);
                                        $entry = ldap_get_entries($ldapconn, $sr2);                                        
                          if($entry['count']>0){
                                
                             $namegrupo=$entry[0]['cn'][0];
                             $response=' Nome do Grupo:'.$namegrupo;
                              
                                return new Response('<div  class="alert alert-default" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.$response.'</strong></div>',200);
                         
                      
                                                                   
                          }else{
                           // $this->get('session')->getFlashBag()->set('message2','O cpf não existe na base de dados do LDAP!!!');$user = $this->get('security.context')->getToken()->getUser();
                            return new Response('<div  class="alert alert-danger" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O grupo não existe na base de dados do LDAP!!!'.'</strong></div>', 200);
                          
                          }
                          
                    }
                    @ldap_close($ldapconn);
          
       } 
       return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não há conexão com o LDAP..'.'</strong></div>', 200);
            
       } 
    }
     return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não foram inseridos dados para busca.'.'</strong></div>', 200);
            

     }
}

    
}
