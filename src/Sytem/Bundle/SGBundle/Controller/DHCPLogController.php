<?php

namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collection\ArrayCollection;

use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Entity\DadosArquivosRepository;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;

use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Process\Process;
/**
 * Dhcplog controller.
 *
 */
class DhcplogController extends Controller
{
  


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

	public function GetRouters($value){
      $result=null;
      $file = $this->container->get('file_locator')->locate('@SytemSGBundle/Resources/file_routers/file_routers.txt');
       
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

    /*
        Dhcp log functions
    */

    public function geraDateDHCPlog(){
          
       //$path = $this->GetRoutersRemoto(6);
      
            
			 $result =null;
       $status=false;            
       $em = $this->getDoctrine()->getManager();
       $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll(); 
       $filename = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll(); 
       $path=$this->patchweb().$filename[1]->getNomeArquivo();
       join('/', array(trim(" ", '/'), trim($path, '/')));  
       $array=array();
       $aux= array();
       $i=0;
       $j=0;
       
       if(file_exists($path)){
         
        $c=file($path);          
                   
        foreach($c as $key => $valuefile){ 
              if(!strpos($valuefile, "DHCPDISCOVER")===false){
                     $strig=explode(" ", $valuefile);

                     $aux[$i]= $strig[0]."/".$strig[2]."/".$strig[8];
                     $i++;

              }
        }
        
        krsort($aux);        
        $result=array_keys(array_count_values($aux));

        $array_mac= array();
        foreach ($entities as $key => $value) { 
            
            $array_mac[$i]=$value->getMac();
            $i++;
        }
        
        $cont=0;
        $array_macAlterados= array();
        foreach ($result as $key => $valuefile){  
          $string=explode("/", $valuefile);
          if(in_array($string[2],$array_mac)==true){

              $array_macAlterados[$cont] = $valuefile;
              $cont++;
          }

        }
        
      $diasView=array();

      foreach ($entities as $key => $value) {  
          foreach ($array_macAlterados as $key => $valuefile){  
              $string=explode("/", $valuefile);
              if(strcmp($string[2],$value->getMac())==0){

                $date=explode("/",$valuefile);
                $todayI = date("Y");  
                $todayF = date("d/m/y");             
                $mes=$this->verifica_mes($date[0]); 
                $data_inicio= $date[1]."/".$mes."/".$todayI;
                $data_fim=$todayF;
                $dataDHCPLog=$this->verificaAno($data_inicio,$data_fim);
                $inicial=$this->dif_datas($dataDHCPLog, $data_fim);
                $dats_inicial = explode(';', $inicial);
                $dias=$this->retornadias($dats_inicial[0],$data_fim);   
                $diasView[$cont]=  $string[2]."-". $dias;  
                $dateformat=explode('/',  $dataDHCPLog);
                $dateformatresult=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]."";
                $verificacao=$this->verificaAlteradata($dataDHCPLog, $value->getDataDhcplog()->format('d/m/Y'));
                
                if($verificacao==true){
                            
                  $entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($value->getId());
                  $entity->setDataDhcplog(new \DateTime(date($dateformatresult)));//new \DateTime(date($dateformatresult))
                  
                  $em->flush();
                }

                $cont++; 
                  
              }
             
              /*$entity = $em->getRepository('SytemSGBundle:DadosArquivos')->find($value->getId());
                $entity->setDataDhcplog(new \DateTime(date('2014-12-01')));//new \DateTime(date($dateformatresult))
                $em->flush();*/

            }
        }
        return '2 - Atualização da data via Dhcplog feita com sucesso.</span>';
      
     }else{
        return '2 - Erro ao atualizar via dhcplog.</span>';
     }
					   
		  //return;
    }
    public function verificaAno($dataDHCPLog, $dataCorrente){
        $data1=explode("/", $dataDHCPLog);
        $data2=explode("/", $dataCorrente);    
        

        if($data1[1]>$data2[1]){
          $result=$data1[0]."/".$data1[1]."/".(string)((int)$data1[2]-1);
          //echo $result;
          return  $result;

        }else{
          $result=$data1[0]."/".$data1[1]."/".$data1[2];
          return  $result;
        }

    }

