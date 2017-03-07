<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Addons\Ebooks\Models;

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;
use Kazist\Model\BaseModel;

class EbooksModel extends BaseModel {

    public function getInfo() {
        return 'Hello World!';
    }

    public function getEbooks() {


        $query = $this->getQueryBuilder('#__ebooks_ebooks', 'ee');

        $query->where('ee.published=1');
        $query->orderBy('ee.id', 'DESC');

        $query->setFirstResult(0);
        $query->setMaxResults(6);

        $records = $query->loadObjectList();

        return $records;
    }

}
