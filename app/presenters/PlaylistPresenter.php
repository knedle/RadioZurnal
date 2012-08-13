<?php

/**
 * Playlist presenters.
 *
 * @author     Ladislav Sevcuj
 * @package    Radiozurnal
 */
use Nette\Application\UI\Form,
    Nette\Utils\Strings,
    Nette\Application as NA,
    Nette\Diagnostics;

class PlaylistPresenter extends BasePresenter {

    private $interprets;
    private $songs;
    private $interpretSongs;
    private $logs;
    private $perPage = 25;
    private $finalCount = 950; // z clanku...
    private $session = null;

    protected function startup() {
        parent::startup();

        $this->interpretSongs = $this->getService('interpretSongs');
        $this->logs = $this->getService('logs');

        $session = $this->getService('session');
        $this->session = $session->getSection('playlist');

        $playlist = $this->getService('playlists');

        $totalCount = $playlist->interpretSongs->count();

        $this->template->finalCount = $this->finalCount; // cilovy pocet
        $this->template->totalCount = $totalCount;
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
        $this->template->showSort = true;
        $this->template->sortBy = Strings::webalize($order);
        $this->template->interpretSongs = $dataSource;
    }

    /**
     *
     * @param type $by 
     */
    public function renderStatsBy($by) {
        $playlist = $this->getService('playlists');

        $totalCount = $playlist->interpretSongs->count();

        $yearCountDataSource = $playlist->loadAgregation($by);

        $yearCount = $yearCountDataSource->fetchPairs("year", "yearCount");

        $this->template->interpretSongs = $this->getService('interpretSongs');
        $this->template->showSort = false;
        $this->template->summaryList = $yearCount;
        $this->template->maxYearCount = max($yearCount);



        switch ($by) {
            case Playlist::AGGREGATION_INTERPRET:
                $this->setView('statsByInterpret');
                $this->template->interpretList = $this->getService('interprets')->fetchPairs("id", "name");
                break;
            case Playlist::AGGREGATION_INTERPRET_PLAYED:
                $this->template->interpretList = $this->getService('interprets')->fetchPairs("id", "name");
                $this->setView('statsByInterpretPlayed');
                break;
            case Playlist::AGGREGATION_SONG_PLAYED:
                $limit = 100;
                $this->template->topLimit = $limit;
                $this->template->interpretSongs->order('counter DESC, interpret.name ASC, song.title ASC')->limit($limit);
                $this->setView('statsBySongPlayed');
                break;
            case Playlist::AGGREGATION_YEAR:
                $this->setView('statsByYear');
                break;
            case Playlist::AGGREGATION_DECADE:
                $this->setView('statsByDecade');
                break;
            default:
                $this->setView('default');
                break;
        }
    }

    /**
     * 
     */
    public function renderToday($date = null) {
        $playlist = $this->getService('playlists');

        $vp = new VisualPaginator($this, 'vp');

        $today = !empty($date) ? new DateTime($date) : new DateTime();

        if (empty($date)) {
            $dataSource = $playlist->logs->where('DATE(log.logtime) =  CURDATE()');
        } else {
            $dataSource = $playlist->logs->where('DATE(log.logtime) = ?', $today->format('Y-m-d'));
        }

        $totalCount = $dataSource->count();

        $prevDay = clone $today;
        $prevDay->sub(new DateInterval('P1D'));

        // ziskat hlasovani uzivatele
        $userHash = $this->getBrowserHash();
        $ratingsSource = $playlist->ratings->where('user_hash', $userHash)->where('day', new \Nette\Database\SqlLiteral('CURDATE()'));
        $ratings = array();
        foreach ($ratingsSource as $ratingSource) {
            $ratings[$ratingSource->interpret_id][$ratingSource->song_id] = $ratingSource->like;
        }

        $todayRatings = $this->getService('ratings')->where('day', $today->format('Y-m-d'));

        //$dataSource = $playlist->interpretSongs->where('DATE(interpret_song.created_at) =  CURDATE() OR DATE(interpret_song.modified_at) =  CURDATE()');

        $dataSourceSong = clone $dataSource;
        $dataSourceInterpret = clone $dataSource;

        $dataSourceForYear = $playlist->interpretSongs->where('song_id', $dataSourceSong->select('song_id'))->where('interpret_id', $dataSourceInterpret->select('interpret_id'));
        $years = array();
        foreach ($dataSourceForYear as $forYear) {
            $years[$forYear->interpret_id][$forYear->song_id] = $forYear->year;
        }

        $paginator = $vp->getPaginator();
        $paginator->itemsPerPage = $this->perPage * 10; // zvysime pocet tak, aby byly vzdy vypsany vsechny songy
        $paginator->itemCount = $dataSource->count();

        // naplnit sablonu daty
        $this->template->interpretSongs = $dataSource->order('log.logtime DESC');
        $this->template->showSort = false;
        $this->template->today = true;
        $this->template->limit = $this->perPage;
        $this->template->years = $years;
        $this->template->date = $date;
        $this->template->prevDay = $prevDay->format("d.m.Y");
        $this->template->totalCount = $totalCount;
        $this->template->ratings = $ratings;
        $this->template->showRating = $today->format('Y-m-d') == date('Y-m-d');
        $this->template->todayRatingCount = count($todayRatings);
    }

