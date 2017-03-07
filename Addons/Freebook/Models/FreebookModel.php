<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Addons\Freebook\Models;

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

class FreebookModel {

    public function getInfo() {
        return 'Hello World!';
    }

    public function getFreeEbooks() {

        $factory = new KazistFactory;

        $query = $factory->getQueryBuilder('#__ebooks_ebooks', 'ee');

        $query->where('ee.is_free=1');
        $query->andWhere('ee.published=1');
        $query->orderBy('ee.id ', 'DESC');

        $query->setFirstResult(0);
        $query->setMaxResults(10);

        $records = $query->loadObjectList();

        return $records;
    }

}
