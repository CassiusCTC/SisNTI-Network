<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sytem\Bundle\SGBundle\Entity\Dnsips;
use Sytem\Bundle\SGBundle\Form\DnsipsType;
use Sytem\Bundle\SGBundle\Entity\DadosArquivos;
use Sytem\Bundle\SGBundle\Form\DadosArquivosType;
use Sytem\Bundle\SGBundle\Entity\Laboratorios;
use Sytem\Bundle\SGBundle\Form\LaboratoriosType;
use Sytem\Bundle\SGBundle\Entity\Intervalosips;
use Sytem\Bundle\SGBundle\Form\IntervalosipsType;
use Sytem\Bundle\SGBundle\Entity\Grupos;
use Sytem\Bundle\SGBundle\Form\GruposType;

use Sytem\Bundle\SGBundle\Form\UsuariosystemType;

/*Esta classe ela trata o importação dos dados para o banco, não esta relacionado ao upload de arquivos referentes aos termos de compromisso*/

class ImportarCsvController extends Controller{

   public function uploadAction(){
    
    $session = $this->getRequest()->getSession();
    if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
         return $this->redirect($this->generateUrl('error'), 302);  
    }
    
    return $this->render('SytemSGBundle:ImportarCsv:upload.html.twig',array(
        'data'=> new \DateTime(),
        
    ));

  }

  public function importarAction(Request $request){

  	$session = $this->getRequest()->getSession();
    if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
         return $this->redirect($this->generateUrl('error'), 302);  
    }

  	if($request->getMethod()=='POST'){
  		$csv=$_FILES['arquivo'];
      $option=$request->request->get('option');
       if(!empty($csv)){
          //echo $status='sucesso';exit();
		          $nomefile=$_FILES['arquivo']['tmp_name']; 
              $nomeoriginalfile=$_FILES['arquivo']['name']; 
              $auxname=explode('.', $nomeoriginalfile);
              $extencao=$auxname[sizeof($auxname)-1];
              if($extencao=='csv'){
    		          $menssage=$this->importarcsvdatabase($nomefile,$option);
                  $this->get('session')->getFlashBag()->set('message',$menssage);           
                  return $this->redirect('upload'); 
              }
              else {
                $this->get('session')->getFlashBag()->set('message','O arquivo '.$nomeoriginalfile.' não podera ser importado pois a extenção do arquivo não é suportada.');           
                return $this->redirect('upload'); 

              }
  		}else{
           	
         $this->get('session')->getFlashBag()->set('message','Error no upload do arquivo.');       
         return $this->redirect('upload'); 
  		}
    }

  	
    $this->get('session')->getFlashBag()->set('message','Error ao importar o arquivo.');       
    return $this->redirect('upload'); 
  }

  public function importarcsvdatabase($namefile,$option) 
  {
  	$em = $this->getDoctrine()->getManager(); 
    if($option=='Dnsips'){
      $handle = fopen($namefile, "r");
      $c=count(file($namefile));
      if( $c>0 ){ 
          $i=0;$x=0;
          $array=array();
          while (($data = fgetcsv($handle, 1000, ";")) !== FALSE){  
            if($x>0){
               if(count($data)==1){   
                 $array[$i]=$data[0]; 
                 $i++; 
               }
            }$x++;     
          }
          $total=0;$totalimport=0;
          foreach ($array as $key => $value2) {
            
                   if (preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',trim($value2), $matches )) {
                    
                  
                      if(!$em->getRepository('SytemSGBundle:Dnsips')->findOneByIpdns($value2)){
                       
                            if(strlen(trim($value2))<=15 && strlen(trim($value2))>=7){ 
                                 
                                 $value= new Dnsips();                
                                 $registro=$this->regex(explode(',', $value2));
                                 $value->setIpdns($registro);
                                 $em->persist($value);
                                 $em->flush(); 
                                 $totalimport++;
                            }
                       }
                   }
                   $total++;    
           }  
          // exit;
         fclose($handle);
         if($totalimport>0){
             return 'A tabela foi importada '.$option.' com sucesso. Foram importados '.$totalimport.' de um total de '.$total;
         }else{
             return 'Não foram importados nenhum registro.';
         }
       
    }else{
            return 'Erro: formato do arquivo '.$option.' não pode ser lido, delimitadores aceitados ( ; e , ).'; 
    }

   }else if($option=='Grupos'){
      $handle = fopen($namefile, "r");
      $em = $this->getDoctrine()->getManager(); 
      $c=count(file($namefile));
      if( $c>0 ){ 
          $i=0;$x=0;
          $array=array();
          while (($data = fgetcsv($handle, 1000, ";")) !== FALSE){  
               if($x>0){
                 if(count($data)==1){       
                   $array[$i]=$data[0]; 
                   $i++;
                 }
               }$x++; 

          }
        $total=0;$totalimport=0;
          
          foreach ($array as $key => $value2) {
                $registro=$this->regex(explode(',', $value2));
                if(is_numeric(trim($value2))){
                    if(!$em->getRepository('SytemSGBundle:Grupos')->findOneByGrupo($value2)){
                             $value= new Grupos();
                             $totalregistrados++;
                             $value->setGrupo($registro);
                             $em->persist($value);
                             $em->flush(); 
                             $totalimport++;
                    }
                }
                $total++;      
           }  
         fclose($handle);
         
         if($totalimport>0){
             return 'A tabela foi importada '.$option.' com sucesso. Foram importados '.$totalimport.' de um total de '.$total;
         }else{
             return 'Não foram importados nenhum registro.';
         }
    }else{
            return 'Erro: formato do arquivo '.$option.' não pode ser lido, delimitadores aceitados ( ; e , ).'; 
    }

   }else if($option=='Laboratorios'){
      $handle = fopen($namefile, "r");
      $c=count(file($namefile));
      if( $c>0 ){ 
          $i=0;$x=0;
        $array=array();
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE){
              if($x>0){
                 if(count($data)==3){                     
                       $array[$i]= utf8_decode($data[0].';'.$data[1].';'.$data[2]);
                       $i++;
                 }                   
              }    
                $x++;
        }
        $array_count=array_count_values($array);
           $total=0;$totalimport=0;
        $em = $this->getDoctrine()->getManager();      
        foreach ($array as $key=>$values) {
                     $d=explode(';', $values);                    
                     $lab=$this->regex(explode(',', $d[0]));
                     $bloco=$this->regex(explode(',',$d[1]));
                     $sala=$this->regex(explode(',', $d[2]));                               
                     if(!$em->getRepository('SytemSGBundle:Laboratorios')->findOneByNomeLaboratorio($lab)){
                        if((strlen( $lab)<=200) && (strlen($bloco)<=200) && (strlen($sala)<=60)){
                          if( $lab!=' ' && $bloco!=' ' && $sala!=' '){
                            $value= new Laboratorios(); 
                            $value->setNomeLaboratorio($lab);
                            $value->setBloco($bloco);
                            $value->setSala($sala);
                            $em->persist($value);
                            $em->flush(); 
                            $totalimport++;
                        }
                      }
                     }
                     $total++;
         }
         fclose($handle);
         if($totalimport>0){
             return 'A tabela foi importada '.$option.' com sucesso. Foram importados '.$totalimport.' de um total de '.$total;
         }else{
             return 'Não foram importados nenhum registro.';
         }
        
    }else{
            return 'Erro: formato do arquivo '.$option.' não pode ser lido, delimitadores aceitados ( ; e , ).'; 
    }
   }else if($option=='DadosArquivos'){
     $handle = fopen($namefile, "r");
      $em = $this->getDoctrine()->getManager();       
      $c=count(file($namefile));
      if( $c>0 ){ 
          $i=0;$x=0;
        $array=array();
        $total=0;
        $totalregistrados=0;
        $user = $this->get('security.context')->getToken()->getUser();
        date_default_timezone_set("UTC");

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE){
           
           if(count($data)==8){               
              if($x>0){            
                $array[$i]=$data[0].';'.$data[1].';'.$data[2].';'.$data[3].';'.$data[4].';'.$data[5].';'.$data[6].';'.$data[7];       
                $i++;
              }$x++;
           }else{
              return 'A tabela '.$option.'não foi importada erro no numero de colunas';
              break;
           }
        } 
      
        foreach ($array as $key => $row){
            $d=explode(';', $row); 
            $valorIp=$this->regex(explode(',', $d[0]));
            $valorMac=$this->regex(explode(',', $d[1]));
            $valorTipo=$d[2];
            $valorPatrimonioLocal=$this->regex(explode(',', $d[3]));
            $valorLaboratorio=$this->regex(explode(',', $d[4]));            
                       
            $valorResponsavel=$this->regex(explode(',', $d[5]));

            if(strlen($d[6])<11){
              $valorUsuario='0'.$d[6]; 
            }else{
              $valorUsuario=$this->regex(explode(',', $d[6]));; 
            }
              
            //$valorStatus=$this->regex(explode(',', $d[8]));
            $valorDescricao=$this->regex(explode(',', $d[7]));
            $lab=$em->getRepository('SytemSGBundle:Laboratorios')->findOneByNomeLaboratorio(trim($valorLaboratorio));
            $resp=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf(trim($valorResponsavel));
            
            $usuario=$em->getRepository('SytemSGBundle:Usuariosystem')->findOneByCpf(trim($valorUsuario));
            
            $tipo=$em->getRepository('SytemSGBundle:Tipos')->findOneByDescricao(trim($valorTipo));
            
            if($this->validardadosaquivo($valorIp,$valorMac)!==TRUE){
             if(!$em->getRepository('SytemSGBundle:DadosArquivos')->findOneByIp(trim($valorIp))&&!$em->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac(trim( $valorMac))){
             
              if(!is_null($lab)&& !is_null($tipo)){ 
                 
                  if(!is_null($resp) && !is_null($usuario)){
                    
                      if(!is_null($valorDescricao)){
                         
                           if((strlen(trim($valorIp))<=15 && strlen(trim($valorIp))>=7)&&(strlen(trim($valorMac))==17)&&(strlen(trim($valorPatrimonioLocal))<=50)){ 
                                   
                                 $value= new DadosArquivos();                     
                                 $value->setIp($this->regex(explode(',', $valorIp)));
                                 $value->setMac($this->regex(explode(',', $valorMac)));
                                 $value->setTipo($tipo);
                                 $value->setPatrimonioLocal($this->regex(explode(',', $valorPatrimonioLocal)));
                                 $value->setLaboratorio($lab);
                                 $value->setResponsavel($resp);
                                 $value->setUsuario($usuario);
                                 $value->setDescricao($this->regex(explode(',', $valorDescricao)));
                                 $value->setDataCadastro(new \DateTime);
                                 $value->setDataDhcplog(new \DateTime);
                                 $value->setStatus('DESBLOQUEADO');
                                 $value->setStatusdhcp('disable');
                                 $em->persist($value);
                                 $em->flush(); 
                                 $totalregistrados++;
                          }
                   }
                 
                }
               
              }
            }
            }
            $total++;
           
          }   
     
      
        fclose($handle);
        if($total==$totalregistrados){
          

          return 'A tabela foi importada '.$option.' com sucesso foram importados '.$totalregistrados.' registros de '.$total.' total.';

        }else{
            
           return 'A tabela foi importada '.$option.' com sucesso foram importados '.$totalregistrados.' registros de '.$total.' total. Porem alguns registros foram descartados pois nao se adequavam a validação dos campos.';
        }
    }
    else{
            return 'Erro: formato do arquivo '.$option.' não pode ser lido, delimitadores aceitados ( ; e , ).'; 
    }


   }else if($option=='Intervalosips'){

      $handle = fopen($namefile, "r");
      $em = $this->getDoctrine()->getManager(); 
      
      $c=count(file($namefile));
      if( $c>0 ){ 
          $i=0;$x=0;
          $array=array();
          while (($data = fgetcsv($handle, 1000, ";")) !== FALSE){  
              if($x>0){
               if(count($data)==1){       
                   $array[$i]=$data[0]; 
                   $i++;   
                }
              } $x++;     
          }
           $total=0;$totalimport=0;
          foreach ($array as $key => $value2) {
                  
                    if (preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$value2, $matches )) {
                      if(!($ips = $em->getRepository('SytemSGBundle:Intervalosips')->findOneByIntervalo($value2))){
                             $registro=$this->regex(explode(',', $value2));
                             $value= new Intervalosips();
                             $value->setIntervalo($registro);
                             $em->persist($value); 
                             $em->flush(); 
                             $totalimport++;
                      }
                   } 
                   $total++;
           }  
           
         fclose($handle);
        
         if($totalimport>0){
             return 'A tabela foi importada '.$option.' com sucesso. Foram importados '.$totalimport.' de um total de '.$total;
         }else{
             return 'Não foram importados nenhum registro.';
         }
    
    }else{
            return 'Erro: formato do arquivo '.$option.' não pode ser lido, delimitadores aceitados ( ; e , ).'; 
    }
  }
     

  }
 
   public function verificaIP($handle){

       $array=array();
       $j=0;
       $i=0;
       foreach ($handle as $key => $value) {
         # code...
       
           

                       $array[$i]=$value;
                       $i++;  
              
            
              
       }
       $count=count($array);
       if($j==$count){
          $soma=0;
          $values=array_count_values($array);
          foreach ($values as $key => $value) {
            $soma+=$value;
          }
          if($soma!=$count){
              return TRUE;
          }else if(($this->verificaIPdatabase($array)==TRUE)&&($soma==$count)){
              return TRUE;
          }else if(($this->verificaIPdatabase($array)==TRUE)&&($soma!=$count)){
              return TRUE;
          }else if(($this->verificaIPdatabase($array)==TRUE)){
              return TRUE;
          }else if($soma==$count){
            return FALSE;
          }
         
       }else{
         return TRUE;
       }
  }

   public function verificaIPdatabase($array){

       $array2=array();
       $j=0;
       $i=0;
       $em = $this->getDoctrine()->getManager(); 
       $ips = $em->getRepository('SytemSGBundle:Dnsips')->findAll();
       foreach ($ips as $key => $value) {
           $array2[$i]= $value->getIpdns();
           $i++;       
       }      
       foreach ($array as $key => $value) {
          if(in_array( $value,$array2)){
              return TRUE;
              break;
          }
       }
       return FALSE;
  }
  public function verificaNull($array){

    foreach ($array as $key => $value) {
          if($value==null){
              return TRUE;
              break;
          }else if($value==''){
              return TRUE;
              break;
          }
       }
        return FALSE;
         

  }
  public function verificaMacdatabase($array){

       $array2=array();
       $j=0;
       $i=0;
       $em = $this->getDoctrine()->getManager();
       $ips = $em->getRepository('SytemSGBundle:DadosArquivos')->findAll();
       foreach ($ips as $key => $value) {
           $array2[$i]= $value->getMac();
           $i++;       
       }      
       foreach ($array as $key => $value) {
          if(in_array($array2, $value)){
              return TRUE;
              break;
          }
       }
       return FALSE;
  
}

