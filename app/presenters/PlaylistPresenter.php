<?php

/**
 * Playlist presenters.
 *
 * @author     Ladislav Sevcuj
 * @package    Radiozurnal
 */
use Nette\Application\UI\Form,
    Nette\Utils\Strings,
    Nette\Application as NA;

class PlaylistPresenter extends BasePresenter {

    private $interprets;
    private $songs;
    private $interpretSongs;
    private $perPage = 25;
    private $finalCount = 950; // z clanku...
    private $session = null;

    protected function startup() {
        parent::startup();

        $this->interpretSongs = $this->getService('interpretSongs');

        $session = $this->getService('session');
        $this->session = $session->getSection('playlist');
    }

    public function renderDefault() {
        $playlist = $this->getService('playlists');
        $dataSource = $playlist->interpretSongs;

        $vp = new VisualPaginator($this, 'vp');

        $paginator = $vp->getPaginator();
        $paginator->itemsPerPage = $this->perPage;
        $paginator->itemCount = $dataSource->count();

        if (empty($this->session->order)) {
            $order = 'created_at DESC';
        } else {
            $order = $this->session->order;
        }

        $dataSource->limit($paginator->itemsPerPage, $paginator->offset)->order($order);

        $this->template->limit = $this->perPage;
        $this->template->finalCount = $this->finalCount; // cilovy pocet
        $this->template->totalCount = $paginator->itemCount; // pocet v db
        $this->template->showSort = true;
        $this->template->sortBy = Strings::webalize($order);
        $this->template->interpretSongs = $dataSource;
    }

    /**
     *
     * @param type $data 
     */
    public function handleDelete($data) {
        $playlist = $this->getService('playlists');
        $playlist->delete($data);
//        $this->template->interpretSongs = $this->getService('interprets')->order('created_at');
        $this->flashMessage('Záznam byl odstraněn...');
        if ($this->isAjax()) {
            $this->template->interpretSongs = $playlist->interpretSongs->order('created_at DESC');
            $this->invalidateControl('list');
        } else {
            $this->redirect('this');
        }
    }

    /**
     *
     * @param type $data 
     */
    public function handlePlayNow($data, $confirm = null) {
        if ($confirm) {
            $playlist = $this->getService('playlists');
            $playlist->playNow($data);
            if (!empty($this->session->keyword)) {
                $data = $playlist->search($this->session->keyword);
            } else {
                $data = $playlist->interpretSongs->where('0');
            }
            if ($this->isAjax()) {
                $this->template->interpretSongs = $data->order('created_at DESC');
                $this->template->confirm = 1;
                $this->invalidateControl('list');
            } else {
                $this->redirect('this');
            }
        }
    }

    public function handleSort($by, $ascDesc = 'asc') {
        switch ($by) {
            case "name":
                $by = "interpret.name";
                break;
            case "title":
                $by = "song.title";
                break;
            case "time":
                $by = "created_at";
                break;
        }
        $order = $by . ' ' . Strings::upper($ascDesc);
        if ($this->isAjax()) {
            $playlist = $this->getService('playlists');
            $data = $playlist->interpretSongs;
            $this->template->interpretSongs = $data->order($order);
            $this->template->sortBy = Strings::webalize($order);
            $this->template->showSort = false;
            $this->template->confirm = 1;
            $this->invalidateControl('list');
        } else {
            $this->session->order = $order;
            $this->redirect('this');
        }
    }

    protected function createComponentSongSaveForm() {
        $form = new Form;
        $form->addText('interpret', 'Interpret:')->setAttribute('placeholder', 'interpret')->setAttribute('class', 'span4 addInterpret');
        $form->addText('song', 'Song:')->setAttribute('placeholder', 'song')->setAttribute('class', 'span4 addSong');
        $form->addText('year', 'Rok:')->setAttribute('placeholder', 'rok')->setAttribute('class', 'span1');
        $form->addSubmit('save', 'ulož')->setAttribute('class', 'span2 btn btn-primary');
        $form->addSubmit('cancel', 'zruš ukládání')->setAttribute('class', 'span2 btn');
        $form->onSuccess[] = callback($this, 'songSaveFormSubmitted');
        return $form;
    }

    protected function createComponentSearchForm() {
        $form = new Form;
        $form->setMethod('GET');
        $form->addText('keyword', 'část názvu/jména interpreta:')->setAttribute('placeholder', 'část názvu songu / část jména interpreta')->setAttribute('class', 'span6 filterList icon-search')->setAttribute('autocomplete', 'off');
        $form->addSubmit('find', 'najdi')->setAttribute('class', 'span2 btn btn-primary icon-search');
        $form->onSuccess[] = callback($this, 'searchFormSubmitted');
        return $form;
    }

