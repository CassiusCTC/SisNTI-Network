<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sytem\Bundle\SGBundle\Entity\Dhcpconf;
use Sytem\Bundle\SGBundle\Form\DhcpconfType;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;
use Sytem\Bundle\SGBundle\Controller\DadosArquivosController;
/**
 * IntervalosIps controller.
 *
 */
class DhcpconfController extends Controller
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
        
        $entities = $em->getRepository('SytemSGBundle:Dhcpconf')->findAll();
        date_default_timezone_set("America/Sao_Paulo");
        
        return $this->render('SytemSGBundle:Dhcpconf:index.html.twig', array(
            'entities' => $entities,
            'data'=> new \DateTime(),
        ));
    }
    /**
     * Creates a new Dhcpconf entity.
     *
     */
    public function createAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }
        $entity = new Dhcpconf();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $mensagem=$this->ExecDHCP();  
            $mensagem2= array();
            $mensagem2=$this->ExecShells();
            
            $this->get('session')->getFlashBag()->add('message', '1 - Criado com sucesso.');
            $this->get('session')->getFlashBag()->add('message',$mensagem);
            $this->get('session')->getFlashBag()->add('message',$mensagem2[0]); 
            $this->get('session')->getFlashBag()->add('message',$mensagem2[1]);         
            
            return $this->redirect($this->generateUrl('dhcpconf', array('id' => $entity->getId())));
        }

        return $this->render('SytemSGBundle:Dhcpconf:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Dhcpconf entity.
     *
     * @param Dhcpconf $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Dhcpconf $entity)
    {
        $form = $this->createForm(new DhcpconfType(), $entity, array(
            'action' => $this->generateUrl('dhcpconf_create'),
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

        $entity = new Dhcpconf();
        $form   = $this->createCreateForm($entity);

        return $this->render('SytemSGBundle:Dhcpconf:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a IntervalosIps entity.
     *
     */
    /*public function showAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SytemSGBundle:Dhcpconf')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Intervalos de Ips entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Dhcpconf:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }*/

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

        $entity = $em->getRepository('SytemSGBundle:Dhcpconf')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dhcpconf entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SytemSGBundle:Dhcpconf:edit.html.twig', array(
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
    private function createEditForm(Dhcpconf $entity)
    {
        $form = $this->createForm(new DhcpconfType(), $entity, array(
            'action' => $this->generateUrl('dhcpconf_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('SytemSGBundle:Dhcpconf')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dhcpconf entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $em->flush();

             $mensagem=$this->ExecDHCP(); 
             $mensagem2=array();
             $mensagem2=$this->ExecShells();
            
            $this->get('session')->getFlashBag()->add('message', '1 - Editado com sucesso.');
                  
            $this->get('session')->getFlashBag()->add('message',$mensagem);
            $this->get('session')->getFlashBag()->add('message',$mensagem2[0]);  
            $this->get('session')->getFlashBag()->add('message',$mensagem2[1]);  

            return $this->redirect($this->generateUrl('dhcpconf', array('id' => $id)));
        }

        return $this->render('SytemSGBundle:Dhcpconf:edit.html.twig', array(
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
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SytemSGBundle:Dhcpconf')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Dhcpconf não existe na base de dados.');
            }

            $em->remove($entity);
            $em->flush();     
            $mensagem=$this->ExecDHCP();
            $mensagem2=array();
            $mensagem2=$this->ExecShells();
            
            
            $this->get('session')->getFlashBag()->add('message', '1 - Item removido com sucesso.');
            $this->get('session')->getFlashBag()->add('message',$mensagem);
            $this->get('session')->getFlashBag()->add('message',$mensagem2[0]);
            $this->get('session')->getFlashBag()->add('message',$mensagem2[1]);
        }

        return $this->redirect($this->generateUrl('dhcpconf'));
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
            ->setAction($this->generateUrl('dhcpconf_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
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
    public function ExecDHCP(){
        $menssage=null;
        $dhcp=$this->FilesDHCP();
        $menssage='2 - DHCP local: '.$dhcp.'.';
        
        return $menssage;      

    }

   
    public function ExecShells(){
        $menssage=null;
        

         $dhcp=$this->ShelltransporteDHCP();
         $dhcpd=$this->FilesShelldhcdexec();
         $menssage=array();
        
         $menssage[1]='3 - Dhcp copiado: '.$dhcp.'.';
         $menssage[0]='4 - Dhcp executado: '.$dhcpd;

       
       
        return $menssage;      

    }

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
              return 'o arquivo '.$nomeArq.' executado remotamente com sucesso ( '.$shell.' ).';
            }else{
              return 'o arquivo '.$nomeArq.' não executado remotamente com sucesso ( '.$shell.' ).';
            }        

        }
      
    }
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
          return 'o arquivo '.$nomeArq.' esta sendo copiado com sucesso ( '.$shell.' ).';
         }else{
          return 'o arquivo '.$nomeArq.' não esta sendo copiado com sucesso ( '.$shell.' ).';
         
         }
      }
      
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
        $pathjoin=$pathaux.'web/systemfiles/';
        join('/', array(trim(" ", '/'), trim($pathjoin, '/')));
        return $pathjoin;
    }   

   

}

