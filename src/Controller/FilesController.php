<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FileRepository;
use App\Repository\ContactsRepository;

class FilesController extends AbstractController
{
    #[Route('/files', name : 'app_files')]
    public function fileList(FileRepository $repo, Request $request) : Response
    {
        $content = json_decode($request->getContent());
        $pageNo = $content->pageNo ;
        $sortField = $content->sortField ;
        $sortOrder = $content->sortOrder ;
        $files = $repo->findList($pageNo, $sortField, $sortOrder);
        $count = $repo->recordCount();
        $response = new JsonResponse(["listData"=>$files, "total"=> $count]);
        return $response;
    }

    #[Route('/contacts', name: 'app_contacts')]
    public function contactList( ContactsRepository $repo, Request $request ) : Response
    {
        $content = json_decode($request->getContent());
        $fileId = $content->fileId;
        $pageNo = $content->pageNo;
        $sortField = $content->sortField;
        $sortOrder = $content->sortOrder;
        $searchText = $content->searchText;
        $contacts = $repo->findByFileId( $fileId, $pageNo, $sortField, $sortOrder, $searchText);
        $count = $repo->recordCount($fileId);
        $response = new JsonResponse(["listData"=> $contacts, "total"=> $count]);
        return $response;
    }
}