    public function verificaAlteradata($dataDHCPLog, $dataBD){
        $data1=explode("/", $dataDHCPLog);
        $data2=explode("/", ((string)$dataBD));    
      

        if(($data1[0]!=$data2[0])&&(($data1[1]>$data2[1])||($data1[2]>=$data2[2]))){
          
          return  1;

        }if(($data1[0]==$data2[0])&&(($data1[1]>$data2[1])||($data1[2]>=$data2[2]))){
          
          return  1;

        }if(($data1[0]!=$data2[0])&&(($data1[1]>$data2[1])&&($data1[2]>=$data2[2]))){
          
          return  1;

        }if(($data1[0]!=$data2[0])&&(($data1[1]<$data2[1])&&($data1[2]>$data2[2]))){
          
          return  1;

        }else{
          $result=$data1[0]."/".$data1[1]."/".$data1[2];
          return  0;
        }

    }

    public function verifica_mes($date){
        $mes=0;
        if(!strcmp("Jan", $date)){
          $mes=1;

        }else if(!strcmp("Feb", $date)){
              $mes=2;
        }
        else if(!strcmp("Mar", $date)){
              $mes=3;
        }
        else if(!strcmp("Apr", $date)){
              $mes=4;
        }
        else if(!strcmp("May", $date)){
              $mes=5;
        }
        else if(!strcmp("June", $date)){
              $mes=6;
        }
        else if(!strcmp("July", $date)){
              $mes=7;
        }
        else if(!strcmp("Aug", $date)){
              $mes=8;
        }
        else if(!strcmp("Sept", $date)){
              $mes=9;
        }
        else if(!strcmp("Oct", $date)){
              $mes=10;
        }
        else if(!strcmp("Nov", $date)){
              $mes=11;
        }
        else if(!strcmp("Dec", $date)){
              $mes=12;
        }
         return $mes;
    }

    public function dif_datas($data_inicio, $data_fim){ 

        list($dia_i, $mes_i, $ano_i) = explode("/", $data_inicio); //Data inicial 
        list($dia_f, $mes_f, $ano_f) = explode("/", $data_fim); //Data final 
        $mk_i = mktime(0, 0, 0, $mes_i, $dia_i, $ano_i); 
        $mk_f = mktime(0, 0, 0, $mes_f, $dia_f, $ano_f); 
        
        $diferenca = $mk_f - $mk_i; //Acha a diferença entre as datas 
        
        if($diferenca == 0 ){ 
        return 'É a mesma data'; 
        }elseif($diferenca > 0 ){ 
          return $data_inicio; 
        }elseif($diferenca < 0 ){
          $data=explode("/", $data_inicio);
          $dataMax= $data[0]."/".$data[1]."/".(string)((int)$data[2]-1);
          return $dataMax; 
        } 
    } 
    public function geraTimestamp($data) {
      $partes = explode('/', $data);
      return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
    } 
    public function retornadias($data_inicio,$data_fim){
        // Usa a função criada e pega o timestamp das duas datas:
        $time_inicial = $this->geraTimestamp($data_inicio);
        $time_final = $this->geraTimestamp($data_fim);
       
        // Calcula a diferença de segundos entre as duas datas:
        $diferenca = $time_final - $time_inicial; // 19522800 segundos
      
        // Calcula a diferença de dias
        $dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
         
        // Exibe uma mensagem de resultado:
       // echo "A diferença entre as datas ".$data_inicio." e ".$data_fim." é de <strong>".$dias."            </strong> dias";
        return $dias;
    
   }
   
   public function dhcplogviewAction(Request $request){

       if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
       }
        //$message=$this->returnMenssage();
       
        //$this->get('session')->getFlashBag()->set('message', $message); 
       

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a order by a.mac";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();
       

