<?php

use Nette\Diagnostics;

class Playlist extends Nette\Object {

    /** @var Nette\Database\Table\Selection */
    private $interprets;
    private $songs;
    private $database;
    public $interpretSongs;

    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
        $this->interpretSongs = $this->database->table('interpret_song');
        $this->interprets = $this->database->table('interpret');
        $this->songs = $this->database->table('song');
    }

    /**
     *
     * @param type $data 
     */
    public function save($values) {
        if (!empty($values['interpret']) && $values['song']) {

            // najit interpreta                
            $interpret = $this->interprets->where('name', $values['interpret'])->fetch();

            // ulozit interpreta, pokud neexistuje
            if (empty($interpret)) {
                $this->interprets = $this->database->table('interpret');
                $interpret = $this->interprets->insert(array('name' => $values['interpret'], 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            }

            //$interpretId = $interpret['id'];
            // najit song
            $song = $this->songs->where('title', $values['song'])->fetch();

            // ulozit song
            if (empty($song)) {
                $this->songs = $this->database->table('song');
                $song = $this->songs->insert(array('title' => $values['song'], 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            }

            // otestovat song u interpreta
            if ($interpret->related('interpret_song')->where('song_id', $song['id'])->count()) {
                // jiz existuje
            } else {
                // ulozit song u interpreta
                $interpret->related('interpret_song')->insert(array('song_id' => $song['id'], 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $keyword
     * @return type 
     */
    public function search($keyword) {
        $keyword = '%' . $keyword . '%';
        return $this->interpretSongs->where('interpret.name LIKE ? OR song.title LIKE ? ', $keyword, $keyword);
    }

    /**
     *
     * @param type $data 
     */
    public function delete($data) {
        list($interpretId, $songId) = explode('-', $data);
        if (!empty($interpretId) && !empty($songId)) {
            // smazat spojeni
            $this->interpretSongs->where('interpret_id', $interpretId)->where('song_id', $songId)->delete();

            // ostestovat existenci jinych dat u interpreta
            $is_count = $this->database->table('interpret_song')->where('interpret_id', $interpretId)->count();
            // pokud neexistuji, smazat interpreta
            if (empty($is_count)) {
                $this->interprets->find($interpretId)->delete();
            }

            // otestovat existenci jinych dat u songu
            $is_count = $this->database->table('interpret_song')->where('song_id', $songId)->count();
            // pokud neexistuji, smazat
            if (empty($is_count)) {
                $this->songs->find($songId)->delete();
            }

            $this->interpretSongs = $this->database->table('interpret_song');
        }
    }

    public function playNow($data) {
        list($interpretId, $songId) = explode('-', $data);
        if (!empty($interpretId) && !empty($songId)) {
            $row = $this->interpretSongs->where('interpret_id', $interpretId)->where('song_id', $songId)->fetch();         
            $row->update(array('counter' => new \Nette\Database\SqlLiteral('`counter` + 1'), 'modified_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $this->interpretSongs = $this->database->table('interpret_song');
        }
    }
}
