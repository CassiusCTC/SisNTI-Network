<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collection\ArrayCollection;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;
use Sytem\Bundle\SGBundle\Entity\Usuariosystem;
use Sytem\Bundle\SGBundle\Form\UsuariosystemType;
use Sytem\Bundle\SGBundle\Controller\DadosArquivosController;
use Symfony\Component\HttpFoundation\Response;


use Sytem\Bundle\SGBundle\Entity\Solicitacoes;
use Sytem\Bundle\SGBundle\Entity\SolicitacoesRepository;
use Sytem\Bundle\SGBundle\Form\SolicitacoesType;

/**
 * Solicitacoes controller.
 *
 */
class SolicitacoesController extends Controller
{

    /**
     * Lists all Solicitacoes entities.
     *
     */
    public function indexAction(Request $request)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

       $em = $this->getDoctrine()->getManager();  
       $usuarios=$em->getRepository('SytemSGBundle:Usuariosystem')->findAll();
       $array=$this->count_valauesResp();       
                $em    = $this->get('doctrine.orm.entity_manager');
                $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'não avaliado' order by a.dataSolicitacao";  
                $query = $em->createQuery($dql);
                $query=$query->getResult();
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate($query,$request->query->get('page', 1)/*page number*/,10/*limit per page*/);
                
