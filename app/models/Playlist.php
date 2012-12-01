<?php

use Nette\Diagnostics;

class Playlist extends Nette\Object {
    const AGGREGATION_YEAR = 'year';
    const AGGREGATION_DECADE = 'decade';
    const AGGREGATION_INTERPRET = 'interpret';
    const AGGREGATION_INTERPRET_PLAYED = 'playedInterpret';
    const AGGREGATION_SONG_PLAYED = 'playedSong';

    /** @var Nette\Database\Table\Selection */
    private $interprets;
    private $songs;
    private $database;
    public $logs;
    public $interpretSongs;
    public $ratings;

    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
        $this->interpretSongs = $this->database->table('interpret_song');
        $this->interprets = $this->database->table('interpret');
        $this->songs = $this->database->table('song');
        $this->logs = $this->database->table('log');
        $this->ratings = $this->database->table('rating');
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
                $year = !empty($values['year']) ? $values['year'] : 0;
                $interpret->related('interpret_song')->insert(array('song_id' => $song['id'], 'year' => $year, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
                // log
                $interpret->related('log')->insert(array('song_id' => $song['id'], 'logtime' => new \Nette\Database\SqlLiteral('NOW()')));
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
        return $this->interpretSongs->where('interpret.name LIKE ? OR interpret.alt LIKE ? OR song.title LIKE ? OR song.alt LIKE ?', $keyword, $keyword, $keyword, $keyword);
    }

    public function addInterpretSong($interpretName, $songTitle) {
        $out = '';
        $interpret = $this->interprets->where('UPPER(interpret.name) LIKE ? OR UPPER(interpret.alt) LIKE ?', $interpretName, $interpretName)->fetch();
        $song = $this->songs->where('UPPER(song.title) LIKE ? OR UPPER(song.alt) LIKE ?', $songTitle, $songTitle)->fetch();
        if ($interpret && $song) {
            $out = 'detectedAndSave';
        } elseif ($interpret && empty($song)) {
            // znamy interpret, neznamy song
            $song = $this->songs->insert(array('title' => $songTitle, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $out = 'newSong';
        } elseif (empty($interpret) && $song) {
            // znamy song, neznamy interpret
            $interpret = $this->interprets->insert(array('name' => $interpretName, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $out = 'newInterpret';
        } else {
            // neznamy interpret i neznamy song
            $song = $this->songs->insert(array('title' => $songTitle, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $interpret = $this->interprets->insert(array('name' => $interpretName, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $out = 'newInterpretAndSong';
        }

        // ulozit

        if ($interpret->related('interpret_song')->where('song_id', $song['id'])->count()) {
            // jiz existuje
            $data = $interpret->id . '-' . $song->id;
            $result = $this->playNow($data);
        } else {
            // ulozit song u interpreta
            $year = 0;
            $interpret->related('interpret_song')->insert(array('song_id' => $song['id'], 'year' => $year, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
            // loguju
            $interpret->related('log')->insert(array('song_id' => $song['id'], 'logtime' => new \Nette\Database\SqlLiteral('NOW()')));
        }

        return $out;
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

    public function deleteLog($data) {
        list($interpretId, $songId, $time) = explode('*', $data);
        if (!empty($interpretId) && !empty($songId) && !empty($time)) {
            // smazat log
            $this->logs->where('interpret_id', $interpretId)->where('song_id', $songId)->where('logtime', $time)->delete();

            // zjistit pocet ostatnich logu
            $log_count = $this->database->table('log')->where('interpret_id', $interpretId)->where('song_id', $songId)->count();
            if (empty($log_count)) {
                // pokud logy uz zadne nejdou, smazat spojeni
                $this->interpretSongs->where('interpret_id', $interpretId)->where('song_id', $songId)->delete();
            }

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

    /**
     *
     * @param type $data 
     */
    public function playNow($data) {
        list($interpretId, $songId) = explode('-', $data);
        if (!empty($interpretId) && !empty($songId)) {
            $row = $this->interpretSongs->where('interpret_id', $interpretId)->where('song_id', $songId)->where('NOT DATE(modified_at)', new \Nette\Database\SqlLiteral('CURDATE()'))/* ->fetch() */;
            $result = $row->update(array('counter' => new \Nette\Database\SqlLiteral('`counter` + 1'), 'modified_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $this->interpretSongs = $this->database->table('interpret_song');
            // loguju
            $todayLogs = $this->logs->where('interpret_id', $interpretId)->where('song_id', $songId)->where('logtime + INTERVAL 10 MINUTE > ?', new \Nette\Database\SqlLiteral('NOW()'));
            if (!count($todayLogs)) {


                $this->logs->insert(
                    array(
                        'interpret_id' => $interpretId, 
                        'song_id' => $songId, 
                        'logtime' => new \Nette\Database\SqlLiteral('NOW()'), 
                        'ip'=>$_SERVER['REMOTE_ADDR'], 
                        'browser_hash'=>$this->getHash()
                        )
                    );
            }
        }
        return $result;
    }

    /**
     *
     * @param type $table
     * @param type $column
     * @param type $primaryKey
     * @return type 
     */
    public function detectColumnTypeAndData($table, $column, $primaryKey) {
        $query = $this->database->table($table)->select($column);
        $object = null;
        // je-li to cislo, predpokladame sloupec id
        if (is_numeric($primaryKey)) {
            $object = $query->get($primaryKey);
        } else {
            $decodeJson = json_decode($primaryKey);
            $decodeJson = get_object_vars($decodeJson);
            if (is_array($decodeJson)) {
                foreach ($decodeJson as $col => $val) {
                    $query->where($col, $val);
                }
                $object = $query->fetch();
            }
        }

        if (isset($object[$column])) {
            $fields = $this->database->query('SHOW COLUMNS FROM ' . $table);
            foreach ($fields as $field) {
                if ($field->Field == $column) {
                    // detekovat
                    switch (true) {
                        case (preg_match("/text/", $field->Type)) :
                            $type = 'textarea';
                            break;
                        case (preg_match("/datetime/", $field->Type)) :
                            $tmpData = (array) $object[$column];
                            $object[$column] = $tmpData['date'];
                        default:
                            $type = 'text';
                            break;
                    }
                    break;
                }
            }

            return array(
                'value' => $object[$column],
                'type' => $type
            );
        }
        return null;
    }

    /**
     * 
     */
    public function saveValue($data) {
        $table = $data['table'];
        unset($data['table']);
        $column = $data['column'];
        unset($data['column']);
        $primaryKey = $data['primaryKey'];
        unset($data['primaryKey']);

        // projet zbytek, v jedno bude nepradne
        // udelat jinak
        $value = '';
        foreach ($data as $val) {
            if (!empty($val)) {
                $value = $val;
                break;
            }
        }
        $query = $this->database->table($table);
        $object = null;
        // je-li to cislo, predpokladame sloupec id
        if (is_numeric($primaryKey)) {
            $object = $query->find($primaryKey);
        } else {
            $decodeJson = json_decode($primaryKey);
            $decodeJson = get_object_vars($decodeJson);
            if (is_array($decodeJson)) {
                foreach ($decodeJson as $col => $val) {
                    $query->where($col, $val);
                }
                //$query->limit(1);
                $object = $query; //->fetch();
            }
        }
        if (count($object)) {
            $object->update(array($column => $value));
            //$this->interpretSongs = $this->database->table('interpret_song');
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $by
     * @return type 
     */
    public function loadAgregation($by = '') {
        $limit = 50;
        switch ($by) {
            case self::AGGREGATION_DECADE:
                $this->interpretSongs->select('COUNT(`year`) AS yearCount, SUBSTRING(year,1,3) AS year')->where('NOT year', 0)->order('year DESC')->group('SUBSTRING(year,1,3)');
                break;
            case self::AGGREGATION_INTERPRET:
                $this->interpretSongs->select('COUNT(`interpret_id`) AS yearCount, interpret_id AS year')->order('yearCount DESC')->group('interpret_id', 'yearCount > 1');
                break;
            case self::AGGREGATION_INTERPRET_PLAYED:
                $this->interpretSongs->select('SUM(`counter`) AS yearCount, interpret_id AS year')->order('yearCount DESC')->group('interpret_id', 'yearCount > 1')->limit($limit);
                break;
            default:
                $this->interpretSongs->select('COUNT(`year`) AS yearCount, year')->where('NOT year', 0)->order('year DESC')->group('year');
                break;
        }
        return $this->interpretSongs;
    }

    private function getHash() {
        // ruzne prohlizece nemusi mit vsechny, proto @
        @$hashSource =
                //$_SERVER['HTTP_ACCEPT'] . // nepouzivat, pokud volam i pod ajaxem
                $_SERVER['HTTP_ACCEPT_ENCODING'] .
                $_SERVER['HTTP_ACCEPT_CHARSET'] .
                $_SERVER['HTTP_ACCEPT_LANGUAGE'] .
                $_SERVER['HTTP_UA_CPU'] .
                $_SERVER['HTTP_USER_AGENT'] .
                $_SERVER['REMOTE_ADDR'];
        return md5($hashSource);
    }    

}

