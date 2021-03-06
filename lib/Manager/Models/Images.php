<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/29/18
 * Time: 2:00 PM
 */

namespace Manager\Models;
use Manager\Config;
use Manager\Helpers\Server;


class Images extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, "image");
    }

    /**
     * Write data to a file based on a file pointer.
     * @param int $userId
     * @param string $name
     * @param string $file
     * @param string $type
     * @param int $time
     * @return int ID for the new entry or false if fail
     */
    public function writeFile($userId,
                              $name, $file, $type, $datetime) {
        $pdo = $this->pdo();

        $sql = <<<SQL
insert into $this->tableName(userid, name, image, thumbnail, type, date_added, date_modified)
values(?, ?, ?, ?, ?, ?, ?)
SQL;

        $fp = fopen($file, 'rb');
        if($fp === false) {
            return false;
        }

        switch ($type){
            case 'image/jpeg':
                $orig = imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $orig = imagecreatefrompng($file);
                break;
            case 'image/gif':
                $orig = imagecreatefromgif($file);
                break;
            default:
                return false;
        }

        list($w, $h) = getimagesize($file);
        $scale = $this->scale($w, $h, 64);
        $thumb = imagecreatetruecolor($scale['w'], $scale['h']);
        imagecopyresampled($thumb, $orig, 0, 0, 0, 0, $scale['w'], $scale['h'], $w, $h);
        $stmt = $pdo->prepare($sql);

        $tempFile = './tempimage' . Server::getRequestTime();

        switch ($type){
            case 'image/jpeg':
                $tempFile .= '.jpg';
                imagejpeg($thumb, $tempFile, 100);
                break;
            case 'image/png':
                $tempFile .= '.png';
                imagepng($thumb, $tempFile, 100);
                break;
            case 'image/gif':
                $tempFile .= '.gif';
                imagegif($thumb, $tempFile);
                break;
            default:
                return false;
        }

        $tempFp = fopen($tempFile, 'rb');
        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $name);
        $stmt->bindParam(3, $fp, \PDO::PARAM_LOB);
        $stmt->bindParam(4, $tempFp, \PDO::PARAM_LOB);
        $stmt->bindParam(5, $type);
        $stmt->bindParam(6, $datetime);
        $stmt->bindParam(7, $datetime);



        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            if(!$stmt->execute()) {
                return false;
            }
        } catch(\PDOException $e) {
            print_r($e->getMessage());
            return false;
        } finally {
            imagedestroy($thumb);
            imagedestroy($orig);
            unlink($tempFile);
        }

        return $pdo->lastInsertId();
    }


    /**
     * Reads a file from the database given an ID
     * @param $id
     * @return array|null
     */
    public function readFileId($id) {
        $pdo = $this->pdo();

        $sql = <<<SQL
select `image`, thumbnail, userid, `name`, `type`, date_added, date_modified
from $this->tableName
where id=?
SQL;

        $userId = null;
        $name = null;
        $image = null;
        $type = null;
        $created = null;
        $modified = null;
        $thumbnail = null;

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);


        $stmt->bindColumn(1, $image, \PDO::PARAM_LOB);
        $stmt->bindColumn(2, $thumbnail, \PDO::PARAM_LOB);
        $stmt->bindColumn(3, $userId, \PDO::PARAM_INT);
        $stmt->bindColumn(4, $name, \PDO::PARAM_STR);
        $stmt->bindColumn(5, $type, \PDO::PARAM_STR);
        $stmt->bindColumn(6, $created, \PDO::PARAM_STR);
        $stmt->bindColumn(7, $modified, \PDO::PARAM_STR);

        if($stmt->fetch(\PDO::FETCH_BOUND) !== false) {
            return [
                'userId'=>$userId,
                'name'=>$name,
                'image' => $image,
                'thumbnail' => $thumbnail,
                'type' => $type,
                'date_added' => strtotime($created),
                'date_modified' => strtotime($modified)
            ];
        } else {
            return null;
        }
    }

    public function getThumbnail($id) {

    }

    private function createThumnail($id) {

    }

    private function scale($w, $h, $max = 64) {
        $ratiox = $w/$max;
        $ratioy = $h/$max;

        $ratio = max($ratiox, $ratioy);

        $neww = intval($w/$ratio);
        $newh = intval($h/$ratio);
        return ['w' => $neww, 'h' => $newh];
    }
}