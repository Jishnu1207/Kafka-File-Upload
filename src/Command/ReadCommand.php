<?php

namespace App\Command;

use App\Entity\Contacts;
use App\Entity\File;
use App\Repository\ContactsRepository;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\While_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

use function PHPSTORM_META\map;

class ReadCommand extends Command
{
    protected static $defaultName = 'Read';
    protected static $defaultDescription = 'Read File Data and insert into table';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager,KernelInterface $kernel)
    {   
        $this->entityManager = $entityManager;
        $this->projectDir = $kernel->getProjectDir();

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conf = new \RdKafka\Conf();

        $conf->set('metadata.broker.list', '127.0.0.1');

        $conf->set('group.id', 'group1');

        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new \RdKafka\KafkaConsumer($conf);

        $consumer->subscribe(['Kafka-File']);

        $batch=0;

        $skip=0;

        while (true) 
        {
            $message = $consumer->consume(5*1000);

            switch ($message->err) 
            {
                case RD_KAFKA_RESP_ERR_NO_ERROR:

                    $repo=$this->entityManager->getRepository(File::class);

                    $repo2=$this->entityManager->getRepository(Contacts::class);

                    $obj=json_decode($message->payload);

                    $id=$obj->id;

                    $fname=$obj->name;

                    $map=$repo->fetchMap($id);

                    $len=sizeof($map);

                    $key=array_search('email',$map);

                    $path=$this->projectDir.'/public/uploads/'.$fname;

                    $finder= new Finder();

                    $finder->files()->in($this->projectDir.'/public/uploads/')->name($fname);

                    if($finder->hasResults())
                    {
                        $file=fopen($path,'r');
                        while(!feof($file))
                        {
                            $row=fgetcsv($file);
                            if($row)
                            { 
                            $email=$row[$key];
                            $echeck=$repo2->checkMail($email,$row,$map);
                            if($echeck)
                            {
                                $skip++;
                            }
                            else
                            {
                            // if($this->mxValidation($email))
                            // {
                                $contacts=new Contacts();

                                for( $i=0; $i<$len; $i++)
                                {
                                    if( (!empty($map[$i])) && (!empty($row[$i])))
                                    {
                                        switch($map[$i])
                                        {
                                            case "first_name" :
                                                $contacts->setFirstName($row[$i]);
                                                break;
                                            case "last_name" :
                                                $contacts->setLastName($row[$i]);
                                                break;
                                            case "email" :
                                                $contacts->setEmail($row[$i]);
                                                break;
                                            case "company_name" :
                                                $contacts->setCompanyName($row[$i]);
                                                break; 
                                            case "city" :
                                                $contacts->setCity($row[$i]);
                                                break;  
                                            case "zip" :
                                                $contacts->setZip($row[$i]);
                                                break; 
                                            case "phone" :
                                                $contacts->setPhone($row[$i]);
                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                }
                                $date=new \DateTime('now');
                                $contacts->setCreatedDate($date);
                                $contacts->setFileId($id);
                                $this->entityManager->persist($contacts);
                            // }
                                $batch++;
                                if($batch==3)
                                {
                                    $batch=0;
                                    $this->entityManager->flush();
                                }
                        }
                            }
                        }
                    }

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:

                    echo "No more messages; will wait for more\n";

                    $this->entityManager->flush();

                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:

                    echo "Timed out\n";
                    
                    break;

                default:

                    throw new \Exception($message->errstr(), $message->err);

                    break;
            }
        }
    }
    public function mxValidation($email)
    {
        if(checkdnsrr($email,'MX'))
        {
            $val=true;
        }
        else
        {
            $val=false;
        }
        return $val;
    }
    
    
}




// $contacts->setFirstName($fread[0]);
            // $contacts->setLastName($fread[1]);
            // $contacts->setEmail($fread[2]);
            // $contacts->setCompanyName($fread[3]);
            // $contacts->setCity($fread[4]);
            // $contacts->setZip($fread[5]);
            // $contacts->setPhone($fread[6]);
            // $contacts->setCreatedDate($date);