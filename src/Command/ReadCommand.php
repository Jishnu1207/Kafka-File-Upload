<?php

namespace App\Command;

use App\Entity\Contacts;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
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
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumer = $this->kafkaConfig();

        while (true) 
        {
            $message = $consumer->consume(5*1000);

            switch ($message->err) 
            {
                case RD_KAFKA_RESP_ERR_NO_ERROR:

                    $repo = $this->entityManager->getRepository(File::class);

                    $repo2 = $this->entityManager->getRepository(Contacts::class);

                    $obj = json_decode($message->payload);

                    $id = $obj->id;

                    $fname = $obj->name;

                    $map = $repo->fetchMap($id);

                    $key = $this->fetchMail($map);

                    if (!empty($key))
                    {
                    $finder=$this->fetchFile($fname);

                    $path = $this->projectDir.'/public/uploads/'.$fname;

                    if ($finder->hasResults())
                    {
                        $batch = 0;
                        $skip = 0;
                        $file = fopen($path, 'r');
                        while (!feof($file))
                            {
                                $row = fgetcsv($file);
                                if ($row)
                                    { 
                                        $email = $row[$key];
                                        $echeck = $repo2->checkMail($email,$row,$map);
                                        if ($echeck)
                                            {
                                                $skip++;
                                            }
                                        else
                                            {
                                            // if($this->mxValidation($email))
                                            // {
                                                $batch=$this->dbInsertion($map, $row, $batch, $id);
                                            // }
                                            }
                                    }
                            }
                            if ($batch!=0)
                            {
                                $this->entityManager->flush();
                            }
                            echo "CSV Data Inserted Successfully!!!";
                    }
                    else
                    {
                        echo "File Not Found!!!";
                    }
                }
                else
                {
                    echo "Email Field Not Found!!!\n";
                }

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:

                    echo "No more messages; will wait for more\n";

                    // $this->entityManager->flush();

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



    public function kafkaConfig()
    {
        $conf = new \RdKafka\Conf();

        $conf->set('metadata.broker.list', '127.0.0.1');

        $conf->set('group.id', 'group1');

        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new \RdKafka\KafkaConsumer($conf);

        $consumer->subscribe(['Kafka-File']);

        return $consumer;

    }



    public function mxValidation($email)
    {
        if (checkdnsrr($email, 'MX'))
        {
            $mx = true;
        }
        else
        {
            $mx = false;
        }
        return $mx;
    }



    public function fetchFile($fname)
    {
        $finder = new Finder();

        $finder->files()->in($this->projectDir.'/public/uploads/')->name($fname);
        
        return $finder;
    }
    


    public function dbInsertion($map, $row, $batch, $id): int
    {
        $len = sizeof($map);

        $contacts = new Contacts();

        for ($i = 0; $i < $len; $i++) 
        {
            if ((!empty($map[$i])) && (!empty($row[$i]))) 
            {
                switch ($map[$i]) 
                {
                    case "first_name":
                        $contacts->setFirstName($row[$i]);
                        break;
                    case "last_name":
                        $contacts->setLastName($row[$i]);
                        break;
                    case "email":
                        $contacts->setEmail($row[$i]);
                        break;
                    case "company_name":
                        $contacts->setCompanyName($row[$i]);
                        break;
                    case "city":
                        $contacts->setCity($row[$i]);
                        break;
                    case "zip":
                        $contacts->setZip($row[$i]);
                        break;
                    case "phone":
                        $contacts->setPhone($row[$i]);
                        break;
                    default:
                        break;
                }
            }
        }
        $date = new \DateTime('now');

        $contacts->setCreatedDate($date);

        $contacts->setFileId($id);

        $this->entityManager->persist($contacts);

        $batch++;

        if ($batch == 500)
        {
            $batch = 0;
            $this->entityManager->flush();
        }
        return $batch;
    }



    public function fetchMail($map)
    {
        $key=array_search('email',$map);

        if(!empty($key))
        {
            return $key;
        }
        else
        {
            return $key;
        }
    }
}