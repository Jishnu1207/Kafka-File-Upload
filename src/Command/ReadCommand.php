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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conf = new \RdKafka\Conf();

        $conf->set('metadata.broker.list', '127.0.0.1');

        $conf->set('group.id', 'group1');

        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new \RdKafka\KafkaConsumer($conf);

        $consumer->subscribe(['Kafka-File']);

        while (true) 
        {
            $message = $consumer->consume(5*1000);

            switch ($message->err) 
            {
                case RD_KAFKA_RESP_ERR_NO_ERROR:

                    $repo=$this->entityManager->getRepository(File::class);

                    $obj=json_decode($message->payload);

                    $batch=0;

                    $id=$obj->id;

                    $fname=$obj->name;

                    $map=$repo->fetchMap($id);

                    $key=array_search('email',$map);

                    $path=$this->projectDir.'/public/uploads/'.$fname;

                    $finder= new Finder();

                    $finder->files()->in($this->projectDir.'/public/uploads/')->name($fname);

                    $str2=$this->insertion($map);

                    if($finder->hasResults())
                    {
                        $file=fopen($path,'r');
                        while(!feof($file))
                        {
                            $row=fgetcsv($file);
                            if($row)
                            {
                            $email=$row[$key];

                            if($this->mxValidation($email,$batch));
                            {
                                foreach($row as $r)
                                {
                                    $str2 .= $r . ",";
                                }
                                    $str2=trim($str2,",");
    
                                    $str2.="),(";
                            }
                            }
                        }
                        $str2=trim($str2,",(");
                    }
                    else
                    {
                        return false;;
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
    public function mxValidation($email,$batch)
    {
        if(checkdnsrr($email,'MX'))
        {
            $batch++;
            $val=true;
        }
        else
        {
            $val=false;
        }
        return $val;
    }
    public function insertion($map)
    {
        $str="INSERT INTO contacts(";

        foreach($map as $m)
        {   
            $str .= $m . ",";
        }
        $str=trim($str,",");

        $str.=") VALUES (";

        print_r($str);
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