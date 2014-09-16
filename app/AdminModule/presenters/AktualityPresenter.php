<?php

namespace AdminModule;

use Nette,
	App\Model,
    \Nette\Utils\Paginator,
    Nette\Application\UI\Form,
    Nette\Application as NA;



class AktualityPresenter extends SecuredPresenter
{

    /** @var \ $aktualityRepository */
    public $aktualityRepository;

    public $uzivateleRepository;


    //napojeni na model
    public function injectAktualityRepository(\AktualityRepository $aktualityRepository)
    {
        $this->aktualityRepository = $aktualityRepository;
    }

    public function injectUzivateleRepository(\UzivateleRepository $uzivateleRepository)
    {
        $this->uzivateleRepository = $uzivateleRepository;
    }

    public function renderDefault($page = 0)
    {
        $paginator = new Paginator();
        $paginator->itemCount = $this->aktualityRepository->count();
        $paginator->itemsPerPage = 20;
        $paginator->page = $page;

        if ($paginator->page !== $page)
        {
            $this->redirect('this', array('page' => $paginator->page));
        }

        $this->template->page = $page;
        $this->template->paginator = $paginator;

        $this->template->articles = $this->aktualityRepository->fetchAllAdmin($paginator);
        $this->template->authors = $this->uzivateleRepository->fetchAllFront();
    }

    public function renderEdit($id = 1, $page)
    {

    }

    public function renderDelete($id = 1)
    {

    }
}
