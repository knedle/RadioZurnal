<?php

/**
 * Playlist presenters.
 *
 * @author     Ladislav Sevcuj
 * @package    Radiozurnal
 */
use Nette\Application\UI\Form,
    Nette\Application as NA;

class PlaylistPresenter extends BasePresenter {

    private $interprets;
    private $songs;
    private $interpretSongs;
    private $limit = 15;
    private $finalCount = 890; // z clanku...

    protected function startup() {
        parent::startup();

        $this->interpretSongs = $this->getService('interpretSongs');
    }

    public function renderDefault() {
        $playlist = $this->getService('playlists');
        $this->template->limit = $this->limit;
        $this->template->finalCount = $this->finalCount;
        $this->template->interpretSongs = $playlist->interpretSongs->order('created_at DESC');
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
    public function handlePlayNow($data) {
        $playlist = $this->getService('playlists');
        $playlist->playNow($data);
        if ($this->isAjax()) {
            $this->template->interpretSongs = $playlist->interpretSongs->order('created_at');
            $this->invalidateControl('list');
        } else {
            $this->redirect('this');
        }
    }

    protected function createComponentSongSaveForm() {
        $form = new Form;
        $form->addText('interpret', 'Interpret:')->setAttribute('placeholder', 'interpret')->setAttribute('class', 'span4 addInterpret');
        $form->addText('song', 'Song:')->setAttribute('placeholder', 'song')->setAttribute('class', 'span4 addSong');
        $form->addSubmit('save', 'ulož')->setAttribute('class', 'span2 btn btn-primary');
        $form->addSubmit('cancel', 'zruš ukládání')->setAttribute('class', 'span2 btn');
        $form->onSuccess[] = callback($this, 'songSaveFormSubmitted');
        return $form;
    }

    protected function createComponentSearchForm() {
        $form = new Form;
        $form->setMethod('GET');
        $form->addText('keyword', 'část názvu/jména interpreta:')->setAttribute('placeholder', 'část názvu songu / část jména interpreta')->setAttribute('class', 'span6 filterList')->setAttribute('autocomplete', 'off');
        $form->addSubmit('find', 'najdi')->setAttribute('class', 'span2 btn btn-primary');
        $form->onSuccess[] = callback($this, 'searchFormSubmitted');
        return $form;
    }

    public function searchFormSubmitted($form) {
        $values = $form->values;
        $playlist = $this->getService('playlists');
        $this->template->interprets = $playlist->search($values['keyword']);
        if ($this->isAjax()) {
            if (empty($values['keyword'])) {
                $this->redirect('this');
            }
            $this->invalidateControl('list');
            $this->invalidateControl('addForm');
        } else {
            
        }
    }
    
    public function handleAddNew() {
        $playlist = $this->getService('playlists');
        if ($this->isAjax()) {
            $this->template->interpretSongs = $playlist->interpretSongs->where('0');
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

}
