<?php

namespace FrontModule;

use Nette,
    App\Model;


/**
 * Homepage presenter.
 */
class AktualityPresenter extends BasePresenter
{

    /*
        public $imageRepository;


        public function injectImageRepository(\ImageRepository $imageRepository)
        {
            $this->imageRepository = $imageRepository;
        }*/

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

}