                // parameters to template
                return $this->render('SytemSGBundle:Solicitacoes:index.html.twig', array('pagination' => $query,
                 'data'=> new \DateTime(),
                 'usuarios' => $usuarios,
                 'array' => $array,
                ));
    }

     
    
    /**
     * Creates a new Solicitacoes entity.
     *
     */
    public function createAction(Request $request)
    { 
        if (!$this->get('security.context')->isGranted('ROLE_USER') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
    if($request->getMethod() == 'POST') {     
       $user = $this->get('security.context')->getToken()->getUser(); 
       $em = $this->getDoctrine()->getManager();  
       $valuex=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($user->getId());
     
        date_default_timezone_set("UTC");
        $entity = new Solicitacoes();

        $entity->setResponsavel($valuex);        
        $entity->setAvaliacao('não avaliado');        
       

        $campos = $request->request->all()["sytem_bundle_sgbundle_solicitacoes"];
        $mac = $campos['mac'];
        $usuario=$request->get('usuario');
        $descricao=$request->get('descricaoequip');
        $justificativa=$request->get('justificativa');
        $tipo=$request->get('tipo');
        $laboratorio=$request->get('laboratorio');

      
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);  
        $arraycliente=array();
                $arraycliente[0]=$valuex->getNome();
                $arraycliente[1]=$valuex->getEmail();
                $arraycliente[2]='Solicitação em andamento.';
                $arraycliente[3]="Sua Solicitação do mac ".$mac."foi enviada com sucesso para avaliação, aguarde o email de resposta. ";
                $arraycliente[4]= $descricao;
                $arraycliente[5]= $tipo;
                $arraycliente[6]= $justificativa;
                $arraycliente[7]= $laboratorio; 
                $arraycliente[8]= $usuario;
             

        if ($form->isValid()) {
            $entity->setDataSolicitacao(new \DateTime);
            $email=$user->getEmail();
            if ($sock = @fsockopen('www.google.com.br', 80, $num, $error, 5)){
                 $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                 $mailer = \Swift_Mailer::newInstance($transport);
                 $message = \Swift_Message::newInstance()
                        ->setSubject('Confirmação de Mac.')
                        ->setFrom('suporteinformatica@icea.ufop.br')
                        ->setTo($email)
                        ->setBody($this->renderView('SytemSGBundle:Default:email.html.twig', array(
                                      'array'=>$arraycliente,
                                      'caminho'=>$this->caminhoimg(),
                                      'caminho2'=>$this->caminhoimg2(),
                                  )), 'text/html'
                                  );
                 //$this->renderView('SytemSGBundle:Default:email.html.twig', array('msg' => $msg,'array'=> $entity))
                 //$msg=Sua solicitação foi enviada com sucesso aguarde o email de resposta !!!;
                 $this->get('mailer')->send($message);
            }else{                   
                   $path1=$this->patchweb();
                   $filename='logemail.log';
                   $path_JOIN=join('/', array(trim(" ", '/'), trim($path1, '/')));
                   
                      $caminho=$path1.$filename;
                      join('/', array(trim(" ", '/'), trim($caminho, '/')));
                      $email_file= fopen($caminho, "a+b");
                      fputs($email_file,$email." Sua solicitação foi enviada com sucesso aguarde o email de resposta.\n");
                      fclose($email_file); 
                 
            }
        
       
            $em2 = $this->getDoctrine()->getManager();
            //Verifica se o mac já esta cadastrado
            if($em2->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($mac)){
                $this->get('session')->getFlashBag()->set('message','O Mac: '.$mac.' já esta cadastrado na base de dados principal.');
                return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
                'entity' => $entity,
                'user' => $user,
                'form'   => $form->createView(),
                ));
            }
             //Verifica se já existe uma solicitação p/ o mac
            if($em2->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($mac)){
                $this->get('session')->getFlashBag()->set('message','O Mac: '.$mac.' já existe como solicitação.');
                return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
                'entity' => $entity,
                'user' => $user,
                'form'   => $form->createView(),
                ));
            } 
            //Verifica se o conteudo é um cpf não válido
            if(!preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                          $this->get('session')->getFlashBag()->add('message','Este valor não é um cpf.');
                          //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                           'user' => $user,
                           ));
            }
            /*Verifica se o conteudo é um cpf  válido, se sim verifica 
              se ele existe na base de dados caso nao verifica do ldap 
              caso positivo persiste o mesmo atravez da função searchldap() .
            */
            if(preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                    if( !$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario)){
                     
                      if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.');
                          //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                           'user' => $user,
                           ));
                      }
                    }
            }
             $file=$form['file']->getData();
             $user1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario);                                         
             $entity->setUsuario($user1); 
             //Verifica se para dado usuário existe um termo se caso contrario retorna um usuario.
            if(is_null($file)){ 
              if($usuario!='100000000001'){
                if($this->validatermo($usuario,$entity->getMac())==0){
                                $this->get('session')->getFlashBag()->add('message','O usuário especificado ainda não enviou o termo de compromisso.');
                                //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                                return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
                                'entity' => $entity,
                                'user' => $user,          
                                'form'   => $form->createView(), 
                                 ));

                }
              }
             }  
                   
             if ($file) 
             { 
                          
                            $path=$this->patchupload();
                            $pdf = SolicitacoesType::processFile($file, $entity,$path);
                            $entity->setFile( $path.$pdf);
              }   
                    
                
               
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->set('message','Requisição feita com Sucesso.');
                return $this->redirect($this->generateUrl('useradmin'));
            
        }
      }
        return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
            'entity' => $entity,
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Solicitacoes entity.
     *
     * @param Solicitacoes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Solicitacoes $entity)
    {
        $form = $this->createForm(new SolicitacoesType(), $entity, array(
            'action' => $this->generateUrl('create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Enviar'));
      

        return $form;
    }
    

    /**
     * Displays a form to create a new Solicitacoes entity.
     *
     */
    public function newsolicitacaoAction()
    { 
       if (!$this->get('security.context')->isGranted('ROLE_USER') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }

      $user = $this->get('security.context')->getToken()->getUser();  
      $em = $this->getDoctrine()->getManager();
      $entity = new Solicitacoes();
      $valuex=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($user->getId());
      //print_r($valuex->getCpf());exit();
      $entity->setResponsavel($valuex);
        
        //$entity->setResponsavel($valuex->getCpf());
        $form   = $this->createCreateForm($entity);
        

        return $this->render('SytemSGBundle:Solicitacoes:newsolicitacao.html.twig', array(
            'entity' => $entity,
            'user'   => $user,
            'form'   => $form->createView(),
        ));
    }


    


    /**
     * Finds and displays a Laboratorios entity.
     *
     */
    public function showAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Laboratorios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Solicitacoes:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

  

    /**
     * Displays a form to edit an existing Solicitacoes entity.
     *
     */
    public function viewAction($id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Solicitacoes entity.');
        }

        //$editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Solicitacoes:view.html.twig', array(
            'entity'      => $entity,
            //'edit_form'   => $editForm->createView(),
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
    private function createEditForm(Solicitacoes $entity)
    {
        $form = $this->createForm(new SolicitacoesType(), $entity, array(
            'action' => $this->generateUrl('solicitacoes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar'));

        return $form;
    }
   
    /**
     * Deletes a DadosArquivos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
     $session = $this->getRequest()->getSession();
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);    

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Solicitacoes entity.');
            }
           
            $em->remove($entity);
            $em->flush();
           
            
            
        }

        return $this->redirect($this->generateUrl('solicitacoes'));
      //}
      //}

          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 

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
            ->setAction($this->generateUrl('solicitacoes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'OK'))
            ->getForm()
        ;
    }

    

    private function Logger_solicitacoes($avaliador,$mensagem,$hostname,$mac,$tipo,$email,$celular,$responsavel,$data_solic){
        date_default_timezone_set("UTC");
        $data = date("d‐m‐y");
        $hora = date("H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $path=$this->patchweb();
        $aval=null;
        $name_avaliador=explode(" ", $avaliador);
        if(count($name_avaliador)>=2){
          $aval=$name_avaliador[0]."_".$name_avaliador[1];
        }else{
          $aval=$name_avaliador[0];
        }
        $filename='logsolicit.log';
        $path_JOIN=join('/', array(trim(" ", '/'), trim($path, '/')));
       
            $texto = "[".$data."]-[".$hora."][".$ip."]".$mensagem."Hostname: ".$hostname."|Mac Adress: ".$mac."|Tipo: ".$tipo."|Email: ".$email."|Celular: ".$celular."|Solicitante: ".$responsavel."|Data solicitação: ".$data_solic."\n";
            $caminho=$path.$filename;
            join('/', array(trim(" ", '/'), trim($caminho, '/')));
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular); 
       
    }
     public function GetRouters($value){
      $result=null;
      $file = $this->container->get('file_locator')->locate('@SytemSGBundle/Resources/file_routers/file_routers.txt');
       
      if(file_exists($file)){
        $lines = file ($file);
        //$this->get('session')->getFlashBag()->set('messageExecut', 'Arquivo encontrado!!!');
        $result= $lines[$value];
        //echo "aqui";

      }else{
        $this->get('session')->getFlashBag()->set('messageExecut', 'path nao existe!!!');
        //echo 'path nao existe';
        $result= null;

      }
      //exit();
      return $result;
    }
    
    public function indeferirAction(Request $request, $id)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $user = $this->get('security.context')->getToken()->getUser(); 

                    if ($request->getMethod()=='POST') {
                       $em = $this->getDoctrine()->getManager();
                       $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);
                       $usuarioemail= $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getResponsavel());
                       $mac=$entity->getMac();
                       if (!$entity && !$usuarioemail) {
                             //$this->get('session')->getFlashBag()->set('message','Não foi possivel excluir registro!!');
                            throw $this->createNotFoundException('Unable to find Solicitaçães or Usuariosystem  entity.');
                       }
                       $form_textarea_mensagem=$request->get('mensagem');
                       $arraycliente=array();
                       $arraycliente[0]=$usuarioemail->getNome();
                       $arraycliente[1]=$usuarioemail->getEmail();
                       $arraycliente[2]='Sua solicitação foi indeferida.';
                       $arraycliente[3]="Sua Solicitação do mac ".$mac."foi enviada com sucesso para avaliação, aguarde o email de resposta. ";
                       $arraycliente[4]='Motivo: '.$form_textarea_mensagem.'.';
                       $arraycliente[5]= 'Dados do usuario| Nome: '.$usuarioemail->getNome().'| Email: '.$usuarioemail->getEmail().' |Mac: '.$entity->getMac();
                       $arraycliente[6]= '| Justificativa: '.$entity->getJustificativa();
                  if(!strcmp($usuarioemail->getEmail(),'admin@admin')){
                      if ($sock = @fsockopen('www.google.com.br', 80, $num, $error, 5)){ 
                                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                                $mailer = \Swift_Mailer::newInstance($transport);   
                                $message = \Swift_Message::newInstance()
                                ->setSubject('Confirmação de Mac Indeferido!!!')
                                ->setFrom('suporteinformatica@icea.ufop.br')
                                ->setTo($usuarioemail->getEmail())
                                ->setBody($this->renderView('SytemSGBundle:Default:email.html.twig', array(
                                      'array'=>$arraycliente,
                                      'caminho'=>$this->caminhoimg(),
                                      'caminho2'=>$this->caminhoimg2(),
                                  )), 'text/html'
                                  );
                                $this->get('mailer')->send($message);

                        }else{
                    
                           $path1=$this->patchweb();
                           $filename='logemail.log';
                           $path_JOIN=join('/', array(trim(" ", '/'), trim($path1, '/')));
                          
                              $caminho=$path1.$filename;
                              join('/', array(trim(" ", '/'), trim($caminho, '/')));
                              $email_file= fopen($caminho, "a+b");
                              fputs($email_file, $usuarioemail->getEmail()."|Sua solicitação foi indeferida. Motivo:".$form_textarea_mensagem.". Dados do usuario|Nome: ". $usuarioemail->getNome()."|Email: ". $usuarioemail->getEmail().".\n");
                              fclose($email_file);  
                         }
                      }
                       
                        $mensagem="[Status: Indeferido ]-->Motivo: ".$form_textarea_mensagem;
                        
                        //$em->remove($entity);
                        $entity->setAvaliacao('INDEFERIDO');
                        $entity->setMotivo($form_textarea_mensagem);
                        $entity->setRespaval($user->getNome());
                        $em->flush();
                        $this->Logger_solicitacoes($user->getNome(),$mensagem,'hostlocal',$entity->getMac(),$entity->getTipo(), $usuarioemail->getEmail(), $usuarioemail->getTelefone(), $usuarioemail->getNome(),$entity->getDataSolicitacao()->format('d/m/Y'));
                        $this->get('session')->getFlashBag()->set('message','Indeferido com sucesso.');
                
                    }

                    return $this->redirect($this->generateUrl('solicitacoes'));
         // }
      //}

          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 

    }




   public function reenviaemail(){
       if($sock = @fsockopen('www.google.com.br', 80, $num, $error, 5)){
            $path1=$this->patchweb();
            join('/', array(trim(" ", '/'), trim($path1, '/')));
            $path_JOIN = $path1."logemail.log";
            join('/', array(trim(" ", '/'), trim($path_JOIN, '/')));
            if(file_exists($path_JOIN)){
                $file=file($path_JOIN);
                $tamanho=count($file);   
                if($tamanho>0){
                    $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                    $mailer = \Swift_Mailer::newInstance($transport);       
                    foreach($file as $key => $valuefile){ 
                         
                          $string= array_shift($file);
                          $email= explode(" ",$string);
                          $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                          $mailer = \Swift_Mailer::newInstance($transport);   
                          $message = \Swift_Message::newInstance()
                                    ->setSubject('Confirmação')
                                    ->setFrom('suporteinformatica@icea.ufop.br')
                                    ->setTo($email[0])
                                    ->setBody($valuefile)
                           ;
                         $this->get('mailer')->send($message);
                    }
                    
                    
                        $array_file=implode(" ", $file);
                        file_put_contents($path_JOIN, $array_file);
               }   
            }else{

            }
        }
 }

 /*
    *  Retorna ao administrador as solicitações deferidas     
    */

 public function search_solicitacoesAction(Request $request){
       
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser(); 
                      
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'DEFERIDO' order by a.dataSolicitacao  ";  
        $query = $em->createQuery($dql);
        $query = $query->getResult();
        // parameters to template
        return $this->render('SytemSGBundle:Solicitacoes:search_solicitacoes.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Solicitações Deferidas",
         'var'=> 0,
        
        ));

    
    }
    
    /*
    *  Retorna ao administrador as solicitações indeferidas     
    */
    public function search_indeferidosAction(Request $request){
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser(); 

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'INDEFERIDO' order by a.dataSolicitacao  ";  
        $query = $em->createQuery($dql);
        $query = $query->getResult();
        // parameters to template
        return $this->render('SytemSGBundle:Solicitacoes:search_solicitacoes.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Solicitações Indeferidas",
         'var'=> 2,
        
        ));

    
    }
    
    /*
    *  Retorna ao administrador as notivicações referentes
    *  a atualização de dispositivos gerenciados por um dado responsavel
    */
     public function search_notificacoesAction(Request $request){
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser(); 

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'ATUALIZADO' order by a.dataSolicitacao  ";  
        $query = $em->createQuery($dql);
        $query = $query->getResult();

       
        return $this->render('SytemSGBundle:Solicitacoes:search_solicitacoes.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Notificações de Atualizações.",
         'var'=> 1,
        
        ));

    
    }

    /*
    *  Retorna ao administrador as notivicações referentes
    *  a exclusão de dispositivos gerenciados por um dado responsavel
    */
    public function search_apagarAction(Request $request){
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
       //$user = $this->get('security.context')->getToken()->getUser(); 

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'APAGAR' order by a.dataSolicitacao  ";  
        $query = $em->createQuery($dql);
        $query = $query->getResult();
        // parameters to template
        return $this->render('SytemSGBundle:Solicitacoes:search_solicitacoes.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Notificações de Exclusão.",
         'var'=> 3,
        
        ));

    
    }
    /*
       Exibe os macs que o usuario é responsavel.
    */
    public function search_userAction(Request $request){
       
       if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $user = $this->get('security.context')->getToken()->getUser(); 
        $nome=$user->getId(); 
       
        $em1 = $this->getDoctrine()->getManager();
        $id = $em1->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($nome);
        $value= $id->getId();             
        
        
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.responsavel = '$value' order by a.mac  ";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();

        $parametro='Macs gerenciados por: '.$user->getNome();

        $idsol = $em1->getRepository('SytemSGBundle:Solicitacoes')->findBy(array('responsavel'=>$value));
        if($idsol){
          $array=array();
          $array1=array();
          $i=0;
          foreach ($idsol as $key => $value) {
            if($value->getAvaliacao()=='APAGAR'){
              $array[$i]=$value->getMac();
              $i++;
            }
          }
          foreach ($idsol as $key => $value) {
            if($value->getAvaliacao()=='ATUALIZADO'){
              $array1[$i]=$value->getMac();
              $i++;
            }
          }

         
          
        }
        // parameters to template
        return $this->render('SytemSGBundle:Solicitacoes:search_user.html.twig', array('pagination' =>  $query,
         'data'=> new \DateTime(),
         'status'=> $parametro,
         'flag'=> $array,      
         'flag1'=> $array1,    
        ));

    
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
        $pathjoin=$pathaux.'web/systemfiles/log/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }
/* Esta função faz a busca no sentido de verificar primeiro se o usuário existe no sistema,
   caso contrário verifica no ldap, se sim ele persiste altomaticamente o usuário no sistema.

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


    
    //Valida se exite um termo para o usuario caso não ele retorna 0 e solicita ao usuario
    public function validatermo($search,$macs){
     $em2 = $this->getDoctrine()->getManager();
     $j=0; $i=0;
     $x=$em2->getRepository('SytemSGBundle:Usuariosystem')->findOneBy(array('cpf'=>$search));
     $mac=$em2->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($macs);
     $mac2=$em2->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($macs);
     $cpf=$em2->getRepository('SytemSGBundle:DadosArquivos')->findByUsuario($x->getId());
     $cpf2=$em2->getRepository('SytemSGBundle:Solicitacoes')->findByUsuario($x->getId());
     //echo $cpf[0]->getResponsavel();echo $cpf2[0]->getResponsavel();
     
       if($cpf){
        foreach ($cpf as $key => $value) {
          if(!is_null($value->getFile()))
                $i++; 
          }
        }
        
       if($cpf2){
         foreach ($cpf2 as $key => $value2) {
          if(!is_null($value2->getFile())){
                  $j++;     
          }
         
         }
       }
       
     if($i>0 || $j>0){
      return 1;
     }else if($i==0 && $j==0){
      return 0;
     }



  }
//Retorna a path onde os uloads seram gravados...
public function patchupload(){
        $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/systemfiles/uploadsTC/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }



public function solicitaExclusaoAction($mac){
    if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
   
   $em = $this->getDoctrine()->getManager();
   $macexclusao=$em->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($mac);
   
   if($macexclusao && $macexclusao->getAvaliacao()=='DEFERIDO'){
    $macexclusao->setAvaliacao('APAGAR');
     $em->flush();
   }else if(!$macexclusao){
    $mac1=$em->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($mac);
    $user = $this->get('security.context')->getToken()->getUser();
             date_default_timezone_set("UTC");
    $solicit= new Solicitacoes();  
    /*2*/$solicit->setMac($mac1->getMac()); 
    /*3*/$solicit->setTipo($mac1->getTipo());
    /*4*/$solicit->setAvaliacao('APAGAR');            
   
    /*8*/$solicit->setDataSolicitacao(new \DateTime);
    /*9*/$solicit->setJustificativa('Remoção do dispositivo.');
    /*10*/$solicit->setUsuario($mac1->getUsuario());
          $solicit->setResponsavel($mac1->getResponsavel());
             /*11*/$solicit->setDescricaoequip('n/d');             
             /*12*/$solicit->setLaboratorio($mac1->getLaboratorio());            
             /*13*/$solicit->setRespaval($user->getNome()." /".$user->getEmail());    
             /*14*/$solicit->setMotivo('Exclusão do dispositivo');           
    
    $em->persist($solicit);
    $em->flush();

   }
   $this->get('session')->getFlashBag()->set('message','A solicitação para exclusão do dispositivo foi efetuada com sucesso aguarde a resposta da NTI.');
          
  return $this->redirect($this->generateUrl('search_user'));
             

}

