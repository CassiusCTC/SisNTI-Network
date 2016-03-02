<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collection\ArrayCollection;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Entity\DadosArquivosRepository;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;

use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;

use Sytem\Bundle\SGBundle\Entity\Solicitacoes;
use Sytem\Bundle\SGBundle\Form\SolicitacoesType;

use Sytem\Bundle\SGBundle\Entity\Dhpconf;
use Sytem\Bundle\SGBundle\Form\DhpconfType;

use Sytem\Bundle\SGBundle\Entity\Usuariosystem;
use Sytem\Bundle\SGBundle\Form\UsuariosystemType;

use Symfony\Component\HttpFoundation\File\UploadedFile;


//use Symfony\Component\DependencyInjection\ContainerBuilder;


/**
 * DadosArquivos controller. Gerencia todos os dipsositivos da rede
 *
 */
class DadosArquivosController extends Controller
{
    /**
     * Lists all DadosArquivos entities.
     *
     */
    public function indexAction(Request $request)
    {
        
       $session = $this->getRequest()->getSession();
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }      
              $percento=array();
              $percento =$this->PorcentagemGrafico(); 
               
              $em = $this->getDoctrine()->getManager();
             
               $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
               return $this->render('SytemSGBundle:DadosArquivos:index.html.twig', array('pagination' => $entity,
               'data'=> new \DateTime(),
               'porcentagem'=>$percento,
               'value'=> 0,
               
              ));
           
    }

  
    /**
     * Creates a new DadosArquivos entity.
     *
     */
    public function createAction(Request $request)
    {
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
                $user = $this->get('security.context')->getToken()->getUser();
                
              if($request->getMethod() == 'POST') {               
                $em = $this->getDoctrine()->getManager();               
                $campos = $request->request->all()["sytem_bundle_sgbundle_dadosarquivos"];
                
                $ip = $campos['ip'];                
                $resp =$request->get('responsavel');// $campos['responsavel'];
                $usuario=$request->get('usuario'); //$campos['usuario'];
                //echo $resp;echo$usuario;exit();
                date_default_timezone_set("UTC");
                $entity = new DadosArquivos();
                $entity->setDataCadastro(new \DateTime);
                $entity->setDataDhcplog(new \DateTime);
                $entity->setStatusdhcp('disable');
                               
                
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
                          $this->get('session')->getFlashBag()->add('message','O Ip: '.$ip.' não pertence a faixa de ips cadastrados.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                   }
                  if($em2->getRepository('SytemSGBundle:Ipsexcecoes')->findOneByIpexcecao($ip)){
                          $this->get('session')->getFlashBag()->add('message','O Ip: '.$ip.' não pode ser usado pois faz parte dos ips exceções.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                   }
                   if(!preg_match('/^([0-9]{11})$/',$resp, $matches )||!preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                          $this->get('session')->getFlashBag()->add('message','Este valor não é um cpf!!');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                   }
                   if(preg_match('/^([0-9]{11})$/',$resp, $matches ) && preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                    if(!$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp)|| !$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario)){
                     if(($this->searchldap($resp/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                      }
                      if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                          'entity' => $entity,
                          'form'   => $form->createView(),
                          'json' => $json,
                           ));
                      }
                    }
                  }
                  $file=$form['filemove']->getData();
                  if(is_null($file)){ 
                            if($usuario!='90000000009'||$usuario!='10000000001'||$usuario!='11111111111'){   //echo $this->validatermo($usuario); exit();
                                  if($this->validatermo($usuario,$entity->getMac())==0){
                                                  $this->get('session')->getFlashBag()->add('message','O usuário especificado ainda não enviou o termo de compromisso.');
                                                  //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                                                  return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                                                  'entity' => $entity,
                                                  'form'   => $form->createView(),
                                                  'json' => $json, ));

                                  }
                            } 
                  }  
                                     
                   if ($file) 
                   { 
                                
                                  $path=$this->patchupload();
                                  $pdf = DadosArquivosType::processFileaux($file, $usuario,$path);
                                  $entity->setFile( $path.$pdf);
                    }     
                    $resp1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp);
                    $user1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario);
                 
                    $entity->setResponsavel($resp1);                
                    $entity->setUsuario($user1);     
                   
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('message',"1 - Item criado com sucesso.");
                    $results2=array();
                    $results=array();
                    $results2=$this->ExecDHCPARPShells();
                    $this->get('session')->getFlashBag()->add('message',$results2[0]);
                    $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
                    $results=$this->ExecShells();
                    $this->get('session')->getFlashBag()->add('message',$results[0]);
                    $this->get('session')->getFlashBag()->add('message',$results[1]);
                    $this->get('session')->getFlashBag()->add('message',$results[2]);
                    $this->get('session')->getFlashBag()->add('message',$results[3]);

                    
                    $this->Returnlognew($user,$entity);
                    return $this->redirect($this->generateUrl('dadosarquivos'));
                  
                }
              }
                $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                    'json' => $json,
                ));
    }

    /**
     * Creates a new DadosArquivos entity.
     *
     */
    public function create2Action(Request $request)
    { 
      $session = $this->getRequest()->getSession();
      if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
      }
                $user = $this->get('security.context')->getToken()->getUser();
                
                date_default_timezone_set("UTC");
                
                $campos2 = $request->request->all()["sytem_bundle_sgbundle_dadosarquivos"];
                $ip = $campos2['ip'];


                $mac = $campos2['mac'];
                //$hostname = $campos2['hostname'];
                $tipo = $campos2['tipo'];
                $patrimonioLocal = $campos2['patrimonioLocal'];                
                //$responsavel = $campos2['responsavel'];
                              
                //$usuario = $campos2['usuario'];
                $descricao = $campos2['descricao'];
                //$namefile=$request->get('filename');
                
            
                $em = $this->getDoctrine()->getManager();
                $laboratorio=$request->request->get('laboratorio');
                $entity2 = $em->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($mac);
                $tipox = $em->getRepository('SytemSGBundle:Tipos')->findOneByDescricao($tipo);
                $resp = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity2->getResponsavel());
                $usercomum = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity2->getUsuario());
                $array=$em->getRepository('SytemSGBundle:DadosArquivos')->findByResponsavel($resp->getId());
                $total=count($array);

                
                $em2 = $this->getDoctrine()->getManager();
                $entity3 = $em->getRepository('SytemSGBundle:Laboratorios')->findOneByNomeLaboratorio($laboratorio);
                $pathx=$this->patchupload();$pdfx=basename($entity2->getFile());
                // echo $pathx.$pdfx;exit;
                //$path=$this->patchupload();
                //$pdf = DadosArquivosType::processFile($file=null, $entity,$path);
               
                $entity = new DadosArquivos(); 
                $entity->setMac($mac);                
                $entity->setTipo($tipox);
                $entity->setPatrimonioLocal($patrimonioLocal);
                $entity->setLaboratorio($entity3);
                $entity->setResponsavel($entity2->getResponsavel());
                $entity->setUsuario($entity2->getUsuario());
                $entity->setFile($pathx.$pdfx);
                $entity->setDataCadastro(new \DateTime);
                $entity->setDataDhcplog(new \DateTime);
                $entity->setStatusdhcp('disable');
                $form = $this->create2CreateForm($entity);
                $form->handleRequest($request);
                
                $arraycliente=array();
                $arraycliente[0]=$resp->getNome();
                $arraycliente[1]=$resp->getEmail();
                $arraycliente[2]='Mac deferido.';
                $arraycliente[3]="Sua Solicitação do mac ".$mac." foi Deferido.";
                $arraycliente[4]= $descricao;
                $arraycliente[5]= $tipo;
                $arraycliente[6]= $ip;
                $arraycliente[7]= $laboratorio;
                $arraycliente[8]= $usercomum;
                $arraycliente[9]= $patrimonioLocal;
                
                $email = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity2->getResponsavel());
                
                 if (!$email) {
                    throw $this->createNotFoundException('Unable to find Usuariosystem entity.');
                }
                $emailuser=$email->getEmail();

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
                          return $this->render('SytemSGBundle:DadosArquivos:newdeferido.html.twig', array(
                          'entity' => $entity,                  
                          'entity_value' => $entity2,
                          'responsavel' => $resp ,
                          'usuario' => $usercomum,
                          'entity_laboratorio'=>$entity3,                  
                          'form_r'   => $form->createView(),
                          'total'=>$total,
                          'json' => $json,
                           ));
                   } 
                   if($em2->getRepository('SytemSGBundle:Ipsexcecoes')->findOneByIpexcecao($ip)){
                          $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' não pode ser usado pois já esta reservado para dispositivos como impressoras!!');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:newdeferido.html.twig', array(
                          'entity' => $entity,                  
                          'entity_value' => $entity2,
                          'responsavel' => $resp ,
                          'usuario' => $usercomum,
                          'entity_laboratorio'=>$entity3,                  
                          'form_r'   => $form->createView(),
                          'total'=>$total,
                          'json' => $json,
                           ));
                   } 
                   if($entity2->getResponsavel()!='10010010000' && $entity2->getResponsavel()!='10000000001' && $entity2->getResponsavel()!='11111111111'&& $entity2->getResponsavel()!='90000000009'){
                       if ($sock = @fsockopen('www.google.com.br', 80, $num, $error, 5)){
                         
                         $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                         $mailer = \Swift_Mailer::newInstance($transport);
                         $message = \Swift_Message::newInstance('Wonderful Subject')
                                  ->setSubject('Confirmação de solicitação de Mac Deferido.')
                                  ->setFrom('suporteinformatica@icea.ufop.br')
                                  ->setTo($emailuser)
                                  ->setBody($this->renderView('SytemSGBundle:Default:email.html.twig', array(
                                      'array'=>$arraycliente,
                                      'caminho'=>$this->caminhoimg(),
                                      'caminho2'=>$this->caminhoimg2(),
                                  )), 'text/html'
                                  );
                          $this->get('mailer')->send($message);
                          $respx='Enviado com sucesso.';
                       }else{
                           //$path = $this->container->get('file_locator')->locate('@SytemSGBundle/Resources/file_emails/file_save_email.txt');
                           $path1=$this->patchweblog();
                           $path_JOIN=join('/', array(trim(" ", '/'), trim($path1, '/')));
                           $filename='logemail.log';
                           
                               $caminho=$path1.$filename;
                               join('/', array(trim(" ", '/'), trim($caminho, '/')));
                               //chmod($caminho, 1777);
                               $email_file= fopen($caminho, "a+b");
                               fputs($email_file,$emailuser."|Sua Solicitação de Mac foi Deferida com sucesso! Segue os dados do serviço solicitado:"."\n");
                               fclose($email_file);
                           $respx='O e-mail não foi enviado com sucesso.';

                       }
                    }

                    //$resp1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp);
                    //$user1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usercomum);
                 
                    $entity->setResponsavel($resp);                
                    $entity->setUsuario($usercomum);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();
                    
                    $results2=array();
                    $results=array();
                    $this->get('session')->getFlashBag()->add('message','1 - Deferido com sucesso.');
                                      
                    $results2=$this->ExecDHCPARPShells();
                    $this->get('session')->getFlashBag()->add('message',$results2[0]);
                    $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
                    $results=$this->ExecShells();
                    $this->get('session')->getFlashBag()->add('message',$results[0]);
                    $this->get('session')->getFlashBag()->add('message',$results[1]);
                    $this->get('session')->getFlashBag()->add('message',$results[2]);
                    $this->get('session')->getFlashBag()->add('message',$results[3]); 
                    $this->get('session')->getFlashBag()->add('message','8 - E-mail:'.$respx); 

                    $this->Logger_solicitacoes("Status: Deferido",'hostname',$entity->getMac(),$entity->getTipo(),$emailuser,$email->getTelefone(),$email->getNome(),$entity2->getDataSolicitacao()->format('d/m/Y'));
                    $this->SolicitacaoDeferida($entity2->getId());

                   
                    return $this->redirect($this->generateUrl('solicitacoes'));
                  
                }
                $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                       
                return $this->render('SytemSGBundle:DadosArquivos:newdeferido.html.twig', array(
                    'entity' => $entity,                  
                    'entity_value' => $entity2,
                    'responsavel' => $resp ,
                    'usuario' => $usercomum,
                    'entity_laboratorio'=>$entity3,                  
                    'form_r'   => $form->createView(),
                    'total'=>$total,
                    'json' => $json,
                ));

    }
    //Chama as funções de log
    public function ReturnUser($user,$entity){
         
        $this->Logger_delete($entity->getIp(),$entity->getMac(),$user->getEmail(),$user->getNome());
    }
    //Chama as funções de log
    public function ReturnUser2($user,$entity){
        
        $this->Logger_atualizacao($entity->getIp(),$entity->getMac(),$user->getEmail(),$user->getNome());
    }
    //Chama as funções de log
     public function Returnlognew($user,$entity){
        
        $this->Logger_new($entity->getIp(),$entity->getMac(),$user->getEmail(),$user->getNome());
    }
    
    //funções de log
    public function Logger_solicitacoes($mensagem,$hostname,$mac,$tipo,$email,$celular,$responsavel,$data_solic){
        date_default_timezone_set("UTC");
        $data = date("d‐m‐y");
        $hora = date("H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $path=$this->patchweblog();        
        $aval=null;
        $name_avaliador=explode(" ", $responsavel);
        if(count($name_avaliador)>=2){
          $aval=$name_avaliador[0]."_".$name_avaliador[1];
        }else{
          $aval=$name_avaliador[0];
        }  
        $filename='logs.log';
        join('/', array(trim(" ", '/'), trim($path, '/')));

            $texto = "[".$data."]-[".$hora."][".$ip."]--> ".$mensagem." | Hostname: ".$hostname." | Mac Adress: ".$mac." | Tipo: ".$tipo." | Email: ".$email." | Celular: ".$celular." | Solicitante: ".$responsavel." | Data solicitação: ".$data_solic."\n";
            $caminho=$path.$filename;
            join('/', array(trim(" ", '/'), trim($caminho, '/')));
            //chmod($caminho, 1777);
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular);
        
        
    }
     //funções de log
    public function Logger_atualizacao($ipM,$mac,$email,$responsavel){
        date_default_timezone_set("UTC");
        $data = date("d‐m‐y");
        $hora = date("H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $path=$this->patchweblog();
        $aval=null;
        $name_avaliador=explode(" ", $responsavel);
        if(count($name_avaliador)>=2){
          $aval=$name_avaliador[0]."_".$name_avaliador[1];
        }else{
          $aval=$name_avaliador[0];
        }  
        $filename='logs.log';
        //$path_aux=$path.$filename;
        join('/', array(trim(" ", '/'), trim($path, '/')));      
        
        /*if (!file_exists($path."log")) {// testo se a pasta existe
            mkdir($path."log", 0777);            
            $texto = "[".$data."]-[".$hora."][".$ip."]--> O registro do IP:".$ipM."e Mac Adress: ".$mac.". Foi ATUALIZADO pelo usuário: ".$responsavel." | Username: ".$email.". \n";
            $caminho=$path."log/".$filename;
           //chmod($caminho, 1777);
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular);
                
        } else {*/
            $texto = "[".$data."]-[".$hora."][".$ip."]--> O registro do IP:".$ipM."e Mac Adress: ".$mac.". Foi ATUALIZADO pelo usuário: ".$responsavel." | Username: ".$email.". \n";
            $caminho=$path.$filename;
            join('/', array(trim(" ", '/'), trim($caminho, '/')));  
           // chmod($caminho, 1777);
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular);
        //}//Texto a ser impresso no log:
        


    }
      //funções de log
     public function Logger_new($ipM,$mac,$email,$responsavel){
        date_default_timezone_set("UTC");
        $data = date("d‐m‐y");
        $hora = date("H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $path=$this->patchweblog();
        $aval=null;
        $name_avaliador=explode(" ", $responsavel);
        if(count($name_avaliador)>=2){
          $aval=$name_avaliador[0]."_".$name_avaliador[1];
        }else{
          $aval=$name_avaliador[0];
        }  
        $filename='logs.log';
        //$path_aux=$path.$filename;
        join('/', array(trim(" ", '/'), trim($path, '/')));      
        
        
            $texto = "[".$data."]-[".$hora."][".$ip."]--> O registro do IP:".$ipM."e Mac Adress: ".$mac.". Foi Criado pelo usuário: ".$responsavel." | Username: ".$email.". \n";
            $caminho=$path.$filename;
            join('/', array(trim(" ", '/'), trim($caminho, '/')));
            //chmod($caminho, 1777);
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular);
    }
     //funções de log
    public function Logger_delete($ipM,$mac,$email,$responsavel){
        date_default_timezone_set("UTC");
        $data = date("d‐m‐y");
        $hora = date("H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $path=$this->patchweblog();
        $aval=null;
        $name_avaliador=explode(" ", $responsavel);
        if(count($name_avaliador)>=2){
          $aval=$name_avaliador[0]."_".$name_avaliador[1];
        }else{
          $aval=$name_avaliador[0];
        }  
        $filename='logs.log';
        //$path_aux=$path. $filename;
       join('/', array(trim(" ", '/'), trim($path, '/')));  

       
            $texto = "[".$data."]-[".$hora."][".$ip."]--> O registro do IP:".$ipM."e Mac Adress: ".$mac.". Foi REMOVIDO pelo usuário: ".$responsavel." | Username: ".$email.".\n";
            $caminho=$path.$filename;
            join('/', array(trim(" ", '/'), trim($caminho, '/')));  
            //chmod($caminho, 1777);
            $manipular = fopen($caminho, "a+b");
            fputs($manipular, $texto);
            fclose($manipular);
        
    }

    /**
     * Creates a form to create a DadosArquivos entity.
     *
     * @param DadosArquivos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DadosArquivos $entity)
    {
        $form = $this->createForm(new DadosArquivosType(), $entity, array(
            'action' => $this->generateUrl('dadosarquivos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

   /**
     * Creates a form to create2 a DadosArquivos entity.
     *
     * @param DadosArquivos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function create2CreateForm(DadosArquivos $entity)
    {
        $form = $this->createForm( new DadosArquivosType(),$entity, array(
            'action' => $this->generateUrl('dadosarquivos_create2'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Criar'));

        return $form;
    }

    /**
     * Displays a form to create a new DadosArquivos entity.
     *
     */
    public function newAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
                $user = $this->get('security.context')->getToken()->getUser();
                $entity = new DadosArquivos();                          
                
                $form = $this->createCreateForm($entity);
                 $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          
                return $this->render('SytemSGBundle:DadosArquivos:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                    'json'   => $json,                  
                ));

    }

    /*
     * Retorna os ips livres para o autocomplete dos ips nas views. 
    */
    public function ipsValidos(){
        $em = $this->getDoctrine()->getManager();
        $entities2 = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
        $excecoesip= $em->getRepository('SytemSGBundle:Ipsexcecoes')->findAll();
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
             $listabd[$cont]= trim($value->getIp());
             $cont++;
        }
        $listaexcecoes=array();
        $cont4=0;
        foreach ($excecoesip as $key=>$value) { 
             $listaexcecoes[$cont4]= $value->getIpexcecao();            
             $cont4++;
        }
        
        $ipx=array();
        $xx=0;

        $ips=array_diff($listaIP,$listabd);   
        $ipx=array_diff($ips,$listabd);

       

        $ip_livres=array();
        $ipsx=array_diff($ipx ,$listaexcecoes);   
           
        foreach ($ipsx as $key => $value) {
                  $ip_livres[$cont3]= $value;
                  $cont3++;                  
        }

        return  $ip_livres;
    }


    /**
     * Displays a form to create a new DadosArquivos entity.
     *
     */
    public function newdeferidoAction($id)
    { 
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
          $em = $this->getDoctrine()->getManager();
          $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);
          $resp = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getResponsavel());
          $usuario = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getUsuario());
          $array=$em->getRepository('SytemSGBundle:DadosArquivos')->findByResponsavel($usuario->getId());
          $total=count($array);
          
          $em2 = $this->getDoctrine()->getManager();
          //$entity3 = $em->getRepository('SytemSGBundle:Laboratorios')->findOneByNomeLaboratorio($entity->getLaboratorio());        
          
                if (!$entity) {
                     $this->get('session')->getFlashBag()->set('message','Registro não foi encontrado.');
                }
                
                $entity2 = new DadosArquivos();    
                //$entity2->setLaboratorio($entity3);
                $entity2->setDescricao($entity->getDescricaoequip());    
                $form = $this->create2CreateForm($entity2);
                $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                return $this->render('SytemSGBundle:DadosArquivos:newdeferido.html.twig', array(
                    'entity' => $entity2,
                    'entity_value' => $entity,
                    'responsavel' => $resp,
                    'usuario' => $usuario,
                    'form_r'   => $form->createView(),
                    'json'   => $json,
                    'total'=>$total,
                ));
    }

    /**
     * Finds and displays a DadosArquivos entity.
     *
     */
    public function showAction($id)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
                $em = $this->getDoctrine()->getManager();
                $this->copiaPathfile();
                $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
                $array=$em->getRepository('SytemSGBundle:DadosArquivos')->findByResponsavel($entity->getResponsavel());
                $total=count($array);


                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
                }
                $laboratorio=$em->getRepository('SytemSGBundle:Laboratorios')->find($entity->getLaboratorio());
                $responsavel=$em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getResponsavel());
                $usuario=$em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getUsuario());

                $deleteForm = $this->createDeleteForm($id);
                
                return $this->render('SytemSGBundle:DadosArquivos:show.html.twig', array(
                    'entity'      => $entity,
                    'laboratorio' => $laboratorio,
                    'user'=> $responsavel,
                    'usercomum'=> $usuario,
                    'delete_form' => $deleteForm->createView(),
                    'total'=>$total,
                ));

        //}
    // }
          
          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 
   
    }

    /**
     * Displays a form to edit an existing DadosArquivos entity.
     *
     */
    public function editAction($id)
    {
     $session = $this->getRequest()->getSession();
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
     }
        $em = $this->getDoctrine()->getManager();        
        $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);  
        $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                
        return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json' => $json,

        ));

        //}
     //}
          
          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 
   
    }

    /**
    * Creates a form to edit a DadosArquivos entity.
    *
    * @param DadosArquivos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DadosArquivos $entity)
    {
        $form = $this->createForm(new DadosArquivosType(), $entity, array(
            'action' => $this->generateUrl('dadosarquivos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar','attr' => array('class'=>'btn btn-primary')));

        return $form;
    }
    /**
     * Edits an existing DadosArquivos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
     }
      $user = $this->get('security.context')->getToken()->getUser(); 

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        //Validação ip
        $campos = $request->request->all()["sytem_bundle_sgbundle_dadosarquivos"];
        $ip = $campos['ip'];
        $resp =$request->get('responsavel');// $campos['responsavel'];
        $usuario=$request->get('usuario'); //$campos['usuario'];
                

        if ($editForm->isValid()) {
            $ipValidate=explode(".",$ip);

            $ipValidate[0];
            $ipValidate[1];
            $ipValidate[2];
            $ipValidate[3];
            $ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];

           $em2 = $this->getDoctrine()->getManager();
           if(!preg_match('/^([0-9]{11})$/',$resp, $matches )||!preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                          $this->get('session')->getFlashBag()->add('message','Este valor não é um cpf.');
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          'json' => $json,
                           ));
                   }
          if(preg_match('/^([0-9]{11})$/',$resp, $matches ) && preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                    if(!$em2->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp) ){
                     if(($this->searchldap($resp/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.'.$resp);
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          'json' => $json,
                           ));
                      }
                    }
                    if(!$em2->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario)){
                    
                      if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.'.$usuario);
                          $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
                          'entity'      => $entity,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                          'json' => $json,
                           ));
                      }
                    }
           }
           if(!$em2->getRepository('SytemSGBundle:Intervalosips')->findOneByIntervalo($ipfaixa)){
            $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' informado para atualização não pertence a faixa de ips cadastrados.');
            $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
            return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json' => $json,
             
             ) );

          }
          if($em2->getRepository('SytemSGBundle:Ipsexcecoes')->findOneByIpexcecao($ip)){
            $this->get('session')->getFlashBag()->set('message','O Ip: '.$ip.' informado para atualização não pode ser usado pois esta reservado para dispsitivos como impressoras.');
            $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
            return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json' => $json,
             
             ) );

          }
          $file= $editForm['filemove']->getData();
                  if(is_null($file)){ 
                            if($usuario!='90000000009'||$usuario!='10000000001'||$usuario!='11111111111'){   //echo $this->validatermo($usuario); exit();
                                  if($this->validatermo($usuario,$entity->getMac())==0){
                                                  $this->get('session')->getFlashBag()->add('message','O usuário especificado ainda não enviou o termo de compromisso.');
                                                  $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                                                  return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
                                                  'entity'      => $entity,
                                                  'edit_form'   => $editForm->createView(),
                                                  'delete_form' => $deleteForm->createView(),
                                                  'json' => $json,
                                                  ));

                                  }
                            } 
                  }  
                                     
                   if ($file) 
                   { 
                                
                                  $path=$this->patchupload();
                                  $pdf = DadosArquivosType::processFile($file, $entity,$path);
                                  $entity->setFile( $path.$pdf);
                    }     
            $resp1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp);
            $user1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario);                 
            $entity->setResponsavel($resp1);                
            $entity->setUsuario($user1); 

            $this->ReturnUser2($user,$entity);          
            $em->flush();
            //$this->FilesARPandDHCP();
            $this->get('session')->getFlashBag()->add('message','1 - Atualização feita com sucesso!! Clique em voltar para ter acesso a todos dados.');
            $results2=array();
            $results=array();       
            $results2=$this->ExecDHCPARPShells();
            $this->get('session')->getFlashBag()->add('message',$results2[0]);
            $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
            $results=$this->ExecShells();
            $this->get('session')->getFlashBag()->add('message',$results[0]);
            $this->get('session')->getFlashBag()->add('message',$results[1]);
            $this->get('session')->getFlashBag()->add('message',$results[2]);
            $this->get('session')->getFlashBag()->add('message',$results[3]);

            return $this->redirect($this->generateUrl('dadosarquivos'));
           
        }
        $json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          
        return $this->render('SytemSGBundle:DadosArquivos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json' => $json,
             
       ) );

        //}
    // }
          
          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 
   
    }
    /**
     * Deletes a DadosArquivos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        $user = $this->get('security.context')->getToken()->getUser(); 
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
            }
            $this->ReturnUser($user,$entity);
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('message','1 - Item removido com sucesso.');
            $results2=array();
            $results=array();       
            $results2=$this->ExecDHCPARPShells();
            $this->get('session')->getFlashBag()->add('message',$results2[0]);
            $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
            $results=$this->ExecShells();
            $this->get('session')->getFlashBag()->add('message',$results[0]);
            $this->get('session')->getFlashBag()->add('message',$results[1]);
            $this->get('session')->getFlashBag()->add('message',$results[2]);
            $this->get('session')->getFlashBag()->add('message',$results[3]);
            
        }

        return $this->redirect($this->generateUrl('dadosarquivos'));
        //}

     //}
          
          //return $this->render('SytemSGBundle:Login:login.html.twig',array('erro' => "Acesso Negado!!!", )); 
   
    }

    
    
    /**
     * Creates a form to delete a DadosArquivos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {   
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dadosarquivos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'OK','attr' => array('class'=>'btn btn-danger')))
            ->getForm()
        ;
    }

    /*
    * Gera o arquivo ARP
    */
    
    public function FilesARP(){
        $em = $this->getDoctrine()->getManager();
        $entities2 = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
        $em2 = $this->getDoctrine()->getManager();
        $entities = $em2->getRepository('SytemSGBundle:Intervalosips')->findAll();
        $namefile = $em2->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
       
        $cont2=0;
        $listabdInterv=array();
        foreach ($entities as $key=>$value) { 
             $listabdInterv[$cont2]= $value->getIntervalo();
             $cont2++;
        }

        $listaIP = array();
        $cont=0;
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
        //$path=$this->GetRoutersRemoto(8);
        $path=$this->patchweb();
        join('/', array(trim(" ", '/'), trim($path, '/')));
        if(file_exists($path)){
              //chmod($path, 0777);
              $arp= fopen($path.$namefile[2]->getNomeArquivo(), "w+");
              //Arp - config

              fputs ($arp,"######Faixa de Ips validos em uso.\n\n");   
              $entrada=null;
              foreach ($entities2 as $key=>$value) { 
                 if(!strcmp($value->getStatus(),'DESBLOQUEADO')){ 
                     $entrada = $value->getIp()." ".$value->getMac()."\n";
                     //$entrada2 ="host local".$value->getId()."{\n"."hardware ethernet ".$value->getMac().";\n"."fixed-address ".$value->getIp().";\n}\n";
                 }else {
                        $entrada = $value->getIp()." "."00:00:00:00:00:00"." ##IP Bloqueado"."\n";
                        //$entrada2 ="";
                 }               
                  fputs ($arp,$entrada);
                  //fputs ($dhcp,$entrada2);            
              }
              //fputs ($dhcp,"\n\n#fim Hosts Locais...\n");
              fputs ($arp,"\n######Faixa de Ips validos livres....\n\n");
              foreach ($ips as $key => $value) {
                  # code...
                  $ip_livres= $value." "."00:00:00:00:00:00\n";
                  fputs ($arp, $ip_livres);
              }
              fclose ($arp); 
              //echo $path;exit();
              return 'Arquivo Arp local atualizado com sucesso!!!';
        }/*else if (!file_exists($path)) {// testo se a pasta existe
            mkdir($path, 0777);  


        }*/ else{
              return 'Arquivo Arp local não foi atualizado com sucesso!!!';
        }  
            

    }
    public static function Files(){
      $dhcp=FilesDHCP();
      $arp=FilesARP();
     return array($arp,$dhcp);
      
    }

    /*
    * Gera o arquivo DHCP
    */
    public function FilesDHCP(){
      
        $em = $this->getDoctrine()->getManager();
        $entities2 = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
        $em2 = $this->getDoctrine()->getManager();
        $entities = $em2->getRepository('SytemSGBundle:Intervalosips')->findAll();
        $entities3 = $em2->getRepository('SytemSGBundle:Dnsips')->findAll();
        $subred= $em->getRepository('SytemSGBundle:Dhcpconf')->findAll();
        $cont2=0;
        $listabdInterv=array();
        foreach ($entities as $key=>$value) { 
             $listabdInterv[$cont2]= $value->getIntervalo();
             $cont2++;
        }

        $listaIP = array();
        $cont=0;
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
        $vtamanho=count($entities3);
        $palavra=null;
        $palavra1=null;
       
        foreach ($entities3 as $key => $value){           
           if(strcmp($value->getIpdns(), $entities3[$vtamanho-1]->getIpdns())){
               $palavra.=$value->getIpdns().",";
           }else{
               $palavra.=$value->getIpdns();
           }
        }
        
        $em = $this->getDoctrine()->getManager();
        $nomex = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
       // $path2=$this->GetRoutersRemoto(0);
        $path2=$this->patchweb();
        
        join('/', array(trim(" ", '/'), trim($path2, '/')));
        
        if(file_exists( $path2)){
              //chmod($path2, 0777);
              $dhcp= fopen($path2.$nomex[0]->getNomeArquivo(), "w+");
              //dhcp - config
              
              fputs($dhcp,"##DHCP feito em 06-08-2014 para subscrever o dhcpd.conf de dezembro de 2013 rodando ");
              fputs($dhcp,"\n#option domain-name icea_server ;\n#\n");
              fputs($dhcp,"option domain-name-servers\n".$palavra.";\ndefault-lease-time 99999999;\nmax-lease-time 99999999;\n\n");
              fputs($dhcp,"# ddns-update-style none;\n\n# If this DHCP server is the official DHCP server for the local\n# network, the authoritative directive should be uncommented.\nauthoritative;\n\n# Use this to send dhcp log messages to a different log file (you also\n# have to hack syslog.conf to complete the redirection).\n# log-facility daemon;\nlog-facility local7;\n\n");
              
              foreach ($subred as $key => $valuesubred) {
                fputs($dhcp,"\n".$valuesubred->getOptionrouters()."\n");
              }
              

              fputs($dhcp,"\n\n#HostsLocais\n\n\n");                
              //Arp - config
              //fputs ($arp,"######Faixa de Ips validos em uso.\n\n");   
              //$entrada=null;  
              $entrada2=null;
              foreach ($entities2 as $key=>$value) { 
                 if(!strcmp($value->getStatus(),'DESBLOQUEADO')){ 
                     //$entrada = $value->getIp()." ".$value->getMac()."\n";
                     $entrada2 ="host local".$value->getId()."{\n"."hardware ethernet ".$value->getMac().";\n"."fixed-address ".$value->getIp().";\n}\n";
                 }else {
                        //$entrada = $value->getIp()." "."00:00:00:00:00:00"." ##IP Bloqueado"."\n";
                        $entrada2 ="";
                 }               
                  //fputs ($arp,$entrada);
                  fputs ($dhcp,$entrada2);            
              }
              fputs ($dhcp,"\n\n#fim Hosts Locais...\n");                
              fclose ($dhcp);
              return 'Arquivo DHCP local atualizado com sucesso.';
        }else{
              return 'Arquivo DHCP local não foi atualizado com sucesso.';
        }
        
    }
   /*
    * Executa o arquivo arquivo icea
    */
    public function FilesShellexec(){
        $em = $this->getDoctrine()->getManager();       
        $pathfile = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();       
        $ip=$pathfile[3]->getServerip();
        $nomeArq=$pathfile[3]->getNomeArquivo();     
        $username=$pathfile[3]->getUsername();
        $destino=$pathfile[3]->getCaminhoDestino().$nomeArq;
        if($ip==""){
           
            return 'Execução local!!';
           
        }else{
          join('/', array(trim(" ", '/'), trim($destino, '/')));
          $cmd='ssh '.$username."@".$ip." ".$destino;
          $shell=shell_exec($cmd);
            if(!$shell){
              return ' o arquivo '.$nomeArq.' foi executado com sucesso ('.$shell.').';
            }else{          
              return ' o arquivo '.$nomeArq.' não foi executado com sucesso ('.$shell.').';
          
            }

        }
      
    }
    /*
    * executa o arquivo dhcp via ssh
    */
    public function FilesShelldhcdexec(){
        $em = $this->getDoctrine()->getManager();
        $pathfile = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();       
        $ip=$pathfile[4]->getServerip();
        $nomeArq=$pathfile[4]->getNomeArquivo();     
        $username=$pathfile[4]->getUsername();       
        $destino=$pathfile[4]->getCaminhoDestino().$nomeArq;
        if($ip==""){
           
            return 'Execução local!!!';
           
        }else{
          join('/', array(trim(" ", '/'), trim($destino, '/')));
          //$cmd="scp ".$destino2." ".$username."@".$ip.":".$destino;
          $cmd='ssh '.$username."@".$ip." ".$destino;
          $shell=shell_exec($cmd);
            if(!$shell){
              return ' o arquivo '.$nomeArq.' foi executado com sucesso ('.$shell.').';
            }else{
              return ' o arquivo '.$nomeArq.' não foi executado com sucesso ('.$shell.').';
            }        

        }
      
    }
     /*
    * gera os valores da progressao de faixas
    */
    public function PorcentagemGrafico(){

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
        $em2 = $this->getDoctrine()->getManager();
        $entities2 = $em2->getRepository('SytemSGBundle:Intervalosips')->findAll();
        $contIp=count($entities);
        $contintervalosIp=count($entities2);
        $LISTA= array();
        $v;
        $cont=0;
        $cont1=0;
        foreach ($entities as $key=>$value) {
             $ipValidate=explode(".",$value->getIp());
             $ipValidate[0];
             $ipValidate[1];
             $ipValidate[2];
             $ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
             $LISTA[$cont]=$ipfaixa ;
             $cont++;
        }
        $f=array_count_values($LISTA);
        ksort($f);
       return $f; 
    }

  
   
   /*
     Le a path dos arquivos.
   */
    public function GetRoutersRemoto($value){
      $result=null;
      $file = $this->container->get('file_locator')->locate('@SytemSGBundle/Resources/file_routers/file_roters_remoto.txt');
       
      if(file_exists($file)){
        $lines = file ($file);
        //$this->get('session')->getFlashBag()->set('messageExecut', 'Arquivo encontrado!!!');
        $result= $lines[$value];
        //echo "aqui";

      }else{
        $this->get('session')->getFlashBag()->set('messageExecut', 'path nao existe.');
        //echo 'path nao existe';
        $result= null;

      }
      //exit();
      return $result;
    }
    
    //Atualiza na base de dados solicitações o status da avaliação
    private function SolicitacaoDeferida($id){

           
            $user = $this->get('security.context')->getToken()->getUser(); 
            $em = $this->getDoctrine()->getManager();      
            $entity = $em->getRepository('SytemSGBundle:Solicitacoes')->find($id);

            if (!$entity) {
                 $this->get('session')->getFlashBag()->set('message','Não foi possivel excluir registro.');
                //throw $this->createNotFoundException('Unable to find Laboratorios entity.');
            }
            $entity->setAvaliacao('DEFERIDO');
            
            $entity->setRespaval($user->getNome());
            //$em->remove($entity);
            $em->flush();
    }
    
    public function joins_paths($path){
      return join('/', array(trim(" ", '/'), trim($path, '/')));
    }
    
   /*
    * Executa o comando e copia o arquivo dhcp para o caminho destino...
   */
    public function ShelltransporteDHCP(){
      $em = $this->getDoctrine()->getManager();
      $pathfile = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
      $nomeArq=$pathfile[0]->getNomeArquivo();
      $origem=$this->patchweb().$nomeArq;
      $username=$pathfile[0]->getUsername();
      $ip=$pathfile[0]->getServerip();
      $destino=$pathfile[0]->getCaminhoDestino();
      if($ip==""){
          return 'Execução local';
     
      }else{

         join('/', array(trim(" ", '/'), trim($origem, '/')));
         join('/', array(trim(" ", '/'), trim($destino, '/')));
        
         $cmd="scp ".$origem." ".$username."@".$ip.":".$destino;
         $shell=shell_exec($cmd);
         if(!$shell){
          return 'O arquivo dhcp esta sendo copiado com sucesso ('.$shell.').';
         }else{
          return 'Falha ao copiar o arquivo dhcp ('.$shell.').';
         }
      }
      
    }
    /*
    * Executa o comando e copia o arquivo arp para o caminho destino...
   */
    public function ShelltransporteARP(){
      $em = $this->getDoctrine()->getManager();
      $pathfile = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
      $nomeArq=$pathfile[2]->getNomeArquivo();
      $origem=$this->patchweb().$nomeArq;
      $destino=$pathfile[2]->getCaminhoDestino();
      $username=$pathfile[2]->getUsername();
      $ip=$pathfile[2]->getServerip();
      if($ip==""){
          return 'Execução local';
     
      }else{
        join('/', array(trim(" ", '/'), trim($origem, '/')));
        join('/', array(trim(" ", '/'), trim($destino, '/')));
        
        $cmd="scp ".$origem." ".$username."@".$ip.":".$destino;
        $shell=shell_exec($cmd);
        if(!$shell){
            return 'O arquivo arp esta sendo copiado com sucesso ('.$shell.').';
        }else{
            return 'Falha ao copiar o arquivo arp ('.$shell.').';
        }
      }
      
    }

    /*chama as funcoes que copiam os arquivos arp e dhcp*/

    public function ExecShells(){
        $menssage=null;
         $arp=$this->ShelltransporteARP();
         $dhcp=$this->ShelltransporteDHCP();
         $icea=$this->FilesShellexec();
         $dhcp_exec=$this->FilesShelldhcdexec();

         $menssage=array();
         $menssage[0]= '4 - Arp: '.$arp;
         $menssage[1]= '5 - Dhcp: '.$dhcp;        
         $menssage[2]= '6 - Arp executado: '.$icea;
         $menssage[3]= '7 - DHCP executado: '.$dhcp_exec;
        return $menssage;      

    }

    /*chama as funcoes que geram os arquivos arp e dhcp*/

    public function ExecDHCPARPShells(){
        $menssage=null;
        $arp=$this->FilesARP();
        $dhcp=$this->FilesDHCP();
        $menssage=array();
        $menssage[0]='2 - ARP criado localmente: '.$arp;
        $menssage[1]='3 - DHCP criado localmente: '.$dhcp;
       
        
        return $menssage;      

    }
   

   /*
    * Gera o caminho para a pasta systemfiles
   */
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

   /*
    * Gera o caminho para a pasta systemfiles /uploadsTC
   */
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

   /*
    * Gera o caminho para a pasta systemfiles arquivo de log
   */

    public function patchweblog(){
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

/*
 * As tres funções abaixo fazem o papel de atualizar todos os dados gerenciados por um usuario
 * São elas edituser
*/
   

    /**
    * Displays a form to edit an existing DadosArquivos.      s $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEdituserForm(DadosArquivos $entity)
    {
        $form = $this->createForm(new DadosArquivosType(), $entity, array(
            'action' => $this->generateUrl('updateuser', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Atualizar','attr' => array('class'=>'btn btn-primary')));

        return $form;
    }
    /**
     * Edits an existing DadosArquivos entity.
     *
     */

     /**
     * Displays a form to edit an existing DadosArquivos entity.
     * Gera as views para gerenciamento dos mecs usuarios comuns.
     *
     */
    public function edituserAction($id)
    {
     $session = $this->getRequest()->getSession();
     if (!$this->get('security.context')->isGranted('ROLE_USER') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
     }
        $em = $this->getDoctrine()->getManager();
        //$em3=$this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
        $laboratorio=$em->getRepository('SytemSGBundle:Laboratorios')->find($entity->getLaboratorio());
       
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
        }

        $editForm = $this->createEdituserForm($entity);
        return $this->render('SytemSGBundle:DadosArquivos:edituser.html.twig', array(
            'entity'      => $entity,  
            'laboratorio'  => $laboratorio,            
            'edit_form_user'   => $editForm->createView(),  
        ));

    }

    /* Atualiza os dados dos usuarios comuns referentes aos dispositivos. */

    public function updateuserAction(Request $request, $id)
    {
     if (!$this->get('security.context')->isGranted('ROLE_USER') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
     }
      $user = $this->get('security.context')->getToken()->getUser(); 
      
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($id);

        $laboratorio=$em->getRepository('SytemSGBundle:Laboratorios')->find($entity->getLaboratorio());       
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DadosArquivos entity.');
        }
        $editForm = $this->createEdituserForm($entity);
        $editForm->handleRequest($request);
        //if($request->getMethod()=='POST'){  
        $usuario=$request->get('usuario');
      //$file= $request->files->get('files')); 
          //$request->file->get('files');
        

        if ($editForm->isValid()) {

             
             if(!preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                          $this->get('session')->getFlashBag()->add('message','Este valor não é um cpf.');
                          //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:edituser.html.twig', array(
                          'entity' => $entity,
                          'laboratorio' => $laboratorio,            
                          'edit_form_user'   => $editForm->createView(), 
                          
                           ));
                   }
                   $file=$editForm['filemove']->getData();
                   if( preg_match('/^([0-9]{11})$/',$usuario, $matches )){
                    if( !$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario)){
                     
                      if(($this->searchldap($usuario/*Request $request*/)!==TRUE) ){
                          $this->get('session')->getFlashBag()->add('message','O cpf não existe no ldap ou não há conexão.');
                          //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                          return $this->render('SytemSGBundle:DadosArquivos:edituser.html.twig', array(
                          'entity' => $entity,
                          'laboratorio' => $laboratorio,            
                          'edit_form_user'   => $editForm->createView(), 
                           ));
                      }
                      
                       
                          
                         
                          }
                    }
              if(is_null($file)){ 
              //echo $this->validatermo($usuario); exit();
                if($this->validatermo($usuario,$entity->getMac())==0){
                                $this->get('session')->getFlashBag()->add('message','O usuário especificado ainda não enviou o termo de compromisso.');
                                //$json=json_decode(json_encode($this->ipsValidos(),JSON_FORCE_OBJECT),true);
                                return $this->render('SytemSGBundle:DadosArquivos:edituser.html.twig', array(
                                'entity' => $entity,
                                'laboratorio' => $laboratorio,            
                                'edit_form_user'   => $editForm->createView(), 
                                 ));

                }
              }  
                   
             if ($file) 
             { 
                          
                            $path=$this->patchupload();
                            $pdf = DadosArquivosType::processFile($file, $entity,$path);
                            $entity->setFile( $path.$pdf);


              }        
    
           
            //$resp1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($resp);
            $user1=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf($usuario);                 
            //$entity->setResponsavel($resp1);  
            
            $entity->setUsuario($user1);
            $this->ReturnUser2($user,$entity);          
            $em->flush();
            $this->Notificacoes($entity);
            $this->get('session')->getFlashBag()->add('message','Atualização feita com sucesso, aguarde a liberação da alteração pela Nti. Clique em voltar para ter acesso a todos dados.');

            return $this->redirect($this->generateUrl('search_user'));
           
        }  
     

        return $this->render('SytemSGBundle:DadosArquivos:edituser.html.twig', array(
            'entity'      => $entity,
            'laboratorio' => $laboratorio,            
            'edit_form_user'   => $editForm->createView(),                  
         ));
    
    }

    /**
     * Função que trata as notificações de atualização
     */

    public function Notificacoes($entity){
         $em = $this->getDoctrine()->getManager();
         $option = $em->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($entity->getMac());
         if($option && ($option->getAvaliacao()=='DEFERIDO') ){
           $option->setAvaliacao('ATUALIZADO');
           $em->flush();

         }else if(!$option){
             $option2 = $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getResponsavel());
             $option3= $em->getRepository('SytemSGBundle:Usuariosystem')->find($entity->getUsuario());
             //$option2->getId(); exit();
             $user = $this->get('security.context')->getToken()->getUser();
             date_default_timezone_set("UTC");
             /*1*/$solicit= new Solicitacoes();  
             /*2*/$solicit->setMac($entity->getMac()); 
             /*3*/$solicit->setTipo($entity->getTipo());
             /*4*/$solicit->setAvaliacao('ATUALIZADO');            
             /*7*/$solicit->setResponsavel($option2);
             /*8*/$solicit->setDataSolicitacao(new \DateTime);
             /*9*/$solicit->setJustificativa('Atualização do equipamento.');
             /*10*/$solicit->setUsuario($option3);
             //$solicit->setResponsavel($entity->getResponsavel());
             /*11*/$solicit->setDescricaoequip('n/d');             
             /*12*/$solicit->setLaboratorio($entity->getLaboratorio());            
             /*13*/$solicit->setRespaval($user->getNome()." /".$user->getEmail());    
             /*14*/$solicit->setMotivo('Atualização');    
             
             $em->persist($solicit);
             $em->flush();

         }


    }
     /**
     * Executa as atualizações de notificações de atualização.
     */
    public function atualizaNotificacoesAction(){
            $em = $this->getDoctrine()->getManager();
            $repositorio=$em->getRepository('SytemSGBundle:Solicitacoes');
            $query=$repositorio->createqueryBuilder('d')
                   ->where('d.avaliacao = :avaliacao') 
                   ->setParameter('avaliacao','ATUALIZADO') 
                   ->orderBy('d.mac','ASC')
                   ->getQuery();
            $entities = $query->getResult();

            $em = $this->getDoctrine()->getManager();
            

          if(count($entities)>0){
                foreach ($entities as $key => $value) {
                  if(!$em->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($value->getMac())->getAvaliacao()=='DEFERIDO'){
                    $value->setAvaliacao('DEFERIDO');
                    $em->flush();
                  }else{
                    $em->remove($value);
                    $em->flush();
                  }
                }
               $results2=array();
               $results=array();
               $this->get('session')->getFlashBag()->add('message','1 - Deferido com sucesso!!!');
                                      
               $results2=$this->ExecDHCPARPShells();
               $this->get('session')->getFlashBag()->add('message',$results2[0]);
               $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
               $results=$this->ExecShells();
               $this->get('session')->getFlashBag()->add('message',$results[0]);
               $this->get('session')->getFlashBag()->add('message',$results[1]);
               $this->get('session')->getFlashBag()->add('message',$results[2]);
               $this->get('session')->getFlashBag()->add('message',$results[3]);
          }

         return $this->redirect($this->generateUrl('solicitacoes'));

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

    /*Procura os usuarios pelo cpf e retorna as infoemaçoes do usuario para */
    
  
   /* 
    * Verifica se o arquivo com o termo existe para dado usuario se não solicita que o anexe ao dispositivo
    *
    */

  public function validatermo($search,$macs){
     $em2 = $this->getDoctrine()->getManager();
     $j=0; $i=0;
     $x=$em2->getRepository('SytemSGBundle:Usuariosystem')->findOneBy(array('cpf'=>$search));
     $mac=$em2->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($macs);
     $mac2=$em2->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($macs);
     $cpf=$em2->getRepository('SytemSGBundle:DadosArquivos')->findByUsuario($x->getId());
     $cpf2=$em2->getRepository('SytemSGBundle:Solicitacoes')->findByUsuario($x->getId());
     
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
      if($cpf=='90000000009'|| $cpf=='10000000001'||$cpf=='11111111111'){   
        $i=1;

      }
      if($cpf2=='90000000009'|| $cpf2=='10000000001'||$cpf2=='11111111111'){   
        $j=1;

      }


      
     if($i>0 || $j>0){
      return 1;
     }else if($i==0 && $j==0){
      return 0;
     }



  }
  /*exclui as notificações de exclusão, executa e copia os arquivos arp e dhcp atualizados*/
  public function exclusaoMACAction(){
   if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
   }
   $user = $this->get('security.context')->getToken()->getUser(); 


   $em2 = $this->getDoctrine()->getManager();
   $macexclusao=$em2->getRepository('SytemSGBundle:Solicitacoes')->findBy(array('avaliacao'=>'APAGAR'));
   $em = $this->getDoctrine()->getManager();
   //echo count($macexclusao);echo $macexclusao[0]->getMac();echo $macexclusao[1]->getMac();
   foreach ($macexclusao as $key => $value) {

      $excluir=$em->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($value->getMac());
      //echo $excluir;
      $atualizaaval=$em2->getRepository('SytemSGBundle:Solicitacoes')->findOneByMac($value->getMac());
     
         if($excluir){
          
            $this->ReturnUser($user,$excluir);
            $em->remove($excluir);          
            $em->flush(); 
          if($atualizaaval){
           
              if($atualizaaval->getAvaliacao()=='APAGAR'){
                 $em2->remove($atualizaaval);          
                 $em2->flush(); 
              }
            } 

           }
    }
  
           
   $this->get('session')->getFlashBag()->add('message','1 - Itens removidos com sucesso!!!');
   $results2=array();
   $results=array();
                                      
   $results2=$this->ExecDHCPARPShells();
   $this->get('session')->getFlashBag()->add('message',$results2[0]);
   $this->get('session')->getFlashBag()->add('message',$results2[1]);
                   
   $results=$this->ExecShells();
   $this->get('session')->getFlashBag()->add('message',$results[0]);
   $this->get('session')->getFlashBag()->add('message',$results[1]);
   $this->get('session')->getFlashBag()->add('message',$results[2]);
   $this->get('session')->getFlashBag()->add('message',$results[3]);


   return $this->redirect($this->generateUrl('solicitacoes'));

}

