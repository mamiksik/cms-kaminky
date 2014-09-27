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

    public $aktualityRepository;
    public $error;

    public $imagesRepository;


    public function injectAktualityRepository(\AktualityRepository $aktualityRepository)
    {
        $this->aktualityRepository = $aktualityRepository;
    }

    public function injectImagesRepository(\ImagesRepository $imagesRepository)
    {
        $this->imagesRepository = $imagesRepository;
    }



    public function renderDefault($page = 1)
    {
        $paginator = new Paginator();
        $paginator->itemCount = $this->aktualityRepository->count();
        $paginator->itemsPerPage = 15;
        $paginator->page = $page;

        if ($paginator->page !== $page)
        {
            $this->redirect('this', array('page' => $paginator->page));
        }

        $this->template->page = $page;
        $this->template->paginator = $paginator;
        $this->template->articles = $this->aktualityRepository->fetchAllFront($paginator);

    }

    public function renderDetail($id = 1, $page)
    {

        $this->template->article = $this->aktualityRepository->getById($id);
        $this->template->page = $page;
        $this->template->images = $this->imagesRepository->fetchById($id);

        if(!$this->template->article){
            $this->template->article = 0;
        }

    }

}