    /**
     *
     * @param type $data 
     */
    public function handleDelete($data) {
        $playlist = $this->getService('playlists');
        $playlist->delete($data);
        $this->flashMessage('Záznam byl odstraněn...');
        if ($this->isAjax()) {
            $this->template->interpretSongs = $playlist->interpretSongs->order('created_at DESC');
            $this->invalidateControl('list');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeleteLog($data) {
        $playlist = $this->getService('playlists');
        $playlist->deleteLog($data);
        $this->flashMessage('Záznam byl odstraněn...');
        if ($this->isAjax()) {
            $this->template->interpretSongs = $playlist->interpretSongs->order('created_at DESC');
            $this->invalidateControl('list');
        } else {
            $this->redirect('this');
        }
    }

    public function actionDeleteLog($data) {
        $playlist = $this->getService('playlists');
        $playlist->deleteLog($data);
        $this->flashMessage('Záznam byl odstraněn...');
        $this->redirect('today');
    }

    /**
     *
     * @param type $data 
     */
    public function handlePlayNow($data, $confirm = null) {
        if ($confirm) {
            $playlist = $this->getService('playlists');
            $playlist->playNow($data);
//            if (!empty($this->session->keyword)) {
//                $data = $playlist->search($this->session->keyword);
//            } else {
            $data = $playlist->interpretSongs; //->where('0');
//            }
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

    public function actionPlayNow($data, $confirm = null) {
        if ($confirm) {
            $playlist = $this->getService('playlists');
            $playlist->playNow($data);
            if (!empty($this->session->keyword)) {
                $data = $playlist->search($this->session->keyword);
            } else {
                $data = $playlist->interpretSongs; //->where('0');
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

    protected function createComponentSongSaveForm() {
        $form = new Form;
        $form->addText('interpret', 'Interpret:')->setAttribute('placeholder', 'interpret (ctrl + i = set focus)')->setAttribute('class', 'span4 addInterpret');
        $form->addText('song', 'Song:')->setAttribute('placeholder', 'song (ctrl + alt + i = copy interpret and set focus)')->setAttribute('class', 'span4 addSong');
        $form->addText('year', 'Rok:')->setAttribute('placeholder', 'rok')->setAttribute('class', 'span1');
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
        $form->getElementPrototype()->class('ajaxSubmit');
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
        //if ($form['save']->isSubmittedBy()) {
        //die('sdf');
        $values = $form->getValues();
        $playlist = $this->getService('playlists');
        $saved = $playlist->saveValue($values);

        if ($saved) {
            $this->flashMessage('Editace byla uspesne uložena...');
        } else {
            $this->flashMessage('Editace se nezdarila...');
        }

        $playlist = $this->getService('playlists');
        if (!empty($this->session->keyword)) {
            $data = $playlist->search($this->session->keyword);
        } else {
            $data = $playlist->interpretSongs->where('0');
        }

        if ($this->isAjax()) {
            $this->template->interpretSongs = $data;
            $this->template->hideModalBackground = true;
            $this->invalidateControl('list');
            $this->invalidateControl('modalForm');
        } else {
            $this->redirect('this');
        }
        //}
        //$this->redirect('default');
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

    public function handleWhatNowPlayed($cron = false) {
        $url = "http://www2.rozhlas.cz:81/gselector/player_radiozurnal.js";
        $html = file_get_contents($url);
        $html = stripcslashes($html);

        $autorPregMatch = '/<span class="item-interpret">([^<]*)<\/span>/';
        $songPregMatch = '/<span class="item-track">([^<]*)<\/span>/';

        if (preg_match($autorPregMatch, $html, $matches)) {
            $interpret = trim($matches[1]);
        }

        if (preg_match($songPregMatch, $html, $matches)) {
            $song = trim($matches[1]);
        }

        $save = true;
        $jeDenVTydnu = date('N');
        $jeHodin = date('G');
        if ($jeDenVTydnu == 7 && $jeHodin == 20) {
            $save = false;
        }
        if ($jeDenVTydnu == 6 && $jeHodin == 9) {
            $save = false;
        }

        $this->flashMessage($html, 'dn');

        if (empty($save)) {
            $this->flashMessage('V tuto dobu běží pořad se speciálním playlistem...');
        }

        if (!empty($song) && !empty($interpret)) {
            $playlist = $this->getService('playlists');
            $returnData = $playlist->addInterpretSong($interpret, $song);
            switch ($returnData) {
                case 'detectedAndSave':
                    $this->flashMessage('Song, který právě hraje (' . $interpret . ' - ' . $song . '), byl úspěšně identifikován a uložen do playlistu...');
                    $redirect = 'today';
                    break;
                case 'newSong':
                    $this->flashMessage('Interpret známý (' . $interpret . '), píseň nová (' . $song . ') - píseň uložena do DB a song playlistu... Dobrý úlovek!');
                    $redirect = 'today';
                case 'newInterpret':
                    $this->flashMessage('Interpret nový (' . $interpret . '), píseň známá (' . $song . ') - interpret uložen do DB a song do playlistu... Dobrý úlovek!');
                    $redirect = 'today';
                case 'newInterpretAndSong':
                    // presmerovat na hlavni stranku
                    $this->flashMessage('Identifikován zcela nový song (' . $interpret . ' - ' . $song . '), interpret i píseň uloženy do DB. Výborný úlovek!');
                    $redirect = 'default';
                    break;
                default:
                    break;
            }
        } else {
            $this->flashMessage('Právě nic nehraje, Radiožurnál zapomněl napsat co hraje nebo data nebyla správně identifikována');
            $redirect = 'default';
        }
        if ($cron) {
            die('this is cron url call');
        } else {
            $this->redirect($redirect);
        }
        exit;
    }

    public function actionTestRadiozurnalData() {
        $url = "http://www2.rozhlas.cz:81/gselector/player_radiozurnal.js";
        $html = file_get_contents($url);
        $html = stripcslashes($html);
        echo $html;
        exit;
    }

    public function handleRate($ratingStatus, $interpretId, $songId, $confirm = null) {
        if ($confirm) {

            $playlist = $this->getService('playlists');
            $today = new DateTime();
            $userHash = $this->getBrowserHash();


            // ziskat vsechna +-

            $ratingsSource = $playlist->ratings->where('user_hash', $userHash)->where('day', $today->format('Y-m-d'));
            $ratings = array();
            foreach ($ratingsSource as $ratingSource) {
                $ratings[$ratingSource->interpret_id][$ratingSource->song_id] = $ratingSource->like;
            }
            // vzdy smazat stav v db
            $result = $this->getService('ratings')
                    ->where('interpret_id', $interpretId)
                    ->where('song_id', $songId)
                    ->where('user_hash', $userHash)
                    ->where('day', new \Nette\Database\SqlLiteral('CURDATE()'))
                    ->delete();

            // zkontrolovat, zda bylo jen smazano
            if (!empty($ratings[$interpretId][$songId]) && $ratings[$interpretId][$songId] > 0 && $ratingStatus == 'plus') {
                // jen odstraneni ze seznamu
                unset($ratings[$interpretId][$songId]);
                unset($ratingStatus);
            } elseif (!empty($ratings[$interpretId][$songId]) && $ratings[$interpretId][$songId] < 0 && $ratingStatus == 'minus') {
                // jen odstraneni ze seznamu
                unset($ratings[$interpretId][$songId]);
                unset($ratingStatus);
            } else {
                // ulozit hlasovani
                $rStatus['plus'] = 1;
                $rStatus['minus'] = -1;

                $data = array(
                    'interpret_id' => $interpretId,
                    'song_id' => $songId,
                    'user_hash' => $userHash,
                    'day' => new \Nette\Database\SqlLiteral('CURDATE()'),
                    'like' => $rStatus[$ratingStatus]
                );
                $this->getService('ratings')
                        ->insert($data);
            }
//die($userHash);
            // upravit stav hlasovani

            if ($this->isAjax()) {
                $todayRatings = $this->getService('ratings')->where('day', $today->format('Y-m-d'));
                $this->template->ratings = $ratings;
                $this->template->confirm = 1;
                $this->template->todayRatings = count($todayRatings);
                $this->invalidateControl('list');
                // tohle jeste nefunguje, melo by pak
                $this->invalidateControl('hlasovani-' . $interpretId . '-' . $songId);
                $this->invalidateControl('ratingCount');
            } else {
                $this->redirect('today');
            }
        } else {
            $this->invalidateControl('list');
        }
    }

    public function actionRate($ratingStatus, $interpretId, $songId, $confirm = null) {
        $this->handleRate($ratingStatus, $interpretId, $songId, $confirm);
    }

    /**
     * vrati hash uzivatele podle jeho prohlizece
     * pokus o jeho identifikaci
     * @return string 
     */
    private function getBrowserHash() {
        // ruzne prohlizece nemusi mit vsechny, proto @
        @$hashSource =
                //$_SERVER['HTTP_ACCEPT'] .
                $_SERVER['HTTP_ACCEPT_ENCODING'] .
                $_SERVER['HTTP_ACCEPT_CHARSET'] .
                $_SERVER['HTTP_ACCEPT_LANGUAGE'] .
                $_SERVER['HTTP_UA_CPU'] .
                $_SERVER['HTTP_USER_AGENT'] .
                $_SERVER['REMOTE_ADDR'];
        return md5($hashSource);
    }

}

// text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8gzip,deflate,sdchwindows-1250,utf-8;q=0.7,*;q=0.3cs-CZ,cs;q=0.8Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.60 Safari/537.1127.0.0.1
// text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8gzip,deflate,sdchwindows-1250,utf-8;q=0.7,*;q=0.3cs-CZ,cs;q=0.8Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.60 Safari/537.1127.0.0.1
//186801e1dcefb55250ca8a60e534345