<?php

namespace AdminModule;

use Nette\Security\User;
use Nette\Application\ForbiddenRequestException;

class SecuredPresenter extends BasePresenter {

	/**
	 * (non-phpDoc)
	 *
	 * @see Nette\Application\Presenter#startup()
	 */
	protected function startup() {
		parent::startup();
		if(!$this->user->isLoggedIn()) {
      if($this->user->getLogoutReason() === User::INACTIVITY) {
        $this->flashMessage('Byl jsi odhlášen, protože jsi nebyl dlouho aktivní.', 'warning');
      }

            $this->flashMessage('Pro vstup do této části webu se musíš přihlásit.', 'warning');
            $backlink = $this->storeRequest();
            $this->redirect('Sign:default', array('backlink' => $backlink));
    }
	}
}