public function count_valauesResp(){
  $em = $this->getDoctrine()->getManager();
  $m=$em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
  $array=array();
  $i=0;
  foreach ($m as $key => $value) {
      $array[$i]=(string)$value->getResponsavel();
      $i++;    
  }
  $result=array_count_values($array);
  //print_r($result);exit;
  return  $result;

}

/*
    *  Retorna ao administrador as notivicações referentes as solicitações
    *  dispositivos gerenciados por um dado responsavel
    */
    public function search_usernovanotificacaoAction(Request $request){
       if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $user = $this->get('security.context')->getToken()->getUser(); 
        $nome=$user->getId(); 
       
        $em1 = $this->getDoctrine()->getManager();
        $id = $em1->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($nome);
        $value= $id->getId();

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:Solicitacoes a where a.avaliacao = 'não avaliado' and a.responsavel='$value' order by a.dataSolicitacao  ";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();

        
        return $this->render('SytemSGBundle:Solicitacoes:search_usernovanotificacao.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Notificações de solicitações em andamento.",
        ));

    
    }

    /*Esta action procura faz a busca se o usuario existe no ldap para fazer a auxiliar
     * o usuário na verificação do cpf no momento do cadastro
     */

    public function searchCpfuserAction(Request $request){
    
    //if($request‐>isXmlHttpRequest()){
         
         $cpf = $request->get('usuario');
         $id = $request->get('id');
       // $cpf=$request->get('usuario');
       // $id=$request->get('id');
        //echo $cpf;exit;
      $em = $this->getDoctrine()->getManager();
      if($cpf!=''){ 
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
                          $filter = "(uid=".$cpf.")"; // this command requires some filter
                          $justthese = array("uid", "cn", "sn", "mail", "gidNumber","telephonenumber"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                          $sr = ldap_search($ldapconn,$complemento/*"dc=ufop,dc=br"*/, $filter, $justthese);
                          $entry = ldap_get_entries($ldapconn, $sr);
                          if($entry['count']>0){
                              $cpf=$entry[0]['uid'][0];
                              $nome=$entry[0]['cn'][0] . " " . $entry[0]['sn'][0];
                              $telefone=$entry[0]['telephonenumber'][0];
                              $email=$entry[0]['mail'][0];
                              $response=' Nome: '.$nome." / Cpf: ".$cpf.'</br>'.'Email:'.$email." / Telefone: ".$telefone;
                              return new Response('<div  class="alert alert-default" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.$response.'</strong></div>',200);
                          }else{
                            
                            return new Response('<div  class="alert alert-danger" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O cpf não existe na base de dados do LDAP!!!'.'</strong></div>', 200);
                          
                          }
                          
                    }
                    @ldap_close($ldapconn);
          
       } 
         return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não há conexão com o LDAP..'.'</strong></div>', 200);
              
     }
          
      }else{  
      
        return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O não foram informados dados para a busca!!!'.'</strong></div>', 200);
           
        }
     
       
        

    }
    /*
    * Gera o caminho real das imagens
   */
    public function caminhoImg(){
      $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/bundles/sytemsg/images/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }
    public function caminhoImg2(){
      $path=__DIR__;
        $path2=explode('/', $path);
        $pathaux=null;
        foreach ($path2 as $key => $value) {
           if($value!='src'&& $value!='Sytem' && $value!='Bundle' && $value!='SGBundle' && $value!='Controller'){
             $pathaux.=$value.'/';

           }
        }
        $pathjoin=$pathaux.'web/bundles/sytemsg/imgemail/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }

    
}
