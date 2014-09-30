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

                $this->template->images = $this->imagesRepository->fetchById($id);

                $form = $this['aktualityForm'];
                if (!$form->isSubmitted())
                {
                    $row = iterator_to_array($this->aktualityRepository->getById($id));
                    if (!$row)
                    {
                        throw new NA\BadRequestException('Záznam nebyl nalezen');
                    }

                    // dump($row);

                    $this['aktualityForm']->setDefaults($row);

                    $images_edit = $this->imagesRepository->fetchById($id);

                    foreach ($images_edit as $image_edit)
                    {
                        $form['image_edit'][$image_edit->id]->setValues($image_edit);
                        // $this->template->$image_path_preview[$image_edit->id] = $image_edit->path_preview;
                    }

                }

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

        $form->addText('name', 'Jmeno')
             ->setRequired("Všechna pole musí být vyplněna!");

        $form->addTextArea('content', 'Obsah')
             ->setRequired("Všechna pole musí být vyplněna!");

        $form->addCheckbox('hide', '');

        /*/////////////////////*/
        $presenter = $this;
        $invalidateCallback = function () use ($presenter)
        {
            /** @var \Nette\Application\UI\Presenter $presenter */
            $presenter->invalidateControl('Form');
        };

        // rendruje editaci obrazku
        $replicator = $form->addDynamic('image_edit', function (Container $container) use ($invalidateCallback)
        {
            $container->addHidden('id_record');
            $container->addHidden('path_preview');
            $container->addHidden('path_real');

            $container->addSubmit('remove', 'Smazat')
                ->addRemoveOnClick($invalidateCallback);

        }, 0);

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

    //Smazani
    protected function createComponentDeleteForm()
    {
        $form = new Form;
        $form->addSubmit('cancel', 'Zrušit');
        $form->addSubmit('delete', 'Smazat')->setAttribute('class', 'default');
        $form->addHidden('id');

        $form->onSubmit[] = callback($this, 'deleteFormSubmitted');
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
        //  dump($form);

        //pokud kliknuto ulozit
        if ($form['save']->isSubmittedBy())
        {

            $values = [];
            $dynamic = [];
            $dynamic_edit = [];
            $id_zaznamu = 0;

            //rozdeli 1 pole na 2
            foreach ($form->values as $k => $v)
            {
                if ($k == 'image')
                {
                    $dynamic[$k] = $v;
                }
                elseif ($k == 'image_edit')
                {
                    $dynamic_edit[$k] = $v;
                }
                else
                {
                    $values[$k] = $v;
                }

            }

            //ziskani id z url
            $id = (int)$this->getParam('id');

            //pokud zaznam exzistuje
            if ($id > 0)
            {
                $this->aktualityRepository->updateById($id, $values);
                $this->flashMessage('záznam byl upraven.');
            }
            else
            {

                //ověření záznamu se stejným jménem
                $row = count($this->aktualityRepository->controlByName($values['name']));

                if ($row == 0)
                {
                    //vytvori a vlozi hodnoty
                    $this->aktualityRepository->insert($values);
                    $this->flashMessage('záznam byl přidán.');

                    //ziska nove id
                    $id = $this->aktualityRepository->getbyKey('name', $values['name']);
                }
                else
                {
                    $this->flashMessage('záznam s tímto jménem existuje.');
                }
            }

            //
            //Smaze zaznami
            //

            //pole pro smazani
            $image_delete = [];
            //pole vsech obrazku s od bytu
            $images_edit_original = $this->imagesRepository->getByKey('id_record', $id);
            //prevede image_edit na jednoduche pole ahodne s polem od $image_edit_original
            $dynamic_edit = iterator_to_array($dynamic_edit['image_edit']);

            //projede obje pole o vrati vse krom toho z pole $dynamic_edit
            foreach ($images_edit_original as $k_original => $v_original)
            {
                //pokud hodnota z $k_original neni v poli hodí se do smazani
                if (!array_key_exists($k_original, $dynamic_edit))
                {
                    $image_delete[$k_original] = $v_original;
                }
            }

            //smaže všechny záznami
            foreach ($image_delete as $k => $v)
            {
                //dump($v);
                $file_name_real = $v->path_real;
                $file_name_preview = $v->path_preview;

                $file_name_real = \Nette\Image::fromFile(WWW_DIR .  $file_name_real);
                $file_name_preview = \Nette\Image::fromFile(WWW_DIR  . $file_name_preview);

                //dodělat mazaní fotek z složky
                $this->imagesRepository->deleteByKey('id', $k);
            }

            //
            //vlozeni do database
            //

            $id_zaznamu = $id;
            $data = $form->getValues();
            foreach ($data['image'] as $file)
            {

                // kontrola jestli se jedná o obrázek a jestli se nahrál dobře
                if ($file->isImage() and $file->isOk())
                {

                    // oddělení přípony pro účel změnit název souboru na co chceš se zachováním přípony
                    $file_ext = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));

                    // vygenerování náhodného řetězce znaků, můžeš použít i \Nette\Strings::random()
                    $file_name = uniqid(rand(0, 30), TRUE) . $file_ext;

                    // přesunutí souboru z temp složky někam, kam nahráváš soubory
                    $file->move(WWW_DIR . '/data/' . $file_name);

                    //v případě, že chceš vytvořit z obrázku i miniaturu
                    $image = \Nette\Image::fromFile(WWW_DIR . '/data/' . $file_name);
                    if ($image->getWidth() > $image->getHeight())
                    {
                        $image->resize(200, NULL);
                    }
                    else
                    {
                        $image->resize(NULL, 200);
                    }
                   // $image->sharpen();
                    $image->save(WWW_DIR . '/data/thumbs/' . $file_name);

                }

                $values_database = [];
                //vytvoří zazanam cest k fotce
                $path_preview = '/data/thumbs/' . $file_name;
                $path_real = '/data/' . $file_name;

                //vloži všechny získané hodnoty do pole
                $values_database['id_record'] = $id_zaznamu;
                $values_database['path_preview'] = $path_preview;
                $values_database['path_real'] = $path_real;


                //vloži odkaz do databaze
                $values_database = $this->imagesRepository->insertImage($values_database);
            }

        }
        //přesměruje zpět na default
        $this->redirect('default');
    }

    public function deleteFormSubmitted(Form $form)
    {
        if ($form['delete']->isSubmittedBy())
        {

            $this->aktualityRepository->deleteById($this->getParam('id'));
            $this->imagesRepository->deleteByKey("id_record" ,$this->getParam('id'));

            $this->flashMessage('Článek byl smazán!', 'danger');
        }

        $this->redirect('Aktuality:default');
    }


    //
    //FORM SENT END
    //

}
