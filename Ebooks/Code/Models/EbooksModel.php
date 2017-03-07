<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Ebooks\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Json\Json;
use Kazist\Service\Database\Query;

/**
 * Description of MarketplaceModel
 *
 * @author sbc
 */
class EbooksModel extends BaseModel {

    public $limit = '';
    public $frequency = array('daily', 'weekly', 'monthly', 'yearly');
    public $page_flip_folder = '';
    public $page_flip_location = '';
    public $page_flip_web_location = '';

    public function save($form) {

        $out = '';
        $rcode = '';

        $factory = new KazistFactory();

        $id = parent::save($form);

        $ebook = $this->getEbook($id);

        exec("convert -version", $out, $rcode); //Try to get ImageMagick "convert" program version number.

        if (strpos(implode('', $out), 'ImageMagick')) {

            $pdf_file = rtrim(JPATH_ROOT, '/') . '/' . $ebook->file_file;

            $file_path_arr = explode('/', $ebook->file_file);
            $file_path_arr_rev = array_reverse($file_path_arr);
            $file_name_arr = explode('.', $file_path_arr_rev[0]);

            unset($file_path_arr_rev[0]);
            $new_file_path_arr = array_reverse($file_path_arr_rev);
            $image_folder = rtrim(JPATH_ROOT, '/') . '/' . implode('/', $new_file_path_arr) . '/' . $file_name_arr[0] . '/';
            $jpg_file = $new_file_path = $image_folder . $file_name_arr[0] . '.jpg';

            $factory->makeDir($image_folder);

            exec("convert -density 300 {$pdf_file} {$jpg_file}", $new_out, $result);
        }

        return $id;
    }

    public function getPagesList() {

        $files_arr = array();

        $scanned_directory = array_diff(scandir($this->page_flip_location), array('..', '.', 'resized'));

        if (!empty($scanned_directory)) {
            foreach ($scanned_directory as $item) {
                if (getimagesize($this->page_flip_location . '/' . $item)) {

                    $file_key = str_replace($this->page_flip_folder . '-', '', $item);
                    $file_key = str_replace('.jpg', '', $file_key);

                    $files_arr[$file_key] = rtrim($this->page_flip_web_location) . '/' . $item;
                }
            }
        }

        ksort($files_arr);

        return $files_arr;
    }

    public function checkFlipPages($item) {

        $factory = new KazistFactory();
        $session = $factory->getSession();
        $kazi_url = $session->get('kazi_url');

        $file_file = $item->file_file;
        $file_file_arr = explode('/', $file_file);
        $file_file_arr_rev = array_reverse($file_file_arr);

        $file_name = $file_file_arr_rev[0];
        $file_name_arr = explode('.', $file_name);

        array_pop($file_file_arr);
        $file_file_arr[] = $file_name_arr[0];

        $this->page_flip_location = JPATH_ROOT . implode('/', $file_file_arr);
        $this->page_flip_web_location = $kazi_url->base_root . implode('/', $file_file_arr);
        $this->page_flip_folder = $file_name_arr[0];

        if (is_dir($this->page_flip_location)) {
            return true;
        } else {
            return false;
        }
    }

    public function getRelatedEbook($ebook_id) {

        $json = new Json();
        $factory = new KazistFactory();



        $query = new Query();
        $query = $this->getQueryBuilder('#__ebooks_ebooks', 'ee');

        if ($magazine_id) {
            $query->where('ee.id<>:ebook_id');
            $query->setParameter('ebook_id', $ebook_id);
        }

        $query->setFirstResult(0);
        $query->setMaxResults(5);
        $records = $query->loadObjectList();

        return $records;
    }

    public function getCategoryEbooks($category_id) {

        $query = new Query();

        $query = $this->getQueryBuilder('#__ebooks_ebooks', 'ee');

        if ($category_id) {
            $query->where('ee.category_id=:category_id');
            $query->setParameter('category_id', $category_id);
        } else {
            $query->where('1=-1');
        }

        $query->setFirstResult(0);
        $query->setMaxResults(5);
        $records = $query->loadObjectList();

        return $records;
    }

    public function getEbook($id) {

        $query = new Query();
        $query = $this->getQueryBuilder('#__ebooks_ebooks', 'ee');

        if ($id) {
            $query->where('ee.id=:id');
            $query->setParameter('id', $id);
        } else {
            $query->where('1=-1');
        }



        $record = $query->loadObject();

        return $record;
    }

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function downloadEbook() {

        $id = $this->request->get('id');
        $ebook = $this->getRecord($id);

        $path = JPATH_ROOT . $ebook->file_file;
        $path_arr = array_reverse(explode('/', $path));
        $filename = $path_arr[0];

        if ($this->canDownloadEbook()) {

            $this->logEbookViews($ebook->id);

            header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            header('Accept-Ranges: bytes');  // Allow support for download resume
            header('Content-Length: ' . filesize($path));  // File size
            header('Content-Encoding: none');
            header('Content-Type: application/pdf');  // Change the mime type if the file is not PDF
            header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
            readfile($path);  // This is necessary in order to get it to actually download the file, otherwise it will be 0K

            return true;
        } else {
            $this->redirectToEbook();
            return false;
        }
    }

