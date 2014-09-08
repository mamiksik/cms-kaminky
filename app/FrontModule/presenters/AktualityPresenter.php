<?php

namespace FrontModule;

use \Nette,
    \App\Model,
    \Nette\Utils\Paginator;




/**
 * Homepage presenter.
 */
class AktualityPresenter extends BasePresenter
{

    public $articlesRepository;
    public $error;


    public function injectAktualityRepository(\AktualityRepository $aktualityRepository)
    {
        $this->articlesRepository = $aktualityRepository;
    }

    public function renderDefault($page = 1)
    {
        $paginator = new Paginator();
        $paginator->itemCount = $this->articlesRepository->count();
        $paginator->itemsPerPage = 15;
        $paginator->page = $page;
        if ($paginator->page !== $page) {
            $this->redirect('this', array('page' => $paginator->page));
        }
        $this->template->paginator = $paginator;
        $this->template->articles = $this->articlesRepository->fetchAllFront($paginator);
    }

    public function renderDetail($id = 1)
    {
;
        $this->template->article = $this->articlesRepository->getById($id);

        if(!$this->template->article){
            $this->template->article = 0;
        }

    }

}
