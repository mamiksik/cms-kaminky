<?php

namespace AdminModule;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class UzivatelePresenter extends SecuredPresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

}
