<?php

namespace FrontModule;

use \Nette,
    \App\Model,
    \Nette\Utils\Paginator;


class StrankyPresenter extends BasePresenter
{
    public $strankyRepository;
    public $uzivateleRepository;
    public $error;


    public function injectStrankyRepository(\StrankyRepository $strankyRepository)
    {
        $this->strankyRepository = $strankyRepository;
    }

    public function injectUzivateleRepository(\UzivateleRepository $uzivateleRepository)
    {
        $this->uzivateleRepository = $uzivateleRepository;
    }

    public function renderDefault($page = 1)
    {
        $paginator = new Paginator();
        $paginator->itemCount = $this->strankyRepository->count();
        $paginator->itemsPerPage = 5;
        $paginator->page = $page;

        if ($paginator->page !== $page)
        {
            $this->redirect('this', array('page' => $paginator->page));
        }

        $this->template->page = $page;
        $this->template->paginator = $paginator;
        $this->template->pages = $this->strankyRepository->fetchAllFront($paginator);
        $this->template->authors = $this->uzivateleRepository->fetchAllFront();
    }

    public function renderDetail($id = 1, $page)
    {
        $this->template->page_db = $this->strankyRepository->getById($id);
        $this->template->page = $page;
        $this->template->authors = $this->uzivateleRepository->fetchAllFront();

        if(!$this->template->page_db){
            $this->template->page_db = 0;
        }

    }


}