/*Força o dowload do arquivo essa action é chamada na tela de avaliação deferida para verificar o termo de compromisso!!!*/
public function forcedowloadAction($id,$flag){
     $arquivo=null;
     if($flag==1){
         $aux=$this->auxforcedownload($id);
         if(!is_null($aux)){
            $arquivo=$aux;
         }else{
            $this->get('session')->getFlashBag()->add('message','O documento não existe ou foi removido.');        
           return $response = $this->redirect($this->generateUrl('dadosarquivos_newdeferido',array('id'=>$id)));
            
         }
     }else if($flag==2){
         $aux=$this->auxforcedownloadshow($id);
         if(!is_null($aux)){
            $arquivo=$aux;
         }else{
            $this->get('session')->getFlashBag()->add('message','O documento não existe ou foi removido.');        
           return $response = $this->redirect($this->generateUrl('dadosarquivos_show',array('id'=>$id)));
            
         }

     }
     
     join('/', array(trim(" ", '/'), trim($arquivo, '/')));
     if(file_exists($arquivo)){ 
    // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
        switch(strtolower(substr(strrchr(basename($arquivo),"."),1))){ // verifica a extensão do arquivo para pegar o tipo
           case "pdf": $tipo="application/pdf"; break;
           
        }
        $response = $this->render('SytemSGBundle:Solicitacoes:forcedowload.html.twig',array(
        'data'=> new \DateTime(),
        
          
        ));
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($arquivo));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($arquivo) . '";');
        $response->headers->set('Content-length', filesize($arquivo));
        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(readfile($arquivo));
        
        //$this->get('session')->getFlashBag()->set('message','Arquivo gerado com sucesso!!!');
        
      exit; // aborta pós-ações
      return $response;
   }else{
    if($flag==1){
       $this->get('session')->getFlashBag()->add('message','O pdf solicitado não existe.');        
       return $response = $this->redirect($this->generateUrl('dadosarquivos_newdeferido',array('id'=>$id)));
     }else if($flag==2){
        $this->get('session')->getFlashBag()->add('message','O pdf solicitado não existe.');        
       return $response = $this->redirect($this->generateUrl('dadosarquivos_show',array('id'=>$id)));
     
     }  
   }
   
}



