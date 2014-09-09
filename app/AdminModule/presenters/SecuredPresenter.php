<?php

namespace AdminModule;

use Nette\Security\User;
use Nette\Application\ForbiddenRequestException;

class SecuredPresenter extends BasePresenter {

public $data;


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

        /*    $acl = new \Nette\Security\Permission;

            // definujeme role
            $acl->addRole('guest');
            $acl->addRole('registered', 'guest'); // registered dědí od guest
            $acl->addRole('admin', 'registered'); // a od něj dědí administrator*/

           // $this->template->user = $this->userService->find($this->getIdentity()->getId());
         //   $this->template->data = \Nette\Environment::getUser()->getRoles();

            $this->flashMessage('Pro vstup do této části webu se musíš přihlásit.', 'warning');
            $backlink = $this->storeRequest();
            $this->redirect('Sign:default', array('backlink' => $backlink));
    }
	}
}