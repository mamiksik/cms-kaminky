<?php

namespace AdminModule;

use Nette\Security\User;
use Nette\Application\ForbiddenRequestException;

class SecuredPresenter extends BasePresenter {

    private $httpRequest;
    public $ipRepository;

    public function injectIpRepository(\IpRepository $ipRepository)
    {
        $this->ipRepository = $ipRepository;
    }

    public function injectHttpRequest(\Nette\Http\IRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

	/**
	 * (non-phpDoc)
	 *
	 * @see Nette\Application\Presenter#startup()
	 */
	protected function startup() {

        $this->ipRepository->insert($this->httpRequest->getRemoteHost());
        if($this->ipRepository->count() > 30)
        {
            $id = $this->ipRepository->fetchId();
            $this->ipRepository->deleteById($id);
        }

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