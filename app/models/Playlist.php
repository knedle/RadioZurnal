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
                $year = !empty($values['year']) ? $values['year'] : 0;
                $interpret->related('interpret_song')->insert(array('song_id' => $song['id'], 'year' => $year, 'created_at' => new \Nette\Database\SqlLiteral('NOW()')));
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

    /**
     *
     * @param type $data 
     */
    public function playNow($data) {
        list($interpretId, $songId) = explode('-', $data);
        if (!empty($interpretId) && !empty($songId)) {
            $row = $this->interpretSongs->where('interpret_id', $interpretId)->where('song_id', $songId)->where('NOT DATE(modified_at)', new \Nette\Database\SqlLiteral('CURDATE()'))/*->fetch()*/;
            $row->update(array('counter' => new \Nette\Database\SqlLiteral('`counter` + 1'), 'modified_at' => new \Nette\Database\SqlLiteral('NOW()')));
            $this->interpretSongs = $this->database->table('interpret_song');
        }
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
                $object = $query;//->fetch();
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
    public function loadAgregation($by = '')  {
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
}
