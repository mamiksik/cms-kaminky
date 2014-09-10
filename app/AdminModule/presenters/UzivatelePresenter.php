<?php

namespace AdminModule;

use Nette,
	App\Model,
    \Nette\Utils\Paginator,
    Nette\Application\UI\Form,
    Nette\Application as NA;


/**
 * Homepage presenter.
 */
class UzivatelePresenter extends SecuredPresenter
{
    /**
     * @var \App\Model\UserManager
     * @inject
     */
    public $userManager;

    public $uzivateleRepository;

    public function injectUzivateleRepository(\UzivateleRepository $uzivateleRepository)
    {
        $this->uzivateleRepository = $uzivateleRepository;
    }

	public function renderDefault($page = 1)
	{

        $paginator = new Paginator();
        $paginator->itemCount = $this->uzivateleRepository->count();
        $paginator->itemsPerPage = 20;
        $paginator->page = $page;

        if ($paginator->page !== $page)
        {
            $this->redirect('this', array('page' => $paginator->page));
        }

        $this->template->page = $page;
        $this->template->paginator = $paginator;

		$this->template->users = $this->uzivateleRepository->fetchAllAdmin($paginator);
	}

    public function renderDelete($id = 1)
    {
        $this->template->userDelete = $this->uzivateleRepository->getById($id);

        if (!$this->template->userDelete) {
            throw new NA\BadRequestException('Záznam nebyl nalezen');
        }
    }

    protected function createComponentUserForm() {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Vyplň prosím pole.');

        $form->addText('realname', 'Reálné jméno:')
            ->setRequired('Vyplň prosím pole.');

        $form->addText('email', 'Email:')
            ->setType('email')
            ->setRequired('Vyplň prosím pole.');

        $form->addPassword('password', 'Heslo:')
             ->setRequired('Vyplň prosím pole');

        $role = array(
            'registred' => 'Učitel',
            'editor' => 'Redaktor',
            'admin'  => 'Admin',
        );

        $form->addSelect('role', 'Oprávnění:', $role)
         //   ->setPrompt('Zvol oprávnění');
        ;

        $form->addSubmit('cancel', 'Zrušit')
            ->setValidationScope(FALSE); // prvek se nebude validovat

        $form->addSubmit('save', 'Uložit')->setAttribute('class', 'default');

        $form->onSubmit[] = callback($this, 'userFormSubmitted');
        $form->addProtection('Odešlete prosím formulář znovu (bezpečnostní token vypršel).');
        return $form;
    }

    public function userFormSubmitted(Form $form) {

        if ($form['save']->isSubmittedBy()) {
            $values = $form->values;
            $id = (int) $this->getParam('id');

            if ($id > 0)
            {
                $this->uzivateleRepository->updateById($id, $values);
                $this->flashMessage('Uživatel byl upraven.', 'success');
            }
            else
            {
                try {
                    /* $this->getUser()->login($values->username, $values->password);*/
                    $this->userManager->add($values->username, $values->realname, $values->password, $values->email, $values->role);
                    $this->flashMessage('Uživatel byl přidán.', 'success');
                    //$this->redirect('default');

                } catch (Nette\Security\AuthenticationException $e) {
                    $form->addError($e->getMessage());
                }
            }
        }
        $this->redirect('default');
    }

    public function renderAdd()
    {
        $this['userForm']['save']->caption = 'Přidat';
    }

    public function renderEdit($id = 0)
    {

        $form = $this['userForm'];
        if (!$form->isSubmitted())
        {
            $row = iterator_to_array($this->uzivateleRepository->getById($id));
            if (!$row)
            {
                throw new NA\BadRequestException('Uživatel nebyl nalezen');
            }
            $this['userForm']->setDefaults($row);

        }
    }

    protected function createComponentDeleteForm() {
        $form = new Form;
        $form->addSubmit('cancel', 'Zrušit')
             ->setValidationScope(FALSE); // prvek se nebude validovat;

        $form->addSubmit('delete', 'Smazat')->setAttribute('class', 'default');
        $form->addHidden('id') ;

        $form->onSubmit[] = callback($this, 'deleteFormSubmitted');
        $form->addProtection('Odešlete prosím formulář znovu (bezpečnostní token vypršel).');
        return $form;
    }

    //provádí smazání
    public function deleteFormSubmitted(Form $form) {
        if ($this->user->id == $this->getParam('id')) {

            $this->flashMessage('Chyba, nejde smazat vlastní účet', 'danger');
            $this->redirect('Uzivatele:default');

        }
        elseif($form['delete']->isSubmittedBy())
        {
            $this->uzivateleRepository->deleteById($this->getParam('id'));

            $this->flashMessage('Uživatel byl smazán.', 'success');
            $this->redirect('Uzivatele:default');
        }
        else
        {
            $this->flashMessage('Nastala neidentifikovaná chyba.', 'danger');
            $this->redirect('Uzivatele:default');
        }
    }


}
