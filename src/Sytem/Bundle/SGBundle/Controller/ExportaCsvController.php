<?php
namespace Sytem\Bundle\SGBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Connection;

class ExportaCsvController extends Controller{



  public function exportarAction(){
    $session = $this->getRequest()->getSession();
    if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
         return $this->redirect($this->generateUrl('error'), 302);  
    }
    
    return $this->render('SytemSGBundle:ExportaCsv:exportar.html.twig',array(
        'data'=> new \DateTime(),
        
    ));

  }

  public function exportarcsvAction(Request $request){
  	$session = $this->getRequest()->getSession();
    if (!$this->get('security.context')->isGranted('ROLE_ADMIN') ) {
         return $this->redirect($this->generateUrl('error'), 302);  
    }

  	if($request->getMethod()=='GET'){
  		      $table=$_GET['table'];
            $response=$this->geraCSV($table);
            return $response;
        
    }
    $this->get('session')->getFlashBag()->set('message','Error ao gerar arquivo.');
       
  	return $this->redirect('exportar'); 

  }
  /* Esta função gera o arquivo csv para as classes Dnsips, DadosArquivos, ArquivosRemotos,
     Dhcpconf, ServidorLdap.
  */

  public function geraCSV($table){

    $em = $this->getDoctrine()->getManager();
    $handle = fopen('php://output', 'w+');
    if($table=='Dnsips'){
          $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){ 
                $valor=$this->regex(explode(',', $row->getIpdns()));
                $lista[$i]=utf8_decode($valor);
                $i++;
          }
          fputcsv($handle, explode(",",'ipdns'), $delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador);
          }
          
          fclose($handle);
      }else if($table=='Grupos'){
          $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){ 
                $valor=$this->regex(explode(',', $row->getGrupo()));
                $lista[$i]=$valor;
                $i++;
          }
          fputcsv($handle, explode(",",'codigo grupo'), $delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador);
          }
          
          fclose($handle);
      }else if($table=='DadosArquivos'){
          $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){
            $valorIp=$this->regex(explode(',', $row->getIp()));
            $valorMac=$this->regex(explode(',', $row->getMac()));
            $valorTipo=$this->regex(explode(',', $row->getTipo()));
            $valorPatrimonioLocal=$this->regex(explode(',', $row->getPatrimonioLocal()));
            $valorLaboratorio=$this->regex(explode(',', $row->getLaboratorio()));
            $valorResponsavel=$this->regex(explode(',', $row->getResponsavel()));
            $valorUsuario=$this->regex(explode(',', $row->getUsuario())); 
            $valorDescricao=$this->regex(explode(',', $row->getDescricao()));
            
            
            $lista[$i]=utf8_decode($valorIp.','.$valorMac.','.$valorTipo.','.$valorPatrimonioLocal.','.$valorLaboratorio.','.$valorResponsavel.','.$valorUsuario.','.$valorDescricao);
            $i++;
          }
          fputcsv($handle, explode (",",'ip,mac,tipo,patrimoniolocal,laboratorio,responsavel,usuario,descrição'),$delimitador);
          foreach ($lista as $line)
          {
               fputcsv($handle,explode(',', $line),$delimitador);
          }
          
          fclose($handle);

      }else if($table=='ArquivosRemotos'){

          $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){
            $valorServerip=$this->regex(explode(',', $row->getServerip()));
            $valorNomeArquivo=$this->regex(explode(',', $row->getNomeArquivo()));
            $valorUsername=$this->regex(explode(',', $row->getUsername()));
            $valorCaminhoDestino=$this->regex(explode(',', $row->getCaminhoDestino()));
            $lista[$i]=utf8_decode($valorServerip.','.$valorNomeArquivo.','.$valorUsername.','.$valorCaminhoDestino);
            $i++;
          }
          fputcsv($handle, explode(",",'serverip,nomeArquivo,username,caminhoDestino'),$delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador);
          }
          
          fclose($handle);

      }else if($table=='Dhcpconf'){
         $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){
              
            $valorOptionrouters=$this->regex(explode(',', $row->getOptionrouters()));

            $lista[$i]=utf8_decode($valorOptionrouters); 
            $i++;
          }
          fputcsv($handle,explode(",",'optionrouters'), $delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador );
          }
          
          fclose($handle);


      }else if($table=='Laboratorios'){
         $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){
              
            $valorOptionrouters=$this->regex(explode(',', $row->getNomeLaboratorio()));
            $valorBloco=$this->regex(explode(',', $row->getBloco()));
            $valorSala=$this->regex(explode(',', $row->getSala()));

            $lista[$i]=utf8_decode($valorOptionrouters.','.$valorBloco.','.$valorSala); 
            $i++;
          }
          fputcsv($handle,explode(",",'nomelaboratorio,bloco,sala'), $delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador );
          }
          
          fclose($handle);


      }
      else if($table=='ServidorLdap'){
         $results = $em->getRepository('SytemSGBundle:'.$table)->findAll();
        
          $delimitador=';';
          $i=0;
          
          foreach ( $results as $key => $row){
            $valorIpLdap=$this->regex(explode(',', $row->getIpLdap()));
            $valorComplemento=$this->regex(explode(',', $row->getComplemento()));
            $valorUsername=$this->regex(explode(',', $row->getUsername()));
            $valorPassword=$this->regex(explode(',', $row->getPassword()));
            
            $lista[$i]=utf8_decode($valorIpLdap.','.$valorComplemento.','.$valorUsername.','.$valorPassword);
            $i++;
          }
          fputcsv($handle,explode(",",'ipLdap,complemento,username,password'),$delimitador);
          foreach ($lista as $line)
          {
            fputcsv($handle,explode(',',$line),$delimitador);
          }
          
          fclose($handle);


      }
      $response = $this->render('SytemSGBundle:ExportaCsv:exportarcsv.html.twig',array(
      'data'=> new \DateTime(),
      'database' =>$handle,
        
      ));
        
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename='.$table.'.csv');
        $response->headers->set('Content-Description', 'Submissions Export');       
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        //$response->prepare();
        $response->sendHeaders();
        $response->sendContent();
        //$this->get('session')->getFlashBag()->set('message','Arquivo gerado com sucesso!!!');
        return $response;
  }

  public function regex($valueS){
       $string=null; 
         
       foreach ($valueS as $key => $value) {
          $string.=$value.' ';
       }
    return $string;
 }


}


