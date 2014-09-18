<?php

namespace AdminModule;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter
{
    public $aktualityRepository;


    public function injectAktualityRepository(\AktualityRepository $aktualityRepository)
    {
        $this->aktualityRepository = $aktualityRepository;
    }


    public function renderDefault()
	{
        $this->template->articleCount = $this->aktualityRepository->countMy($this->user->id);
	}

}
