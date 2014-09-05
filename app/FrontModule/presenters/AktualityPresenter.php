<?php

namespace FrontModule;

use Nette,
    App\Model;


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

    public function renderDefault()
    {
        $this->template->articles = $this->articlesRepository->fetchAllFront();
    }

}
