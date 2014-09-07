<?php

namespace FrontModule;

use \Nette,
    \App\Model,
    \Nette\Utils\Paginator;;


/**
 * Homepage presenter.
 */
class AktualityPresenter extends BasePresenter
{

        public $articlesRepository;


        public function injectArtislesRepository(\ArticlesRepository $articlesRepository)
        {
            $this->articlesRepository = $articlesRepository;
        }

    public function renderDefault($page = 1)
    {
        $paginator = new Paginator();
        $paginator->itemCount = $this->articlesRepository->count();
        $paginator->itemsPerPage = 15;
        $paginator->page = $page;
        if($paginator->page !== $page) {
            $this->redirect('this', array('page' => $paginator->page));
        }
        $this->template->paginator = $paginator;
        $this->template->articles = $this->articlesRepository->fetchAllFront($paginator);
    }

}