public function auxforcedownload($id){
  $em = $this->getDoctrine()->getManager();
     $j=0; $i=0;
     $solocitacoes=$em->getRepository('SytemSGBundle:Solicitacoes')->find($id);
     $arquivo=null;$aux=null;$aux2=null;

     if($solocitacoes){
        $dir=$this->patchupload();
        $arrayZ=scandir($dir);
        print_r($arrayZ);
        $sol=$em->getRepository('SytemSGBundle:Solicitacoes')->findBy(array('usuario'=> $solocitacoes->getUsuario()));
        $i=0;
        foreach ($sol as $key => $value) {
         
           if(!is_null($value->getFile())){
               if(in_array(basename($value->getFile()), $arrayZ)){
                      $aux=$value->getFile();
                      break;
               }
              
           }
        }
      
        if(!is_null($aux)){
            $arquivo= $aux;
        }else{

          $sol2=$em->getRepository('SytemSGBundle:DadosArquivos')->findBy(array('usuario'=> $solocitacoes->getUsuario()));
          foreach ($sol2 as $key => $value2) {
             if(!is_null($value2->getFile())){
                if(in_array(basename($value2->getFile()), $arrayZ)){
                      $aux2=$value2->getFile();
                      break;
                }
                   
             }
          }
          if(!is_null($aux2)){
              $arquivo= $aux2;
          }
       }
       if(!is_null($arquivo)){
          return $arquivo;
       }
       else{
         return null;
       }
    
     }else{
         return null;
     }

}