public function verificaMac($handle){

       $array=array();
       $j=0;
       $i=0;
      foreach ($handle as $key => $value) {
        # code...
      
         if (preg_match('/^([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2})$/',$value, $matches )) {              
                       $array[$i]=$value;
                       $i++;  
         }
              $j++;     
       }
       $count=count($array);
       if($j==$count){
          $soma=0;
          $values=array_count_values($array);
          foreach ($values as $key => $value) {
            $soma+=$value;
          }
          if($soma!=$count){
              return TRUE;
          }else if(($this->verificaMacdatabase($array)==TRUE)&&($soma==$count)){
              return TRUE;
          }else if(($this->verificaMacdatabase($array)==TRUE)&&($soma!=$count)){
              return TRUE;
          }else if(($this->verificaMacdatabase($array)==TRUE)){
              return TRUE;
          }else if($soma==$count){
            return FALSE;
          }
        
       }else{
         return TRUE;
       }
  }

  public function verificaRepetidos($array){
    $variable=array_count_values($array);
    foreach ($variable as $key => $value) {
        if($value>1){
          return TRUE;
          break;

        }
    }
    return FALSE;

  }
public function verificaLabdatabase($array){

       $array2=array();
       $j=0;
       $i=0;
       $em = $this->getDoctrine()->getManager();
       $ips = $em->getRepository('SytemSGBundle:Laboratorios')->findAll();
       foreach ($ips as $key => $value) {
           $array2[$i]= $value->getNomeLaboratorio();
           $i++;       
       }      
       foreach ($array as $key => $value) {
          if(in_array($array2, $value)){
              return TRUE;
              break;
          }
       }
       return FALSE;
  
}

