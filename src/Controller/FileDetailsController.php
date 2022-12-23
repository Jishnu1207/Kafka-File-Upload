<?php

namespace App\Controller;

use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\ErrorHandler\Error\UndefinedFunctionError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FileRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
class FileDetailsController extends AbstractController
{
    #[Route('/file', name: 'app_file')]
    public function Upload(Request $request,EntityManagerInterface $entityManager):Response
    {
            try
            {
                $UploadedFile = $request->files->get('file');
                if ( $UploadedFile == null)
                {
                    $message = 0;
                    $output = array('message'  => $message);
                    $response = new JsonResponse($output);
                    return $response;
                }
                else
                {
                    $csv = $request->files->get('file')->getClientOriginalName();
                    $Original_Filename = $request->files->get('file')->getClientOriginalName().time();
                    $status = 0;
                    $date = new \DateTime('@'.strtotime('now'));
                    $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                    $upload_success = $UploadedFile->move($destination, $UploadedFile->getClientOriginalName().time());
                    $rows = shell_exec("cat /home/vinuser/Documents/Kafka-File-Upload/public/uploads/$Original_Filename | wc -l");
                    $file = new File();
                    //$name =  $request->files->get('file')->getClientOriginalName().time();
                    $id = $file->getId();
                    $file->setFileName($csv);
                    $file->setStatus($status);
                    $file->setRecordCount($rows);
                    $file->setUploadedAt($date);
                    $file->setNewName($Original_Filename);
                    $entityManager->persist($file);
                    $entityManager->flush();
                   // kafka inserting
                    // $producer = new \RdKafka\Producer();
                    // $producer->setLogLevel(LOG_DEBUG);
                    // $producer->addBrokers("127.0.0.1");
                    // $topic = $producer->newTopic("Kafka-File");
                    $id = $file->getId();
                    // //$name =  $request->files->get('file')->getClientOriginalName().time();
                    // $decoded_array['id'] = $id;
                    // $decoded_array['name'] = $Original_Filename;
                    // $value = json_encode($decoded_array);
                    // $topic->produce(RD_KAFKA_PARTITION_UA, 0, $value);
                    // for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
                    //             $result = $producer->flush(10000);
                    //             if (RD_KAFKA_RESP_ERR_NO_ERROR == $result) {
                    //                 break;
                    //             }
                    // }
                    // if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
                    //             throw new \RuntimeException('Was unable to flush, messages might be lost!');
                    // }           
                    $message = 'file Uploaded';
                    $output = array(
                            'message'  => $message,
                            'id'=>$id,
                           
                    );
                    $response = new JsonResponse($output);
                    return $response;
                }
            }
            catch(\Exception $e)
            {
                $message =  $e->getMessage();
                $output = array(
                'message'  => $message,
                'error' =>$e
                );
                $response = new JsonResponse($output);
                return $response;
            }
    }
    #[Route('/mapping', name: 'app_mapping')]
    public function Mapping(Request $request,FileRepository $repo,KernelInterface $kernel):Response
    {
            $message = 'file id retrived';
            $id = $request->getContent();
            $name = $repo->fetchname($id);
            //$finder = new Finder();
            $this->projectDir = $kernel->getProjectDir();
            $path = $this->projectDir.'/public/uploads/'.$name;
            //$finder->files()->in($this->projectDir.'/public/uploads/')->name($name);
            
            $file = fopen($path,'r');
            //$lines = fgets($file);
            $lines = fgetcsv($file);
            $output=array(
                'lines'=>$lines
            );
            $response = new JsonResponse($output);
            return $response;
    }
     #[Route('/save', name: 'app_save')]
     public function save(Request $request,FileRepository $repo):Response
     {
        try{
                $content = json_decode($request->getContent());
                $id = $content->id;
                $mapping = json_encode($content->mapping);
                $repo->saveMap($id,$mapping);
                $message = 'mapping details saved';
                $producer = new \RdKafka\Producer();
                $producer->setLogLevel(LOG_DEBUG);
                $producer->addBrokers("127.0.0.1");
                $topic = $producer->newTopic("Kafka-File");
                //$id = $file->getId();
                //$name =  $request->files->get('file')->getClientOriginalName().time();
                $decoded_array['id'] = $id;
                
                $decoded_array['name'] =$repo->fetchname($id);
                $value = json_encode($decoded_array);
                $topic->produce(RD_KAFKA_PARTITION_UA, 0, $value);
                for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
                            $result = $producer->flush(10000);
                            if (RD_KAFKA_RESP_ERR_NO_ERROR == $result) {
                                break;
                            }
                }
                if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
                            throw new \RuntimeException('Was unable to flush, messages might be lost!');
                }     
                $output = array(
                    'message' =>$message,
                    'mapping'  => $mapping,
                    'id' =>$id
                    );
                $response=new JsonResponse($output);
                return $response;
        }
        catch(\Exception $e)
            {
                $message =  $e->getMessage();
                $output = array(
                    'message' =>$message,
                    'exception' =>$e
                    );
                $response = new JsonResponse($output);
                return $response;
               
            }  
    }
}