public function auxforcedownloadshow($id){
     $em = $this->getDoctrine()->getManager();
     $j=0; $i=0;
     $solocitacoes=$em->getRepository('SytemSGBundle:DadosArquivos')->find($id);
     $arquivo=null;$aux=null;$aux2=null;

     if($solocitacoes){
        $dir=$this->patchupload();
        $arrayZ=scandir($dir);
        print_r($arrayZ);
        $sol=$em->getRepository('SytemSGBundle:DadosArquivos')->findBy(array('usuario'=> $solocitacoes->getUsuario()));
        $i=0;
        foreach ($sol as $key => $value) {
         
           if(!is_null($value->getFile())){
               if(in_array(basename($value->getFile()), $arrayZ)){
                      $aux=$value->getFile();
                      break;
               }
              
           }
        }
      
        if(!is_null($aux)){
            $arquivo= $aux;
        }
       if(!is_null($arquivo)){
          return $arquivo;
       }
       else{
         return null;
       }
    }else{
         return null;
  }

}

public function copiaPathfile(){
       
    $em = $this->getDoctrine()->getManager();
    $users=$em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
    $array=array();
    $i=0;$j=0;
    foreach ($users as $key => $value) {      
         $array[$i]=(string)$value->getUsuario(); 
         $i++;
    }
    $arrayValues=array_count_values($array);
    $arrayAux=array_keys($arrayValues);
    
       foreach ($arrayAux as $key => $valuex) {
          foreach ($users as $key => $value) {  
              if(!strcmp($valuex,(string)$value->getUsuario()) && $value->getFile()){
                if($value->getFile()!=$this->patchupload()){
                  $arrayfile[$j++]=$valuex.';'.$value->getFile();
                  break;
                }
              } 
          }
       }
   

    foreach ($arrayfile as $key => $valuex) {
          $x=explode(';',$valuex);
          foreach ($users as $key => $value) {  
              if(!strcmp($x[0],(string)$value->getUsuario()) && is_null($value->getFile())){
                  $value->setFile($x[1]);
                  $em->flush();
                  
                }
              } 
     }
}

  public function searchCpfAction(Request $request,$flag){

     if ($request->getMethod()=='POST') {
        $cpf=$request->get('usuario');
        $id=$request->get('id');
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
                              
                              if($flag=='a'){       
                                return new Response('<div  class="alert alert-default" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.$response.'</strong></div>',200);
                         
                              }else if($flag=='b'){
                                return new Response('<div  class="alert alert-default" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.$response.'</strong></div>',200);
                         
                              } else if($em->getRepository('SytemSGBundle:DadosArquivos')->find($flag)->getId()==$flag){
                                return new Response('<div  class="alert alert-default" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.$response.'</strong></div>',200);
                         
                              } 
                                                                   
                          }else{
                           // $this->get('session')->getFlashBag()->set('message2','O cpf não existe na base de dados do LDAP!!!');$user = $this->get('security.context')->getToken()->getUser();
                            if($flag=='a'){       
                             return new Response('<div  class="alert alert-danger" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O cpf não existe na base de dados do LDAP!!!'.'</strong></div>', 200);
                          
                            }else if($flag=='b'){
                              return new Response('<div  class="alert alert-danger" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O cpf não existe na base de dados do LDAP!!!'.'</strong></div>', 200);
                          
                              }else if($em->getRepository('SytemSGBundle:DadosArquivos')->find($flag)->getId()==$flag){
                             return new Response('<div  class="alert alert-danger" ><button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O cpf não existe na base de dados do LDAP!!!'.'</strong></div>', 200);
                          
                            } 

                          }
                          
                    }
                    @ldap_close($ldapconn);
          
       } 
       //$this->get('session')->getFlashBag()->set('message2','Não há conexão com o LDAP..');
           if($flag=='a'){       
             return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não há conexão com o LDAP..'.'</strong></div>', 200);
            
           }else if($flag=='b'){
               return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não há conexão com o LDAP..'.'</strong></div>', 200);
            
           }else if($em->getRepository('SytemSGBundle:DadosArquivos')->find($flag)->getId()==$flag){
             return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'Não há conexão com o LDAP..'.'</strong></div>', 200);
            
           } 

     }
          
      }else{  
          //$this->get('session')->getFlashBag()->set('message2','O não foram informados dados para a busca!!!');
           if($flag=='a'){       
                return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O não foram informados dados para a busca!!!'.'</strong></div>', 200);
         
           }else if($flag=='b'){
                  return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O não foram informados dados para a busca!!!'.'</strong></div>', 200);
         
           }else if($em->getRepository('SytemSGBundle:DadosArquivos')->find($flag)->getId()==$flag){
                return new Response('<div  class="alert alert-danger">  <button  type="button" class="close" data-dismiss="alert">x</button><strong>'.'O não foram informados dados para a busca!!!'.'</strong></div>', 200);
         
           } 
        }
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