public function verificaLab($handle){

       $array=array();
       $j=0;
       $i=0;
       foreach ($handle as $key => $value) {
           if (($value!='')||($value!=null)){               
                       $array[$i]=$value;
                       $i++;  
              }
              $j++;     
       }
       $count=count($array);
       if($j==$count){
          $soma=0;
          $values=array_count_values($array);
          foreach ($values as $key => $value) {
            $soma+=$value;
          }
          if($soma!=$count){
              return TRUE;
          }else if(($this->verificaLabdatabase($array)==TRUE)&&($soma==$count)){
              return TRUE;
          }else if(($this->verificaLabdatabase($array)==TRUE)&&($soma!=$count)){
              return TRUE;
          }else if(($this->verificaLabdatabase($array)==TRUE)){
              return TRUE;
          }else if($soma==$count){
            return FALSE;
          }
       
       }else{
         return TRUE;
       }
  }

  public function regex($valueS){
       $string=null; 
         
       foreach ($valueS as $key => $value) {
          $string.=$value.' ';
       }
    return $string;
  }
 public function validardadosaquivo($value,$value2){
  $em = $this->getDoctrine()->getManager();

  if (preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',trim($value),$matches)) {
              
              $ipValidate=explode(".",$value);
              $ipValidate[0];
              $ipValidate[1];
              $ipValidate[2];
              $it=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
              $ipValidate[3];            
              if((strcmp($ipValidate[0],"000")|| strcmp($ipValidate[0],"00")|| strcmp($ipValidate[0],"0"))&&($ipValidate[3]<=254 || $ipValidate[3]>0)){                         
                 
                  $ips = $em->getRepository('SytemSGBundle:DadosArquivos')->findOneByIp($value);
                  $intervalo=$em->getRepository('SytemSGBundle:Intervalosips')->findOneByIntervalo($it);
                  
                  if (preg_match('/^([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2})$/',trim($value2), $matches )) {  
                     $mac = $em->getRepository('SytemSGBundle:DadosArquivos')->findOneByMac($value2);
                  }else{
                      return TRUE;
                  }
                  
                  if($intervalo==null){
                      return TRUE;
                  }else if(($ips!=null) && ($mac!=null) && ($intervalo==null)){
                      return TRUE;
                  }else if((($ips!=null) || ($mac!=null)) && ($intervalo==null)){
                      return TRUE;
                  }else if(($ips==null) && ($mac!=null)&&($intervalo!==null)){
                    return FALSE;
                  
                  }
               }
             
    } else{
      return TRUE;
    }

}


}