    public function redirectToEbook() {

        $factory = new KazistFactory();

        $factory->enqueueMessage('You have reached Maximum Ebooks Download.', 'error');
    }

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function canDownloadEbook() {

        $counter_total = 0;
        $limit_total = 0;

        $limits = $this->getEbookLimits(null, false);
        $is_active = $this->ebookIsActive();

        if ($is_active) {
            return true;
        } else {
            if (!empty($limits)) {
                foreach ($limits as $limit) {

                    $counter = $this->countByFrequency($limit);

                    if (($limit->book_limit + $limit_total) >= $counter) {
                        $this->limit = $limit;
                        return true;
                    }

                    $limit_total += $limit->book_limit;
                    $counter_total += $counter;
                }
            }
        }

        return false;
    }

    public function ebookIsActive() {

        $factory = new KazistFactory();

        $user = $factory->getUser();


        $id = $this->request->get('id');

        $query = new Query();
        $query->select('COUNT(*) as total');
        $query->from('#__ebooks_ebooks_users', 'eeu');
        $query->where('eeu.user_id=' . $user->id);
        if ($id) {
            $query->andWhere('eeu.ebook_id=:ebook_id');
            $query->setParameter('ebook_id', $id);
        } else {
            $query->where('1=-1');
        }
        $query->where('eeu.is_active=1');

        $result = $query->loadResult();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function countByFrequency($limit) {

        $factory = new KazistFactory();

        $user = $factory->getUser();

        $today_str = date('Y-m-d H:i:s');

        if ($limit->frequency == 'daily') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 day'));
        } elseif ($limit->frequency == 'weekly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-7 day'));
        } elseif ($limit->frequency == 'monthly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 month'));
        } elseif ($limit->frequency == 'yearly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 year'));
        } else {
            $date_str = date('2000-01-01 H:i:s');
        }

        $query = new Query();
        $query->select('COUNT(*) as total');
        $query->from('#__ebooks_ebooks_users', 'eeu');
        $query->where('eeu.date_created BETWEEN :date_str AND :today_str');
        $query->andWhere('eeu.user_id=:user_id');
        $query->setParameter('date_str', $date_str);
        $query->setParameter('today_str', $today_str);
        $query->setParameter('user_id', $user->id);

        $result = $query->loadResult();

        return $result;
    }

    public function getEbookLimits($user_id = '', $fetch_all = true) {

        $group_ids_arr = array();
        $json = new Json();
        $factory = new KazistFactory();


        $user = $factory->getUser();
        $group_ids = $factory->getRecords('#__users_users_groups', 'uug', array('uug.user_id=:user_id'), array('user_id' => $user->id));

        foreach ($group_ids as $key => $group_id) {
            $group_ids_arr[] = $group_id->group_id;
        }

        $id = $this->request->get('id');

        $query = new Query();
        $query = $this->getQueryBuilder('#__ebooks_limits', 'el');

        if (!empty($group_ids_arr)) {
            $query->where('el.group_id IN (' . implode(',', $group_ids_arr) . ')');
        } else {
            $query->where('1=-1');
        }

        $query->orderBy('FIELD(' . 'el.frequency, \'' . implode('\', \'', $this->frequency) . '\')');

        $records = $query->loadObjectList();

        return $records;
    }

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function logEbookViews($ebook_id) {

        $factory = new KazistFactory();
        $user = $factory->getUser();

        $limit_download = $factory->getSetting('ebook_ebook_limit_download');

        if ($limit_download) {

            $data_obj = new \stdClass();
            $data_obj->user_id = $user->id;
            $data_obj->ebook_id = $ebook_id;
            $data_obj->is_active = 1;
            $exist_obj = clone $data_obj;
            $data_obj->frequency = frequency;
            $data_obj->limit_id = $this->limit->id;

            $where_arr = array();
            $where_arr[] = 'user_id=:user_id';
            $where_arr[] = 'ebook_id=:ebook_id';
            $where_arr[] = 'is_active=:is_active';

            $factory->saveRecord('#__ebooks_ebooks_users', $data_obj, $where_arr, $exist_obj);
        }
    }

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function archiveEbooks() {


        $limits = $this->getEbookLimits(null, true);

        if (!empty($limits)) {
            foreach ($limits as $key => $limit) {
                $this->archiveEbooksPerLimit($limit);
            }
        }
    }

    public function archiveEbooksPerLimit($limit) {

        $factory = new KazistFactory();


        if ($limit->frequency == 'daily') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 day'));
        } elseif ($limit->frequency == 'weekly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-7 day'));
        } elseif ($limit->frequency == 'monthly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 month'));
        } elseif ($limit->frequency == 'yearly') {
            $date_str = date('Y-m-d H:i:s', strtotime('-1 year'));
        } else {
            $date_str = date('2000-01-01 H:i:s');
        }

        $query = new Query();
        $query->update('#__ebooks_ebooks_users', 'eeu');
        $query->where('eeu.date_created < :date_str');
        $query->andWhere('eeu.limit_id = :limit_id');
        $query->andWhere('eeu.is_active=1');
        $query->setParameter('date_str', $date_str);
        $query->setParameter('limit_id', $limit->id);
        $query->set('is_active', '0');

        $query->execute();
    }

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function addUserEbooks() {

        $factory = new KazistFactory();



        $limits = $this->getFreeLimits();


        foreach ($limits as $key => $limit) {

            $query = new Query();

            $limit_id = $limit->id;
            $frequency = $limit->frequency;
            $ebook_id = $this->getFreeEbooks($limit);
            $today_date = date('Y-m-d H:i:s');

            $sql = 'INSERT INTO `#__ebooks_ebooks_users` (`user_id`, `limit_id`, `ebook_id`, `frequency`, `date_created`, `is_active`) '
                    . '('
                    . 'SELECT ss.`user_id`, :limit_id, :ebook_id, :frequency, :today_date, 1 FROM `#__subscriptions_subscriptions` AS ss '
                    . 'LEFT JOIN `#__ebooks_ebooks_users` AS eeu ON (eeu.user_id=ss.user_id AND eeu.ebook_i d= :ebook_id)'
                    . 'LEFT JOIN `#__users_users_groups` AS uug ON (uug.user_id=ss.user_id AND uug.group_id=' . $limit->group_id . ')'
                    . 'WHERE  eeu.id IS NULL AND uug.id IS NOT NULL'
                    . ')'
            ;
            $query->setParameter('limit_id', $limit_id);
            $query->setParameter('ebook_id', $ebook_id);
            $query->setParameter('frequency', $frequency);
            $query->setParameter('today_date', $today_date);

            $query->setQuery($sql)->execute();
        }
    }

    public function getFreeEbooks($limit) {

        $factory = new KazistFactory();


        $limit_query = new Query();
        $limit_query->select('elf.*');
        $limit_query->from('#__ebooks_ebooks_freebooks', 'elf');
        $limit_query->where('DATE(NOW()) BETWEEN  elf.start_date AND elf.end_date');
        $limit_query->andWhere('limit_id=:limit_id');
        $limit_query->setParameter('limit_id', $limit->id);

        $selected_ebook = $limit_query->loadObject();

        if (is_object($selected_ebook)) {
            return $selected_ebook->ebook_id;
        } else {

            $data_obj = new \stdClass();

            $free_query = new Query();
            $free_query->select('ee.*');
            $free_query->from('#__ebooks_ebooks', 'ee');
            $free_query->where('is_free=1');
            $free_query->orderBy('RAND()');

            $free_ebook = $free_query->loadObject();

            $data_obj->ebook_id = $free_ebook->id;
            $data_obj->limit_id = $limit->id;
            $data_obj->start_date = $this->getStartDate($limit->frequency);
            $data_obj->end_date = $this->getEndDate($limit->frequency);

            $where_arr = array();
            $where_arr[] = 'ebook_id=:ebook_id';
            $where_arr[] = 'limit_id=:limit_id';
            $where_arr[] = 'start_date=:start_date';
            $where_arr[] = 'end_date=:end_date';

            $factory->saveRecord('#__ebooks_ebooks_freebooks', $data_obj, $where_arr, $data_obj);

            return $free_ebook->id;
        }
    }

    public function getStartDate($frequency) {

        if ($frequency == 'daily') {
            $date_str = date('Y-m-d 00:00:00');
        } elseif ($frequency == 'weekly') {
            $date_str = date('Y-m-d 00:00:00', strtotime('last sunday'));
        } elseif ($frequency == 'monthly') {
            $date_str = date('Y-m-01 00:00:00');
        } elseif ($frequency == 'yearly') {
            $date_str = date('Y-01-01 00:00:00');
        } else {
            $date_str = date('2000-01-01 H:i:s');
        }

        return $date_str;
    }

    public function getEndDate($frequency) {

        if ($frequency == 'daily') {
            $date_str = date('Y-m-d 23:59:59');
        } elseif ($frequency == 'weekly') {
            $date_str = date('Y-m-d 23:59:59', strtotime('next sunday'));
        } elseif ($frequency == 'monthly') {
            $date_str = date('Y-m-31 23:59:59');
        } elseif ($frequency == 'yearly') {
            $date_str = date('Y-12-31 23:59:59');
        } else {
            $date_str = date('2000-01-01 H:i:s');
        }

        return $date_str;
    }

    public function getFreeLimits() {

        $factory = new KazistFactory();


        $query = new Query();
        $query->select('*');
        $query->from('#__ebooks_limits', 'el');
        $query->where('el.free_ebook >= 1');

        $records = $query->loadObjectList();

        return $records;
    }

}