    public function searchFormSubmitted($form) {
        $values = $form->values;
        $this->session->order = '';
        $playlist = $this->getService('playlists');
        $this->template->interprets = $playlist->search($values['keyword']);
        $this->session->keyword = $values['keyword'];
        if ($this->isAjax()) {
            if (empty($values['keyword'])) {
                $this->redirect('this');
            }
            $this->template->confirm = 1;
            $this->invalidateControl('list');
            $this->invalidateControl('addForm');
        } else {
            
        }
    }

    public function handleAddNew() {
        $playlist = $this->getService('playlists');
        if (!empty($this->session->keyword)) {
            $data = $playlist->search($this->session->keyword);
        } else {
            $data = $playlist->interpretSongs->where('0');
        }
        if ($this->isAjax()) {
            $this->template->interpretSongs = $data;
            $this->template->showAddForm = 1;
            $this->invalidateControl('list');
            $this->invalidateControl('addForm');
        } else {
            $this->redirect('this');
        }
    }

    public function songSaveFormSubmitted(Form $form) {
        // volá se po odeslání formuláře
        if ($form['save']->isSubmittedBy()) {
            $values = $form->getValues();
            $playlist = $this->getService('playlists');
            $saved = $playlist->save($values);

            if ($saved) {
                $this->flashMessage('Nový záznam byl v pořádku uložen...');
            } else {
                $this->flashMessage('Nový záznam songu a intepreta nebyl uložen...');
            }
        }
        $this->redirect('default');
    }

    /**
     *
     * @param type $term 
     */
    public function handleAutocompleteSong($term) {
        $data = array();

        $term = Nette\Utils\Strings::trim($term);
        if (!empty($term) && (strlen($term) >= 2)) {
            $this->songs = $this->getService('songs');
            $songs = $this->songs->where('title LIKE ?', '%' . $term . '%');
            if (!empty($songs)) {
                foreach ($songs as $song) {
                    $data[] = array(
                        'id' => $song->id,
                        'label' => $song->title,
                        'value' => $song->title,
                    );
                }
            }
        }

        echo json_encode($data);

        $this->terminate();
    }

    /**
     *
     * @param type $term 
     */
    public function handleAutocompleteInterpret($term) {
        $data = array();

        $term = Nette\Utils\Strings::trim($term);
        if (!empty($term) && (strlen($term) >= 2)) {
            $this->interprets = $this->getService('interprets');
            $interprets = $this->interprets->where('name LIKE ?', '%' . $term . '%');
            if (!empty($interprets)) {
                foreach ($interprets as $interpret) {
                    $data[] = array(
                        'id' => $interpret->id,
                        'label' => $interpret->name,
                        'value' => $interpret->name,
                    );
                }
            }
        }

        echo json_encode($data);

        $this->terminate();
    }

    public function handleModalEditForm($table, $column, $primaryKey) {
        // zjistit existenci table, column, primaryKey
        $playlist = $this->getService('playlists');
        $data = $playlist->detectColumnTypeAndData($table, $column, $primaryKey);

        if (!empty($data)) {
            $data['table'] = $table;
            $data['column'] = $column;
            $data['primaryKey'] = $primaryKey;
            echo json_encode($data);
        } else {
            echo null;
        }

        $this->terminate();
    }

    protected function createComponentModalEditForm() {
        $form = new Form;
        $form->addHidden('primaryKey');
        $form->addHidden('table');
        $form->addHidden('column');
        $form->addText('dataText')->setAttribute('class', 'selectOne span5')->setAttribute('data-type', 'text');
        $form->addTextArea('dataTextarea')->setAttribute('class', 'selectOne span5')->setAttribute('data-type', 'textarea');
        // doplnit dalsi
        $form->addSubmit('save', 'Ulož')->setAttribute('class', 'span2 btn btn-primary fr');
        //$form->addSubmit('cancel', 'zruš ukládání')->setAttribute('class', 'span2 btn');
        $form->onSuccess[] = callback($this, 'modalEditFormSubmitted');
        return $form;
    }

    public function modalEditFormSubmitted(Form $form) {
        // volá se po odeslání formuláře
        if ($form['save']->isSubmittedBy()) {
            $values = $form->getValues();
            $playlist = $this->getService('playlists');
            $saved = $playlist->saveValue($values);

            if ($saved) {
                $this->flashMessage('Editace byla uspesne uložena...');
            } else {
                $this->flashMessage('Editace se nezdarila...');
            }
        }
        $this->redirect('default');        
    }

}