        // parameters to template
        return $this->render('SytemSGBundle:DHCPLog:dhcplogview.html.twig', array(
        'pagination' => $query,
        'data'=> new \DateTime(),
        //'porcentagem'=>$percento,
        'value'=> 0,
        ));
        
        

   }
   public function resultupdateviewAction(Request $request){
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
        }


        date_default_timezone_set("America/Sao_Paulo");

        $message=$this->returnMenssage();       
        $this->get('session')->getFlashBag()->add('message', $message); 
        $mensage=$this->geraDateDHCPlog();
        $this->get('session')->getFlashBag()->add('message', $mensage);
        $this->atualizaStatusDHCPLOG();
        return $this->redirect($this->generateUrl('dhcplogview'));  
        
   }
   public function atualizaStatusDHCPLOG(){
    $em = $this->getDoctrine()->getManager();
    $entities = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll(); 

    foreach ($entities as $key => $value) {
        $data_inicio=$value->getDataDhcplog()->format('d/m/Y');// $date[1]."/".$mes."/".$todayI;
        $data_fim=date("d/m/Y");
        
        if(($this->retornadias($data_inicio,$data_fim)>=0) && ($this->retornadias($data_inicio,$data_fim)<=7)){
                     $value->setStatusdhcp('connected');
        }else if(($this->retornadias($data_inicio,$data_fim)>7) && ($this->retornadias($data_inicio,$data_fim)<=30)){
                     $value->setStatusdhcp('little_bit_connected');
        }else if(($this->retornadias($data_inicio,$data_fim)>30)){
                    $value->setStatusdhcp('no_connected');
        }
        $em->flush();
    }
    
   }

   public function connectedAction(Request $request){
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
           return $this->redirect($this->generateUrl('error'), 302);  
     }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.statusdhcp = 'connected' order by a.datadhcplog";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();
        
        // parameters to template
        return $this->render('SytemSGBundle:DHCPLog:result.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Usuarios que se conectaram nos ultimos 7 dias.",
         'row'=> 1,
        
        ));

    
    }

    public function noconnectedAction(Request $request){
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
          return $this->redirect($this->generateUrl('error'), 302);  
      }
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.statusdhcp = 'no_connected' order by a.datadhcplog  ";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();
        // parameters to template
        return $this->render('SytemSGBundle:DHCPLog:result.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Usuarios que não se conectaram a mais de 30 dias",
         'row'=> 2,
        
        ));

    
    }
    public function poucoconnectedAction(Request $request){
     if (!$this->get('security.context')->isGranted('ROLE_ADMIN')  ) {
          return $this->redirect($this->generateUrl('error'), 302);  
     }
                      
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM SytemSGBundle:DadosArquivos a where a.statusdhcp = 'little_bit_connected' order by a.datadhcplog ";  
        $query = $em->createQuery($dql);
        $query=$query->getResult();
        // parameters to template
        return $this->render('SytemSGBundle:DHCPLog:result.html.twig', array('pagination' => $query,
         'data'=> new \DateTime(),
         'status'=> " Usuarios que não se conectaram no periodo 7  a 30 dias.",
         'row'=> 3,
        
        ));

    
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

    public function ShelldowloadDHCPLOG(){
      $em = $this->getDoctrine()->getManager();
      $pathfile = $em->getRepository('SytemSGBundle:ArquivosRemotos')->findAll();
      $nomeArq=$pathfile[1]->getNomeArquivo();       
      $origem=$this->patchweb();
      $destino=$pathfile[1]->getCaminhoDestino().$nomeArq;
      $username=$pathfile[1]->getUsername();
      $ip=$pathfile[1]->getServerip(); 
      if(!$ip==""){
      

          join('/', array(trim(" ", '/'), trim($origem, '/')));    
          join('/', array(trim(" ", '/'), trim($destino, '/')));
          
          $cmd="scp ".$username."@".$ip.":".$destino." ".$origem;
          $shell=shell_exec($cmd);

          if(!$shell){
            
              return "O arquivo foi baixado com sucesso.".$cmd;
          }else{
            
              return "Falha ao baixar o arquivo.".$cmd;
          }
      }
      else{
        return "localiza-se na maquina local (Não o arquivo nao foi baixado de servidor externo).";
      }

    }
    
    public function returnMenssage(){
         $dhcplog=$this->ShelldowloadDHCPLOG();
         $mensage= "1 - Dhcplog: ".$dhcplog;
         return  $mensage;
    }

   


   
}