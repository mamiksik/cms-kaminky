<?php

namespace AdminModule;

use Nette,
	App\Model,
    \Nette\Utils\Paginator,
    Nette\Application\UI\Form,
    Nette\Http\User,
    Nette\Application as NA,
    Nette\Forms\Container,
    Nette\Forms\Controls\SubmitButton as NSB;



class AktualityPresenter extends SecuredPresenter
{

    /** @var \ $aktualityRepository */
    public $aktualityRepository;

    /** @var \ $imageRepository */
    public $imagesRepository;

    //napojeni na model
    public function injectAktualityRepository(\AktualityRepository $aktualityRepository)
    {
        $this->aktualityRepository = $aktualityRepository;
    }

    //napojeni na model
    public function injectImagesRepository(\ImagesRepository $imagesRepository)
    {
        $this->imagesRepository = $imagesRepository;
    }


    //
    //RENDER
    //

    public function renderDefault($page = 0)
    {
        //číslování stránek
        $paginator = new Paginator();
        $paginator->itemCount = $this->aktualityRepository->count();
        $paginator->itemsPerPage = 20;
        $paginator->page = $page;

        if ($paginator->page !== $page)
        {
            $this->redirect('this', array('page' => $paginator->page));
        }

        $this->template->page = $page;
        $this->template->paginator = $paginator;

        //vypíše jen záznamy ke kterým mám oprávnění
        if($this->user->isInRole('admin') || $this->user->isInRole('editor'))
        {
            $this->template->articles = $this->aktualityRepository->fetchAllAdmin($paginator);

        }
        elseif($this->user->isInRole('publisher'))
        {
            $this->template->articles = $this->aktualityRepository->fetchAllMyAdmin($paginator, $this->user->id);
        }


    }

    public function renderEdit($id = 1, $page)
    {

        $article = $this->aktualityRepository->getById($id);

        //vypíše jen záznamy ke kterým mám oprávnění
        if(isset($article->author_id))
        {

            if($article->author_id == $this->user->id || $this->user->isInRole('admin') || $this->user->isInRole('editor'))
            {
                $this->template->article = $article;
                $this->template->page = $page;
            }
            else
            {
                $this->flashMessage('Tento článek nemůžeš editovat!', 'warning');
                $this->redirect('Aktuality:default');
            }
        }
        else
        {
            $this->flashMessage('Článek s tímto id není v databázi!', 'warning');
            $this->redirect('Aktuality:default');
        }

    }

    public function renderAdd()
    {
        $this['aktualityForm']['save']->caption = 'Přidat';
    }



    public function renderDelete($id = 1)
    {

        $article = $this->aktualityRepository->getById($id);

        //vypíše jen záznamy ke kterým mám oprávnění
        if(isset($article->author_id))
        {

            if($article->author_id == $this->user->id || $this->user->isInRole('admin') || $this->user->isInRole('editor'))
            {
                $this->template->article = $article;
            }
            else
            {
                $this->flashMessage('Tento článek nemůžeš smazat!', 'warning');
                $this->redirect('Aktuality:default');
            }
        }
        else
        {
            $this->flashMessage('Článek s tímto id není v databázi!', 'warning');
            $this->redirect('Aktuality:default');
        }

    }

    //
    //RENDER END
    //

    //
    //FORMS
    //

    //Formulář pro přidání a editaci akcí
    protected function createComponentAktualityForm($name)
    {
        $form = new Form;
        $form->getElementPrototype()->class[] = "ajax";


        $form->addCheckbox('schovano', '');

        /*/////////////////////*/
        $presenter = $this;
        $invalidateCallback = function () use ($presenter)
        {
            /** @var \Nette\Application\UI\Presenter $presenter */
            $presenter->invalidateControl('Form');
        };

        // rendruje editaci obrazku
        /*$replicator = $form->addDynamic('image_edit', function (Container $container) use ($invalidateCallback)
        {
            $container->addHidden('id_bytu');
            $container->addHidden('path_preview');
            $container->addHidden('path_real');

            $container->addSubmit('remove', 'Smazat')
                ->addRemoveOnClick($invalidateCallback);

        }, 0);*/

        $form->addMultipleFileUpload("image","", /*max num. of files*/ 20);

        $form->addSubmit('save', 'Uložit')
            ->setAttribute('class', 'default')
            ->onClick[] = array($this, 'submitClicked');

        $form->addSubmit('cancel', 'Zrušit')
            ->setValidationScope(FALSE) // prvek se nebude validovat
            ->onClick[] = array($this, 'cancelClicked');

        $this[$name] = $form;
        $form->action .= '#aktuality-Form';

        $form->addProtection('Odešlete prosím formulář znovu (bezpečnostní token vypršel).');
        return $form;
    }

    //
    //FORMS END
    //

    //
    //FORMS SENT
    //

    //Umožnuje zrušit přidání a editaci
    public function cancelClicked()
    {
        // process cancelled
        $this->redirect('default');
    }

    public function submitClicked(NSB $button)
    {
        // $this->redirect('default');
        $form = $button->form;
    }

    //
    //FORM SENT END
    //

}
